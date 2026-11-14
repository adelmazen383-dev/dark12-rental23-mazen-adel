<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApartmentMedia extends Model
{
    protected $table = 'apartment_media';
    protected $fillable = ['apartment_id', 'type', 'file_path', 'sort_order'];
    public function apartment() { return $this->belongsTo(Apartment::class); }
}
