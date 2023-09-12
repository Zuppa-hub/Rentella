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
        if ($request->has(['minLatitude', 'maxLatitude', 'minLongitude', 'maxLongitude'])) {
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
                ];
            }
            return response()->json($results);
        }
        // if no return all the cities 
        return response()->json(
            CityLocation::all()
        );
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
