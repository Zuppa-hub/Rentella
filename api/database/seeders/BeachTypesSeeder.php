<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BeachTypesSeeder extends Seeder
{
    public function run()
    {
        \App\Models\BeachType::factory(3)->create();
    }
}
