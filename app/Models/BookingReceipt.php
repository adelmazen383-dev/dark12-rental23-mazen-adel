<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookingReceipt extends Model
{
    protected $fillable = ['booking_id', 'file_path', 'status', 'rejection_reason', 'verified_at', 'verified_by'];
    protected $casts = ['verified_at' => 'datetime'];
    public function booking() { return $this->belongsTo(Booking::class); }
    public function verifiedByUser() { return $this->belongsTo(User::class, 'verified_by'); }
}
