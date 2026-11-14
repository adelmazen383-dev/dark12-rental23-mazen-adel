<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InspectionRequest extends Model
{
    protected $fillable = ['apartment_id', 'customer_name', 'customer_phone', 'status', 'notes', 'scheduled_at'];
    protected $casts = ['scheduled_at' => 'datetime'];
    public function apartment() { return $this->belongsTo(Apartment::class); }
}
