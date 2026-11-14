<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RentalContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'investor_id',
        'lessor_name',
        'lessor_national_id',
        'apartment_id',
        'unit_number',
        'floor_number',
        'tenant_name',
        'tenant_national_id',
        'tenant_phone',
        'start_date',
        'end_date',
        'rent_amount',
        'insurance_amount',
        'payment_type',
        'service_fee',
        'status',
        'special_terms',
        'issued_at',
        'issued_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'issued_at' => 'datetime',
        'rent_amount' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($contract) {
            if (!$contract->contract_number) {
                $contract->contract_number = 'CNT-' . date('Y') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getEffectiveLessorNameAttribute(): string
    {
        return $this->lessor_name ?: ($this->investor?->full_name ?? 'الطرف الأول');
    }

    public function getEffectiveLessorNationalIdAttribute(): string
    {
        return $this->lessor_national_id ?: ($this->investor?->id_number ?? '—');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'بانتظار الاعتماد',
            'active' => 'ساري ومعتمد',
            'completed' => 'منتهي',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'active' => 'emerald',
            'completed' => 'slate',
            'cancelled' => 'rose',
            default => 'slate',
        };
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            'daily' => 'يومي',
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'semi_annual' => 'نصف سنوي',
            'annual' => 'سنوي',
            default => $this->payment_type,
        };
    }
}
