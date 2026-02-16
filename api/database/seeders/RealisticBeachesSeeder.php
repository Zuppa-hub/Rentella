<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RealisticBeachesSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Create a default owner
        $ownerId = DB::table('owners')->insertGetId([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'mario.rossi@rentella.it',
            'adress' => 'Via Roma 123, Rimini',
            'phone_number' => '+39 0541 123456',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create opening dates
        $openingDateId = DB::table('opening_dates')->insertGetId([
            'start_date' => Carbon::create(2026, 5, 1)->toDateTimeString(),
            'end_date' => Carbon::create(2026, 9, 30)->toDateTimeString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create beach type
        $typeId = DB::table('beach_types')->insertGetId([
            'type' => 'Privata',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Locations on the Adriatic Coast of Italy
        $locations = [
            [
                'city_name' => 'Rimini',
                'latitude' => 44.0678,
                'longitude' => 12.5695,
                'description' => 'La capitale della Riviera Romagnola, famosa per le sue spiagge dorate e la vivace vita notturna',
                'beaches' => [
                    ['name' => 'Bagno 26 Rimini', 'lat' => 44.0678, 'lng' => 12.5695],
                    ['name' => 'Bagno Tiki 26', 'lat' => 44.0682, 'lng' => 12.5698],
                    ['name' => 'Stabilimento Balneare Riccardino', 'lat' => 44.0675, 'lng' => 12.5692],
                    ['name' => 'Bagno 55', 'lat' => 44.0688, 'lng' => 12.5702],
                    ['name' => 'Bagno Vela', 'lat' => 44.0672, 'lng' => 12.5688],
                    ['name' => 'Bagno Libra', 'lat' => 44.0685, 'lng' => 12.5705],
                    ['name' => 'Bagno 62', 'lat' => 44.0692, 'lng' => 12.5708],
                    ['name' => 'Bagno Stefano', 'lat' => 44.0668, 'lng' => 12.5685],
                ]
            ],
            [
                'city_name' => 'Riccione',
                'latitude' => 43.9991,
                'longitude' => 12.6565,
                'description' => 'Elegante località balneare, nota per lo shopping e gli eventi estivi',
                'beaches' => [
                    ['name' => 'Bagno 61 Della Rosa', 'lat' => 43.9991, 'lng' => 12.6565],
                    ['name' => 'Bagno 81 Peppino', 'lat' => 43.9995, 'lng' => 12.6568],
                    ['name' => 'Bagno 91 Brigitte', 'lat' => 43.9988, 'lng' => 12.6562],
                    ['name' => 'Bagno 100 Riccione', 'lat' => 44.0001, 'lng' => 12.6575],
                    ['name' => 'Bagno del Sole', 'lat' => 43.9985, 'lng' => 12.6558],
                    ['name' => 'Bagno Giovanni', 'lat' => 44.0005, 'lng' => 12.6578],
                ]
            ],
            [
                'city_name' => 'Cesenatico',
                'latitude' => 44.1973,
                'longitude' => 12.4032,
                'description' => 'Pittoresco porto canale progettato da Leonardo da Vinci',
                'beaches' => [
                    ['name' => 'Bagno Adriatico', 'lat' => 44.1973, 'lng' => 12.4032],
                    ['name' => 'Bagno Milano', 'lat' => 44.1978, 'lng' => 12.4038],
                    ['name' => 'Bagno Marconi', 'lat' => 44.1968, 'lng' => 12.4028],
                    ['name' => 'Bagno Lido', 'lat' => 44.1985, 'lng' => 12.4042],
                    ['name' => 'Bagno Smeraldo', 'lat' => 44.1965, 'lng' => 12.4025],
                ]
            ],
            [
                'city_name' => 'Cervia',
                'latitude' => 44.2608,
                'longitude' => 12.3476,
                'description' => 'Città del sale e delle spiagge tranquille, ideale per famiglie',
                'beaches' => [
                    ['name' => 'Bagno Andreucci', 'lat' => 44.2608, 'lng' => 12.3476],
                    ['name' => 'Bagno Pineta', 'lat' => 44.2612, 'lng' => 12.3480],
                    ['name' => 'Bagno Delfino', 'lat' => 44.2605, 'lng' => 12.3472],
                    ['name' => 'Bagno Tropical', 'lat' => 44.2618, 'lng' => 12.3485],
                    ['name' => 'Bagno Romagna', 'lat' => 44.2602, 'lng' => 12.3468],
                    ['name' => 'Bagno Fantini', 'lat' => 44.2625, 'lng' => 12.3492],
                ]
            ],
            [
                'city_name' => 'Milano Marittima',
                'latitude' => 44.2856,
                'longitude' => 12.3505,
                'description' => 'Località chic con pineta secolare e spiagge esclusive',
                'beaches' => [
                    ['name' => 'Papeete Beach', 'lat' => 44.2856, 'lng' => 12.3505],
                    ['name' => 'Bagno Sirena', 'lat' => 44.2862, 'lng' => 12.3512],
                    ['name' => 'Bagno Holiday Village', 'lat' => 44.2852, 'lng' => 12.3502],
                    ['name' => 'Bagno Renzo', 'lat' => 44.2868, 'lng' => 12.3518],
                ]
            ],
            [
                'city_name' => 'Cattolica',
                'latitude' => 43.9625,
                'longitude' => 12.7396,
                'description' => 'Accogliente località balneare con celebre acquario',
                'beaches' => [
                    ['name' => 'Bagno Dario', 'lat' => 43.9625, 'lng' => 12.7396],
                    ['name' => 'Bagno Stella Marina', 'lat' => 43.9628, 'lng' => 12.7398],
                    ['name' => 'Bagno Nettuno', 'lat' => 43.9622, 'lng' => 12.7392],
                    ['name' => 'Bagno Claudio', 'lat' => 43.9632, 'lng' => 12.7402],
                    ['name' => 'Bagno Roberto', 'lat' => 43.9618, 'lng' => 12.7388],
                ]
            ],
            [
                'city_name' => 'Bellaria-Igea Marina',
                'latitude' => 44.1422,
                'longitude' => 12.4688,
                'description' => 'Due anime, un\'unica spiaggia: tradizione e accoglienza',
                'beaches' => [
                    ['name' => 'Bagno Azzurro', 'lat' => 44.1422, 'lng' => 12.4688],
                    ['name' => 'Bagno Igea', 'lat' => 44.1428, 'lng' => 12.4692],
                    ['name' => 'Bagno Venere', 'lat' => 44.1418, 'lng' => 12.4685],
                    ['name' => 'Bagno Peter Pan', 'lat' => 44.1435, 'lng' => 12.4698],
                ]
            ],
        ];

        // Insert locations and beaches
        foreach ($locations as $locationData) {
            $locationId = DB::table('cities_location')->insertGetId([
                'city_name' => $locationData['city_name'],
                'latitude' => $locationData['latitude'],
                'longitude' => $locationData['longitude'],
                'description' => $locationData['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($locationData['beaches'] as $index => $beachData) {
                $latJitter = (($index % 3) - 1) * 0.00025 + (mt_rand(-8, 8) / 100000);
                $lngJitter = ((($index + 1) % 3) - 1) * 0.00025 + (mt_rand(-8, 8) / 100000);
                $beachLat = $beachData['lat'] + $latJitter;
                $beachLng = $beachData['lng'] + $lngJitter;

                // Random prices between 10€ and 40€
                $minPrice = rand(10, 25);
                $maxPrice = rand($minPrice + 5, 40);

                $beachId = DB::table('beaches')->insertGetId([
                    'owner_id' => $ownerId,
                    'name' => $beachData['name'],
                    'location_id' => $locationId,
                    'description' => 'Stabilimento balneare attrezzato con servizi moderni e accoglienza familiare',
                    'opening_date_id' => $openingDateId,
                    'special_note' => '',
                    'latitude' => $beachLat,
                    'longitude' => $beachLng,
                    'allowed_animals' => rand(0, 1), // 0 = no, 1 = yes
                    'type_id' => $typeId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Create price first
                $priceId = DB::table('prices')->insertGetId([
                    'price' => $minPrice,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Then create zone with the price_id
                $zoneId = DB::table('beach_zones')->insertGetId([
                    'name' => 'Zona Standard',
                    'description' => 'Zona standard con ombrelloni e lettini',
                    'special_note' => '',
                    'latitude' => $beachLat,
                    'longitude' => $beachLng,
                    'beach_id' => $beachId,
                    'price_id' => $priceId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // If there's a price range, create a premium zone
                if ($maxPrice > $minPrice + 5) {
                    $premiumPriceId = DB::table('prices')->insertGetId([
                        'price' => $maxPrice,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $premiumZoneId = DB::table('beach_zones')->insertGetId([
                        'name' => 'Zona Premium',
                        'description' => 'Zona premium con servizi esclusivi',
                        'special_note' => '',
                        'latitude' => $beachLat,
                        'longitude' => $beachLng,
                        'beach_id' => $beachId,
                        'price_id' => $premiumPriceId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // Add random number of umbrellas (20-80) to the standard zone
                $totalUmbrellas = rand(20, 80);
                for ($i = 1; $i <= $totalUmbrellas; $i++) {
                    DB::table('umbrellas')->insert([
                        'zone_id' => $zoneId,
                        'number' => $i,
                        'special_note' => '',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        echo "\n✅ Created " . count($locations) . " locations with realistic beaches along the Adriatic Coast\n";
    }
}
