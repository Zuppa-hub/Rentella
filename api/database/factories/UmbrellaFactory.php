<?php

namespace Database\Factories;

use App\Models\Umbrella;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Umbrella>
 */
class UmbrellaFactory extends Factory
{
    protected $model = Umbrella::class;
    
    public function definition()
    {
        return [
            'zone_id' => \App\Models\BeachZone::factory(), 
            'number'=>$this->faker->randomNumber(1,51), 
            'special_note'=>$this->faker->sentence,
        ];
    }
}
