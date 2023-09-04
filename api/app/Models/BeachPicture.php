<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeachPicture extends Model
{
    use HasFactory;
    protected $fillable = ['photo', 'beach_id'];

    public function beach()
    {
        return $this->belongsTo(Beach::class);
    }
}
