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
            BeachZone::findOfFail($id)
        );
    }

    public function store(BeachZoneRequest $request)
    {
        return response()->json(
            BeachZone::create($request->all()),
            201
        );
    }

    public function update(BeachZoneRequest $request, $id)
    {
        return response()->json(
            BeachZone::findOrFail($id)->update($request->all()),
            200
        );
    }

    public function destroy($id)
    {
        return response()->json(
            BeachZone::findOrFail($id)->delete(),
            200
        );
    }
}
