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
        $filterParams = ['minLatitude', 'maxLatitude', 'minLongitude', 'maxLongitude', 'myLatitude', 'myLongitude'];

        // initialize array of parameters
        $params = [];

        foreach ($filterParams as $param) {
            if ($request->has($param)) {
                // add parameters to array
                $params[$param] = $request->input($param);
            }
        }

        // check if array is empty 
        if (!empty($params)) {
            // search for near cities 
            $cities = CityLocation::whereBetween('latitude', [$params['minLatitude'], $params['maxLatitude']])
                ->whereBetween('longitude', [$params['minLongitude'], $params['maxLongitude']])
                ->get();
            // obtain beach and zone 
            $results = $cities->map(function ($city) use ($params) {
                $cityBeaches = Beach::where('location_id', $city->id)->pluck('id');
                $zones = BeachZone::whereIn('beach_id', $cityBeaches)->get();
                return [
                    'id' => $city->id,
                    'name' => $city->city_name,
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'description' => $city->description,
                    'min_price' =>  $zones->min('prices.price'),
                    'max_price' => $zones->max('prices.price'),
                    'beach_count' => $cityBeaches->count(),
                    'distance' => $this->calculateDistance($city->latitude, $city->longitude, $params['myLatitude'], $params['myLongitude']) . ' km',
                ];
            });

            // return data as json
            return response()->json($results);
        }

        // return all cities if not parameter found 
        return response()->json(CityLocation::all());
    }

    //calculate distance between your position and beach location
    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // hearh radius 
        $earthRadius = 6371;

        // difference bethween the two points and conversion from degrees to radians 
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $lon1Rad = deg2rad($lon1);
        $lon2Rad = deg2rad($lon2);
        $latDiff = $lat2Rad - $lat1Rad;
        $lonDiff = $lon2Rad - $lon1Rad;

        // distance using the Heversine formula 
        $a = sin($latDiff / 2) ** 2 + cos($lat1Rad) * cos($lat2Rad) * sin($lonDiff / 2) ** 2;
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
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden: Admin only'], 403);
        }
        return response()->json(CityLocation::create($request->validated()), 201);
    }
    public function update(LocationRequest $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden: Admin only'], 403);
        }
        $location = CityLocation::findOrFail($id);
        $location->update($request->validated());
        return response()->json($location, 200);
    }
    public function destroy($id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden: Admin only'], 403);
        }
        $location = CityLocation::findOrFail($id);
        $location->delete();
        return response()->json(null, 204);
    }
}
