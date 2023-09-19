<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeachRequest;
use App\Http\Requests\BeachFilterRequest;
use App\Models\Beach;

class BeachController extends Controller
{
    public function index(BeachFilterRequest $request)
    {
        $beachesQuery = Beach::query();

        foreach ($request->all() as $param => $value) {
            switch ($param) {
                case 'cityId':
                    $beachesQuery->where('location_id', $value);
                    break;
                case 'allowed_animals':
                    $value = ($value === 'yes') ? 1 : 0;
                    $beachesQuery->where('allowed_animals', $value);
                    break;
            }
        }

        $beaches = $beachesQuery->with([
            'zones.prices',
            'zones' => function ($query) {
                $query->withCount('umbrellas');
            },
        ])->get();

        $results = $beaches->transform(function ($beach) {
            $minPrice = $beach->zones->pluck('prices.price')->flatten()->min();
            $maxPrice = $beach->zones->pluck('prices.price')->flatten()->max();

            $beach->allowed_animals = ($beach->allowed_animals == 1) ? 'yes' : 'no';

            return [
                'beach' => $beach,
                'beach_min_price' => $minPrice,
                'beach_max_price' => $maxPrice,
                'total_umbrellas' => $beach->zones->sum('umbrellas_count'),
            ];
        });

        // Rimove umbrellas relation to avoid umbrellas list 
        $results->transform(function ($result) {
            return $result;
        });

        // Restituisce le spiagge filtrate
        return response()->json($results);
    }



    public function show($id)
    {
        return response()->json(Beach::findOrfail($id));
    }

    public function store(BeachRequest $request)
    {
        return response()->json(Beach::create($request->all()), 201);
    }

    public function update(BeachRequest $request, $id)
    {
        return response()->json(Beach::findOrFail($id)
            ->update($request->all()), 200);
    }

    public function destroy($id)
    {
        return response()->json(Beach::findOrFail($id)->delete());
    }
}
