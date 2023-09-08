<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\CityLocation;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        // if request has location paramenters 
        if ($request->has(['minLatitude', 'maxLatitude', 'minLongitude', 'maxLongitude'])) {
            $minLatitude = $request->input('minLatitude');
            $maxLatitude = $request->input('maxLatitude');
            $minLongitude = $request->input('minLongitude');
            $maxLongitude = $request->input('maxLongitude');

            $cities = CityLocation::whereBetween('latitude', [$minLatitude, $maxLatitude])
                ->whereBetween('longitude', [$minLongitude, $maxLongitude])
                ->get();

            return response()->json($cities);
        }
        // if no return all the cities 
        $cityLocations = CityLocation::all();
        return response()->json($cityLocations);
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
