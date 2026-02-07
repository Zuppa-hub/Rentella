<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeachRequest;
use App\Http\Requests\UpdateBeachRequest;
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
        $beach = Beach::with(['owner', 'location', 'openingDate', 'beachType', 'zones.prices'])
            ->findOrFail($id);
        return response()->json($beach);
    }

    public function store(StoreBeachRequest $request)
    {
        $beach = Beach::create($request->validated());
        return response()->json($beach->load(['owner', 'location', 'openingDate', 'beachType']), 201);
    }

    public function update(UpdateBeachRequest $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $beach = Beach::findOrFail($id);
        
        // Check if the authenticated user is the beach owner
        $owner = $beach->owner;
        if (!$owner || $owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden: You can only edit beaches you own'], 403);
        }
        
        $beach->update($request->validated());
        return response()->json($beach->load(['owner', 'location', 'openingDate', 'beachType']), 200);
    }

    public function destroy($id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $beach = Beach::findOrFail($id);
        
        // Check if the authenticated user is the beach owner
        $owner = $beach->owner;
        if (!$owner || $owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden: You can only delete beaches you own'], 403);
        }
        
        $beach->delete();
        return response()->json(null, 204);
    }
}
