<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Authenticatable implements AuthenticatableContract
{
    use HasFactory;
    
    protected $fillable = ['name', 'surname', 'email', 'uuid', 'password', 'preferred_location_id'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the preferred location for the user.
     */
    public function preferredLocation()
    {
        return $this->belongsTo(\App\Models\CityLocation::class, 'preferred_location_id');
    }
}

