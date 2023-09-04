<?php

namespace Database\Factories;

use App\Models\CityLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CityLocation>
 */
class CityLocationFactory extends Factory
{
    protected $model = CityLocation::class;
    public function definition(): array
    { 
        return [
            'city_name' => $this->faker->city,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'description' => $this->faker->sentence,
        ];
    }
}
