<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApartmentBlockedDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'investor_id',
        'start_date',
        'end_date',
        'reason',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    /**
     * Check if a given date range overlaps with this blocked date period
     */
    public function overlapsWith($start, $end): bool
    {
        return ($this->start_date < $end) && ($this->end_date > $start);
    }
}
