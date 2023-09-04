<?php
namespace Database\Seeders;

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class UmbrellasSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Umbrella::factory(10)->create();
    }
}
