<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(OwnersSeeder::class);
        $this->call(UsersSeeder::class);        
        $this->call(CityLocationsSeeder::class);   
        $this->call(OpeningDatesSeeder::class); 
        $this->call(BeachTypesSeeder::class);
        $this->call(BeachesSeeder::class);
        $this->call(PricesSeeder::class);        
        $this->call(BeachZonesSeeder::class);
        $this->call(UmbrellasSeeder::class);
        $this->call(OrdersSeeder::class);
        $this->call(BeachPictureSeeder::class);
    }
}
