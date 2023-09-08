<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeachRequest;
use App\Models\Beach;

use Illuminate\Http\Request;

class BeachController extends Controller
{
    public function index(Request $request)
    {
        // if no return all the cities 
        $cityLocations = Beach::all();
        return response()->json($cityLocations);
    }
    public function show($id)
    {
        // Information related to specific location 
        return response()->json(Beach::find($id));
    }
    public function store(BeachRequest $request)
    {
        $city = Beach::create($request->all());
        return response()->json(['message' => 'City data saved successfully', 'data' => $city], 201);
    }
    public function update(BeachRequest $request, $id)
    {
        $city = Beach::findOrFail($id);
        $city->update($request->all());
        return response()->json(['message' => 'City data updated successfully', 'data' => Beach::findOrFail($id)], 200);
    }
    public function destroy($id)
    {
        $city = Beach::findOrFail($id);
        $city->delete();
        return response()->json(['message' => 'City data removed successfully', 'id' => $id]);
    }
}
