<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['investor_id', 'start_date', 'end_date', 'amount', 'status', 'paid_at', 'payment_reference'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'paid_at' => 'datetime', 'amount' => 'decimal:2'];
    public function investor() { return $this->belongsTo(Investor::class); }
    public function isActive(): bool { return $this->status === 'active' && $this->end_date->isFuture(); }
    public function daysRemaining(): int { return max(0, now()->diffInDays($this->end_date, false)); }
}
