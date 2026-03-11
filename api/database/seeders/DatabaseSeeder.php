<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Use the new realistic beaches seeder with Italian coastal locations
        $this->call(RealisticBeachesSeeder::class);
        
        // Keep these for pictures if needed
        // $this->call(BeachPictureSeeder::class);
    }
}
