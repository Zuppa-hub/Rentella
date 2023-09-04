<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BeachZone;

class BeachZoneFactory extends Factory
{
    protected $model = BeachZone::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'price_id' => \App\Models\Price::factory(),
            'description' => $this->faker->sentence,
            'special_note' => $this->faker->text,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'beach_id' => \App\Models\Beach::factory(),
        ];
    }
}
