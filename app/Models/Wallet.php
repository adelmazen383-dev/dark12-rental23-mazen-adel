<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['investor_id', 'balance'];
    protected $casts = ['balance' => 'decimal:2'];
    public function investor() { return $this->belongsTo(Investor::class); }
    public function transactions() { return $this->hasMany(WalletTransaction::class)->latest(); }
}
