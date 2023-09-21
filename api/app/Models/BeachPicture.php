<?php
/* The BeachPicture class is a model that represents a picture of a beach and is associated with a
Beach model. */
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
