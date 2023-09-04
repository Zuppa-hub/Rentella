<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BeachZonesSeeder extends Seeder
{
    public function run()
    {
        \App\Models\BeachZone::factory(10)->create();
    }
}
