<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\CityLocation;

class LocationController extends Controller
{
    public function index()
    {
        $cityLocations = CityLocation::all();
        return response()->json($cityLocations);
    }
    public function show($id)
    {
        // Information related to specific location 
        return response()->json(CityLocation::find($id));
    }
    public function get_cities_within_coordinates($minLatitude, $maxLatitude, $minLongitude, $maxLongitude)
    {
        $cities = CityLocation::whereBetween('latitude', [$minLatitude, $maxLatitude])
            ->whereBetween('longitude', [$minLongitude, $maxLongitude])
            ->get();

        return response()->json($cities);
    }
    public function store(LocationRequest $request)
    {
        $city = new CityLocation([
            'city_name' => $request->input('city_name'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'description' => $request->input('description'),
        ]);
        $city->save();
        return response()->json(['message' => 'City data saved successfully', $request->all()], 201);
    }
    public function update(LocationRequest $request, $id)
    {
        $city = CityLocation::findOrFail($id);
        $city = new CityLocation([
            'city_name' => $request->input('city_name'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'description' => $request->input('description'),
        ]);
        $city->save();
        return response()->json(['message' => 'City data updated successfully', $request->all()], 201);
    }
    public function destroy($id)
    {
        $city = CityLocation::findOrFail($id);
        $city->delete();
        return response()->json(['message' => 'City data removed successfully', $id]);
    }
}
