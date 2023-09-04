<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CityLocationsSeeder extends Seeder
{
    public function run()
    {
        \App\Models\CityLocation::factory(10)->create();
    }
}
