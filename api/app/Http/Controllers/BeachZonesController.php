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
        $beaches = BeachZone::all();
        return response()->json($beaches);
    }
    
    public function show($id)
    {
        $beachType = BeachZone::find($id);
        
        if (!$beachType) {
            return response()->json(['message' => 'Beach not found'], 404);
        }
        
        return response()->json($beachType);
    }
    
    public function store(BeachZoneRequest $request)
    {
        $beachType = BeachZone::create($request->all());
        return response()->json(['message' => 'Beach data saved successfully', 'data' => $beachType], 201);
    }
    
    public function update(BeachZoneRequest $request, $id)
    {
        $beachType = BeachZone::find($id);
        
        if (!$beachType) {
            return response()->json(['message' => 'Beach not found'], 404);
        }
        
        $beachType->update($request->all());
        return response()->json(['message' => 'Beach data updated successfully', 'data' => $beachType], 200);
    }
    
    public function destroy($id)
    {
        $beachType = BeachZone::find($id);
        
        if (!$beachType) {
            return response()->json(['message' => 'Beach not found'], 404);
        }
        
        $beachType->delete();
        return response()->json(['message' => 'Beach data removed successfully', 'id' => $id]);
    }
}
