<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OwnersSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Owner::factory(10)->create();
    }
}
