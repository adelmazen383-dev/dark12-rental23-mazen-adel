<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'investor_id',
        'subscriber_type',
        'customer_phone',
        'conversation_token',
        'booking_reference',
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
        'device_label',
        'last_notified_at',
    ];

    protected $casts = [
        'last_notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
