<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'full_name', 'id_number', 'id_image', 'phone', 'whatsapp',
        'bank_name', 'bank_account_name', 'iban',
        'license_number', 'license_name', 'license_image',
        'status', 'admin_approved_override', 'rejection_reason', 'suspension_reason', 'suspension_date', 'approved_at',
    ];

    protected $casts = [
        'suspension_date' => 'datetime',
        'approved_at' => 'datetime',
        'admin_approved_override' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function apartments() { return $this->hasMany(Apartment::class); }
    public function subscriptions() { return $this->hasMany(Subscription::class); }
    public function wallet() { return $this->hasOne(Wallet::class); }
    public function rentalContracts() { return $this->hasMany(RentalContract::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function conversations() { return $this->hasMany(Conversation::class); }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->where('end_date', '>=', now()->toDateString());
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->exists();
    }

    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isPending(): bool { return $this->status === 'pending'; }
    public function isSuspended(): bool { return $this->status === 'suspended'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    /**
     * Check if this investor's apartments are allowed to be displayed to clients
     * Rule: Suspended or subscription expired apartments do NOT show unless Admin approved.
     */
    public function isAllowedToDisplayApartments(): bool
    {
        // 1. If Admin gave explicit override approval, show them!
        if ($this->admin_approved_override) {
            return true;
        }

        // 2. If suspended or rejected or pending, hide!
        if ($this->status !== 'approved') {
            return false;
        }

        // 3. If subscription is expired or inactive, hide!
        if (!$this->hasActiveSubscription()) {
            return false;
        }

        return true;
    }

    public function canReceiveBookings(): bool
    {
        if ($this->admin_approved_override) {
            return true;
        }

        return $this->isApproved()
            && $this->hasActiveSubscription()
            && $this->wallet && $this->wallet->balance > 0;
    }
}
