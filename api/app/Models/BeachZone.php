<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeachZone extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price_id', 'description', 'special_note', 'latitude', 'longitude', 'beach_id'];

    public function price()
    {
        return $this->belongsTo(Price::class);
    }

    public function beach()
    {
        return $this->belongsTo(Beach::class);
    }
    public function umbrellas()
    {
        return $this->hasMany(Umbrella::class, 'zone_id', 'id');
    }
}
