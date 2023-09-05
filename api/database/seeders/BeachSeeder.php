<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Beach;
use App\Models\BeachType;

class BeachSeeder extends Seeder
{
    public function run()
    {
        $beachTypes = BeachType::all();

        Beach::factory(10)->create([
            'type_id' => $beachTypes->random()->id,
        ]);
    }
}
