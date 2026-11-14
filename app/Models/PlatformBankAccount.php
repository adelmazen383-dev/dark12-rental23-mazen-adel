<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PlatformBankAccount extends Model
{
    protected $fillable = ['bank_name', 'account_name', 'iban', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query) { return $query->where('is_active', true); }
}
