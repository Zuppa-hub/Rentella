<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OpeningDatesSeeder extends Seeder
{
    public function run()
    {
        \App\Models\OpeningDate::factory(10)->create();
    }
}
