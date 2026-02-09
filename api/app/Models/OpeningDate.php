<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpeningDate extends Model
{
    use HasFactory;
    protected $fillable = ['start_date', 'end_date'];

    public function beaches()
    {
        return $this->hasMany(Beach::class, 'opening_date_id');
    }
}
