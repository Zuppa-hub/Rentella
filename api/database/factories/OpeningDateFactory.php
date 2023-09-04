<?php

namespace Database\Factories;

use App\Models\OpeningDate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpeningDate>
 */
class OpeningDateFactory extends Factory
{
    protected $model = OpeningDate::class;
    public function definition(): array
    {
        return [
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 day', '+3 months'),
            'beach_id' => \App\Models\Beach::factory(),
        ];
    }
}
