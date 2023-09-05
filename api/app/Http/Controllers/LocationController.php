<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CityLocation;

class LocationController extends Controller
{
    public function get_city()
    {
        $cityLocations = CityLocation::all();
        return response()->json($cityLocations);
    }

    public function get_cities_within_coordinates($minLatitude, $maxLatitude, $minLongitude, $maxLongitude)
    {
        $cities = CityLocation::whereBetween('latitude', [$minLatitude, $maxLatitude])
            ->whereBetween('longitude', [$minLongitude, $maxLongitude])
            ->get();

        return response()->json($cities);
    }
}
