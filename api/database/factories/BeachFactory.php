<?php

namespace Database\Factories;

use App\Models\Beach;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Beach>
 */
class BeachFactory extends Factory
{
    protected $model = Beach::class;

    public function definition()
    {
        return [
            'owner_id' => \App\Models\Owner::factory(), // Crea un proprietario casuale
            'name' => $this->faker->word,
            'location_id' => \App\Models\CityLocation::factory(), // Crea una posizione casuale
            'description' => $this->faker->sentence,
            'special_note' => $this->faker->text,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'allowed_animals' => $this->faker->boolean,
            'type_id' => \App\Models\BeachType::factory(), // Crea un tipo di spiaggia casuale
            'opening_date_id'=>\App\Models\OpeningDate::factory(),
        ];
    }
}
