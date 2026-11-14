<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'reference_number', 'reception_code', 'apartment_id', 'customer_name', 'customer_phone',
        'checkin_date', 'checkout_date', 'nights_count', 'daily_rate',
        'insurance_amount', 'extra_fees', 'total_amount', 'status',
        'notes', 'cancelled_at', 'cancellation_reason', 'confirmed_at', 'confirmed_by',
        'reception_checked_in_at', 'reception_checked_out_at', 'checked_in_by', 'is_unregistered_deal', 'reception_notes',
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'checkout_date' => 'date',
        'daily_rate' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'extra_fees' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'reception_checked_in_at' => 'datetime',
        'reception_checked_out_at' => 'datetime',
        'is_unregistered_deal' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (!$booking->reference_number) {
                $booking->reference_number = 'BK-' . strtoupper(Str::random(8));
            }
            if (!$booking->reception_code) {
                $booking->reception_code = 'RCP-' . strtoupper(Str::random(6));
            }
        });
    }

    public function apartment() { return $this->belongsTo(Apartment::class); }
    public function receipts() { return $this->hasMany(BookingReceipt::class); }
    public function confirmedByUser() { return $this->belongsTo(User::class, 'confirmed_by'); }
    public function checkedInByUser() { return $this->belongsTo(User::class, 'checked_in_by'); }

    public function isReceptionCheckedIn(): bool
    {
        return !is_null($this->reception_checked_in_at);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_selection' => 'بانتظار التأكيد',
            'pending_payment' => 'بانتظار الدفع',
            'pending_verification' => 'بانتظار التحقق',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغي',
            'completed' => 'مكتمل',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending_selection' => 'yellow',
            'pending_payment' => 'orange',
            'pending_verification' => 'blue',
            'confirmed' => 'green',
            'cancelled' => 'red',
            'completed' => 'gray',
            default => 'gray',
        };
    }
}
