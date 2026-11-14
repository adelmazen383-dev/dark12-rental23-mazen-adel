<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = ['type', 'recipient_phone', 'recipient_type', 'message', 'status', 'retry_count', 'related_type', 'related_id', 'sent_at', 'error_message'];
    protected $casts = ['sent_at' => 'datetime'];
}
