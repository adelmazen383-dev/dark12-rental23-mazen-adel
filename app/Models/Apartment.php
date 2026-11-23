<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id', 'apartment_number', 'floor_number', 'description',
        'rooms_count', 'living_rooms_count', 'bathrooms_count',
        'view_type', 'features', 'daily_price', 'insurance_amount',
        'extra_fees', 'extra_fees_description', 'checkin_time', 'checkout_time',
        'is_available', 'is_active', 'admin_approved_override', 'is_featured_on_homepage', 'homepage_order',
    ];

    protected $casts = [
        'daily_price' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'extra_fees' => 'decimal:2',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'admin_approved_override' => 'boolean',
        'is_featured_on_homepage' => 'boolean',
        'homepage_order' => 'integer',
    ];

    public function investor() { return $this->belongsTo(Investor::class); }
    public function media() { return $this->hasMany(ApartmentMedia::class)->orderBy('sort_order'); }
    public function images() { return $this->hasMany(ApartmentMedia::class)->where('type', 'image')->orderBy('sort_order'); }
    public function videos() { return $this->hasMany(ApartmentMedia::class)->where('type', 'video')->orderBy('sort_order'); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function inspectionRequests() { return $this->hasMany(InspectionRequest::class); }
    public function conversations() { return $this->hasMany(Conversation::class); }
    public function rentalContracts() { return $this->hasMany(RentalContract::class); }
    public function blockedDates() { return $this->hasMany(ApartmentBlockedDate::class); }

    /**
     * Check if apartment is currently rented out (occupied today) or blocked
     */
    public function isCurrentlyRented(): bool
    {
        $today = now()->toDateString();

        $hasConfirmedBooking = $this->bookings()
            ->where('status', 'confirmed')
            ->whereDate('checkin_date', '<=', $today)
            ->whereDate('checkout_date', '>', $today)
            ->exists();

        if ($hasConfirmedBooking) {
            return true;
        }

        $hasActiveContract = $this->rentalContracts()
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();

        if ($hasActiveContract) {
            return true;
        }

        // Check if investor blocked this unit today
        return $this->blockedDates()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();
    }

    /**
     * Check if apartment is available to be shown and booked by clients
     */
    public function isAvailableForClients(): bool
    {
        // 1. Must be active and marked available
        if (!$this->is_active || !$this->is_available) {
            return false;
        }

        // 2. Must not be currently rented out
        if ($this->isCurrentlyRented()) {
            return false;
        }

        // 3. Admin direct override for this unit
        if ($this->admin_approved_override) {
            return true;
        }

        // 4. Investor status & subscription check
        return $this->investor && $this->investor->isAllowedToDisplayApartments();
    }

    /**
     * Check if apartment has bookings or contracts for specific date range
     */
    public function isBookedForDates($checkin, $checkout): bool
    {
        $hasBooking = $this->bookings()
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($checkin, $checkout) {
                $q->whereDate('checkin_date', '<', $checkout)
                  ->whereDate('checkout_date', '>', $checkin);
            })->exists();

        if ($hasBooking) {
            return true;
        }

        $hasContract = $this->rentalContracts()
            ->where('status', 'active')
            ->where(function ($q) use ($checkin, $checkout) {
                $q->whereDate('start_date', '<', $checkout)
                  ->whereDate('end_date', '>', $checkin);
            })->exists();

        if ($hasContract) {
            return true;
        }

        // Check if investor blocked the apartment for these dates
        return $this->blockedDates()
            ->where(function ($q) use ($checkin, $checkout) {
                $q->whereDate('start_date', '<', $checkout)
                  ->whereDate('end_date', '>', $checkin);
            })->exists();
    }

    /**
     * Scope to only query apartments visible to clients according to all platform business rules:
     * - Must be active and available
     * - Must NOT be currently rented (confirmed booking, active contract, or investor blocked date)
     * - Investor must be approved and have active subscription, UNLESS Admin approved an override
     */
    public function scopeVisibleToClients($query)
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function ($b) use ($today) {
                $b->where('status', 'confirmed')
                  ->whereDate('checkin_date', '<=', $today)
                  ->whereDate('checkout_date', '>', $today);
            })
            ->whereDoesntHave('rentalContracts', function ($c) use ($today) {
                $c->where('status', 'active')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            })
            ->whereDoesntHave('blockedDates', function ($bd) use ($today) {
                $bd->whereDate('start_date', '<=', $today)
                   ->whereDate('end_date', '>=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->where('admin_approved_override', true)
                  ->orWhereHas('investor', function ($inv) use ($today) {
                      $inv->where(function ($invQ) use ($today) {
                          $invQ->where('admin_approved_override', true)
                               ->orWhere(function ($validInv) use ($today) {
                                   $validInv->where('status', 'approved')
                                            ->whereHas('wallet', function ($w) {
                                                $w->where('balance', '>', 0);
                                            })
                                            ->whereHas('subscriptions', function ($sub) use ($today) {
                                                $sub->where('status', 'active')
                                                    ->whereDate('end_date', '>=', $today);
                                            });
                               });
                      });
                  });
            });
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('is_active', true);
    }

    public function scopeByFloor($query, $floor)
    {
        return $query->where('floor_number', $floor);
    }
}
