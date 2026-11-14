<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'booking_id',
        'investor_id',
        'customer_name',
        'customer_phone',
        'customer_token',
        'subject',
        'last_message_at',
        'status',
        'deal_status',
        'offer_price',
        'offer_checkin_date',
        'offer_checkout_date',
        'offer_notes',
        'has_bypass_attempt',
        'bypass_attempt_reason',
        'client_survey_response',
        'investor_survey_response',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'offer_price' => 'decimal:2',
        'offer_checkin_date' => 'date',
        'offer_checkout_date' => 'date',
        'has_bypass_attempt' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($conversation) {
            if (!$conversation->customer_token) {
                $conversation->customer_token = Str::random(40);
            }
        });
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->oldest();
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadMessagesForInvestorCount(): int
    {
        return $this->messages()->where('sender_type', 'customer')->where('is_read_by_investor', false)->count();
    }

    public function unreadMessagesForCustomerCount(): int
    {
        return $this->messages()->whereIn('sender_type', ['investor', 'admin'])->where('is_read_by_customer', false)->count();
    }

    public function unreadMessagesForAdminCount(): int
    {
        return $this->messages()->where('is_read_by_admin', false)->count();
    }
}
