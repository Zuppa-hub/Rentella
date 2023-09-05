<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OpeningDatesSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Beach::all()->each(function ($beach) {
            \App\Models\OpeningDate::factory()->count(1)->create(['beach_id' => $beach->id]);
        });
    }
}
