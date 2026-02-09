<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;
    protected $fillable = ['price'];

    public function zones()
    {
        return $this->hasMany(BeachZone::class, 'price_id');
    }
}
