<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Authenticatable implements AuthenticatableContract
{
    // Aggiungi eventuali campi aggiuntivi necessari
    use HasFactory;
    protected $fillable = ['name', 'surname', 'email', 'uuid', 'password'];
}

