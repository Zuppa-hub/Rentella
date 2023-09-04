<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CityLocation extends Model
{
    use HasFactory;
    protected $table = 'cities_location';
    protected $fillable = ['city_name', 'latitude', 'longitude', 'description'];
}
