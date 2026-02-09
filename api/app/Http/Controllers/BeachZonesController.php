<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeachZoneRequest;
use App\Models\BeachZone;
use Illuminate\Http\Request;

class BeachZonesController extends Controller
{
    public function index()
    {
        return response()->json(
            BeachZone::all()
        );
    }

    public function show($id)
    {
        return response()->json(
            BeachZone::findOrFail($id)
        );
    }

    public function store(BeachZoneRequest $request)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $beach = \App\Models\Beach::findOrFail($request->input('beach_id'));
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json(BeachZone::create($request->validated()), 201);
    }

    public function update(BeachZoneRequest $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $zone = BeachZone::findOrFail($id);
        $beach = $zone->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $zone->update($request->validated());
        return response()->json($zone, 200);
    }

    public function destroy($id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $zone = BeachZone::findOrFail($id);
        $beach = $zone->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $zone->delete();
        return response()->json(null, 204);
    }
}
