<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BeachPictureSeeder extends Seeder
{
    public function run()
    {
        \App\Models\BeachPicture::factory(10)->create();
    }
}
