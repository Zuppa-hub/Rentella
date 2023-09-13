<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['umbrella_id', 'start_date', 'end_date', 'user_id', 'price_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function price()
    {
        return $this->belongsTo(Price::class, 'price_id', 'id');
    }

    public function umbrella()
    {
        return $this->belongsTo(Umbrella::class, 'umbrella_id', 'id');
    }
}
