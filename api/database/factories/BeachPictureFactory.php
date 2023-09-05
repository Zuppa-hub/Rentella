<?php

namespace Database\Factories;

use App\Models\BeachPicture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeachPicture>
 */
class BeachPictureFactory extends Factory
{
    protected $model = BeachPicture::class;
    public function definition(): array
    {
        return [
            'beach_id' => \App\Models\Beach::factory(), // Crea un proprietario casuale
            'photo' => $this->faker->url(),
        ];
    }
}
