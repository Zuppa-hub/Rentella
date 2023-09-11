<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeachTypeRequest;
use App\Models\BeachType;

use Illuminate\Http\Request;

class BeachTypeController extends Controller
{
    public function index()
    {
        $beaches = BeachType::all();
        return response()->json($beaches);
    }
    
    public function show($id)
    {
        $beachType = BeachType::find($id);
        
        if (!$beachType) {
            return response()->json(['message' => 'Beach not found'], 404);
        }
        
        return response()->json($beachType);
    }
    
    public function store(BeachTypeRequest $request)
    {
        $beachType = BeachType::create($request->all());
        return response()->json(['message' => 'Beach data saved successfully', 'data' => $beachType], 201);
    }
    
    public function update(BeachTypeRequest $request, $id)
    {
        $beachType = BeachType::find($id);
        
        if (!$beachType) {
            return response()->json(['message' => 'Beach not found'], 404);
        }
        
        $beachType->update($request->all());
        return response()->json(['message' => 'Beach data updated successfully', 'data' => $beachType], 200);
    }
    
    public function destroy($id)
    {
        $beachType = BeachType::find($id);
        
        if (!$beachType) {
            return response()->json(['message' => 'Beach not found'], 404);
        }
        
        $beachType->delete();
        return response()->json(['message' => 'Beach data removed successfully', 'id' => $id]);
    }
}
