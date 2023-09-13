<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeachRequest;
use App\Models\Beach;
use App\Models\BeachZone;

use Illuminate\Http\Request;

class BeachController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('cityId')) { //if the request has the city id parameters it only shows the beach of this city
            $beaches = Beach::where('location_id', $request->input('cityId'))->get(); //get the beaches of this city

            $results = $beaches->transform(function ($beach) {  //get maximum and minimum price of a beach 
                $zonePrices = BeachZone::whereIn('beach_id', [$beach->id])
                    ->join('prices', 'beach_zones.price_id', '=', 'prices.id')
                    ->selectRaw('MIN(prices.price) as min_price, MAX(prices.price) as max_price')
                    ->first();

                $zones = BeachZone::whereIn('beach_id', [$beach->id])
                    ->join('prices', 'beach_zones.price_id', '=', 'prices.id')
                    ->withCount('umbrellas')
                    ->selectRaw('name, description, special_note, latitude, longitude, prices.price')
                    ->get();

                $beach->allowed_animals = ($beach->allowed_animals == 1) ? 'yes' : 'no';

                return [
                    'beach' => $beach,
                    'beach_min_price' => $zonePrices->min_price,
                    'beach_max_price' => $zonePrices->max_price,
                    'zones' => $zones,
                ];
            });

            return response()->json($results);
        }

        return response()->json(Beach::all());
    }


    public function show($id)
    {
        $beach = Beach::find($id);

        if (!$beach) {
            return response()->json(['message' => 'Beach not found'], 404);
        }

        return response()->json($beach);
    }

    public function store(BeachRequest $request)
    {
        $beach = Beach::create($request->all());
        return response()->json(['message' => 'Beach data saved successfully', 'data' => $beach], 201);
    }

    public function update(BeachRequest $request, $id)
    {
        $beach = Beach::find($id);

        if (!$beach) {
            return response()->json(['message' => 'Beach not found'], 404);
        }

        $beach->update($request->all());
        return response()->json(['message' => 'Beach data updated successfully', 'data' => $beach], 200);
    }

    public function destroy($id)
    {
        $beach = Beach::find($id);

        if (!$beach) {
            return response()->json(['message' => 'Beach not found'], 404);
        }

        $beach->delete();
        return response()->json(['message' => 'Beach data removed successfully', 'id' => $id]);
    }
}
