<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'sender_name',
        'message',
        'is_read_by_customer',
        'is_read_by_investor',
        'is_read_by_admin',
        'attachment_path',
    ];

    protected $casts = [
        'is_read_by_customer' => 'boolean',
        'is_read_by_investor' => 'boolean',
        'is_read_by_admin' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isFromCustomer(): bool
    {
        return $this->sender_type === 'customer';
    }

    public function isFromInvestor(): bool
    {
        return $this->sender_type === 'investor';
    }

    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }
}
