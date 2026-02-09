<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UmbrellaRequest;
use App\Models\Umbrella;
use Illuminate\Http\Request;

class UmbrellasController extends Controller
{
    public function index()
    {
        return response()->json(Umbrella::all());
    }

    public function show($id)
    {
        return response()->json(Umbrella::findOrfail($id));
    }

    public function store(UmbrellaRequest $request)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $zone = \App\Models\BeachZone::findOrFail($request->input('zone_id'));
        $beach = $zone->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json(Umbrella::create($request->validated()), 201);
    }

    public function update(UmbrellaRequest $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $umbrella = Umbrella::findOrFail($id);
        $zone = $umbrella->beachzone;
        $beach = $zone->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $umbrella->update($request->validated());
        return response()->json($umbrella, 200);
    }

    public function destroy($id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $umbrella = Umbrella::findOrFail($id);
        $zone = $umbrella->beachzone;
        $beach = $zone->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $umbrella->delete();
        return response()->json(null, 204);
    }
}
