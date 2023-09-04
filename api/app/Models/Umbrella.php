<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Umbrella extends Model
{
    use HasFactory;
    protected $fillable = ['zone_id', 'number', 'special_note'];

    public function zone()
    {
        return $this->belongsTo(BeachZone::class, 'zone_id');
    }
}
