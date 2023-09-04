<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BeachType;

class BeachTypeFactory extends Factory
{
    protected $model = BeachType::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['stone', 'sand', 'mix']),
        ];
    }
}
