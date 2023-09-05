<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OwnersSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Beach::all()->each(function ($beach) {
            \App\Models\Owner::factory()->count(1)->create(['beach_id' => $beach->id]);
        });
    }
}
