<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PricesSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Price::factory(10)->create();
    }
}
