<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UmbrellasSeeder extends Seeder
{
    public function run()
    {
        \App\Models\BeachZone::all()->each(function ($beachZone) {
            \App\Models\Umbrella::factory()->count(random_int(10,50))->create(['zone_id' => $beachZone->id]);
        });
    }
}
