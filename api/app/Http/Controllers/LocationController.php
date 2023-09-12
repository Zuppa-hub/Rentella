<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationFilterRequest;
use App\Http\Requests\LocationRequest;
use App\Models\Beach;
use App\Models\BeachZone;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\CityLocation;
use App\Models\Price;

class LocationController extends Controller
{
    public function index(LocationFilterRequest $request)
    {
        // if request has location paramenters 
        if ($request->has(['minLatitude', 'maxLatitude', 'minLongitude', 'maxLongitude', 'myLatitude', 'myLongitude'])) {
            // get the city details 
            $cities = CityLocation::whereBetween(
                'latitude',
                [$request->input('minLatitude'), $request->input('maxLatitude')]
            )
                ->whereBetween(
                    'longitude',
                    [$request->input('minLongitude'), $request->input('maxLongitude')]
                )
                ->get();
            //array where store result of the price and city 
            $results = [];
            //search max and min prices for each cities 
            foreach ($cities as $city) {
                $cityBeaches = Beach::where('location_id', $city->id)->pluck('id');
                $zones = BeachZone::whereIn('beach_id', $cityBeaches)
                    ->selectRaw('MIN(prices.price) as min_price, MAX(prices.price) as max_price')
                    ->join('prices', 'beach_zones.price_id', '=', 'prices.id')
                    ->get();
                $results[] = [
                    'city' => $city,
                    'min_price' => $zones->min('min_price'),
                    'max_price' => $zones->max('max_price'),
                    'beach_count' => $cityBeaches->count(), //number of the beach for each city
                    'distance' => $this->calculateDistance($city->latitude, $city->longitude, $request->input('myLatitude'), $request->input('myLongitude')) . ' km',
                ];
            }
            return response()->json($results);
        }
        // if no return all the cities 
        return response()->json(
            CityLocation::all()
        );
    }
    //calculate distance between your position and beach location
    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // hearh radius 
        $earthRadius = 6371;

        // difference bethween the two points and conversion from degrees to radians 
        $latDiff = deg2rad($lat2) - deg2rad($lat1);
        $lonDiff = deg2rad($lon2) - deg2rad($lon1);

        // distance using the Heversine formula 
        $a = sin($latDiff / 2) ** 2 + cos($lat1) * cos($lat2) * sin($lonDiff / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return number_format($earthRadius * $c, 2); //formatted distance using 2 decimal numbers
    }
    public function show($id)
    {
        // Information related to specific location 
        return response()->json(CityLocation::find($id));
    }
    public function store(LocationRequest $request)
    {
        $city = CityLocation::create($request->all());
        return response()->json(['message' => 'City data saved successfully', 'data' => $city], 201);
    }
    public function update(LocationRequest $request, $id)
    {
        $city = CityLocation::findOrFail($id);
        $city->update($request->all());
        return response()->json(['message' => 'City data updated successfully', 'data' => CityLocation::findOrFail($id)], 200);
    }
    public function destroy($id)
    {
        $city = CityLocation::findOrFail($id);
        $city->delete();
        return response()->json(['message' => 'City data removed successfully', 'id' => $id]);
    }
}
