<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'investor_id',
        'booking_id',
        'rental_contract_id',
        'client_name',
        'client_phone',
        'client_tax_number',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'service_fee',
        'status',
        'items',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'items' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = 'INV-' . date('Y') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function contract()
    {
        return $this->belongsTo(RentalContract::class, 'rental_contract_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'مسودة',
            'issued' => 'صادرة',
            'paid' => 'مدفوعة ومسددة',
            'cancelled' => 'ملغاة',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'slate',
            'issued' => 'blue',
            'paid' => 'emerald',
            'cancelled' => 'rose',
            default => 'slate',
        };
    }
}
