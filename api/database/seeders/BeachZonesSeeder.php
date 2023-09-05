<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BeachZonesSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Beach::all()->each(function ($beach) {
            \App\Models\BeachZone::factory()->count(random_int(2, 10))->create(['beach_id' => $beach->id]);
        });
    }
}
