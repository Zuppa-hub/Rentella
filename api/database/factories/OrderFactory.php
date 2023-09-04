<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'umbrella_id' => \App\Models\Umbrella::factory(),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 day', '+3 months'),
            'user_id' => \App\Models\User::factory(),
            'price_id' => \App\Models\Price::factory(),
        ];
    }
}
