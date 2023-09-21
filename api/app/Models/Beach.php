<?php
/* The Beach class is a model in a PHP Laravel application that represents a beach and its associated
properties. */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beach extends Model
{
    use HasFactory;
    protected $fillable = [
        /* The 'owner_id' field in the  array is specifying that the 'owner_id' column in the
        'beaches' table can be mass assigned. This means that when creating or updating a Beach
        model, you can pass an array with the 'owner_id' key to set the value of the 'owner_id'
        column. */
        'owner_id',
        'name',
        'location_id',
        'description',
        'opening_date_id',
        'special_note',
        'latitude',
        'longitude',
        'allowed_animals',
        'type_id',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id');
    }
    public function zones()
    {
        return $this->hasMany(BeachZone::class, 'beach_id');
    }
    public function location()
    {
        return $this->belongsTo(CityLocation::class, 'location_id');
    }

    public function openingDate()
    {
        return $this->belongsTo(OpeningDate::class, 'opening_date_id');
    }

    public function beachType()
    {
        return $this->belongsTo(BeachType::class, 'type_id');
    }
}
