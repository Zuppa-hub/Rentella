<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BeachesTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $ownerId = DB::table('owners')->insertGetId([
            'name' => 'Jhon',
            'surname' => 'Owner',
            'email' => 'owner@example.com',
            'adress' => 'Example street 1',
            'phone_number' => '1234567890',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $locationId = DB::table('cities_location')->insertGetId([
            'city_name' => 'Rimini',
            'latitude' => 44.0582,
            'longitude' => 12.5655,
            'description' => 'test location for beaches',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $openingDateId = DB::table('opening_dates')->insertGetId([
            'start_date' => $now->toDateTimeString(),
            'end_date' => $now->addMonths(6)->toDateTimeString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $typeId = DB::table('beach_types')->insertGetId([
            'type' => 'Privata',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('beaches')->insert([
            'owner_id' => $ownerId,
            'name' => 'test beach',
            'location_id' => $locationId,
            'description' => 'test beach used for local testing',
            'opening_date_id' => $openingDateId,
            'special_note' => '',
            'latitude' => 44.05,
            'longitude' => 12.56,
            'allowed_animals' => false,
            'type_id' => $typeId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
