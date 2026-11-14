<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['wallet_id', 'type', 'amount', 'description', 'booking_id', 'balance_after', 'created_by'];
    protected $casts = ['amount' => 'decimal:2', 'balance_after' => 'decimal:2'];
    public function wallet() { return $this->belongsTo(Wallet::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'إيداع',
            'deduction' => 'خصم',
            'refund' => 'استرداد',
            default => $this->type,
        };
    }
}
