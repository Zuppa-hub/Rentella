<?php
namespace App\Http\Controllers;

use App\Http\Requests\BeachZoneRequest;
use App\Http\Requests\OpeningDateRequest;
use App\Models\OpeningDate;
use Illuminate\Http\Request;

class OpeningDatesController extends Controller
{
    public function index()
    {
        $openingDates = OpeningDate::all();
        return response()->json($openingDates);
    }
    
    public function show($id)
    {
        $openingDate = OpeningDate::find($id);
        
        if (!$openingDate) {
            return response()->json(['message' => 'OpeningDate not found'], 404);
        }
        
        return response()->json($openingDate);
    }
    
    public function store(OpeningDateRequest $request)
    {
        $openingDate = OpeningDate::create($request->all());
        return response()->json(['message' => 'OpeningDate data saved successfully', 'data' => $openingDate], 201);
    }
    
    public function update(OpeningDateRequest $request, $id)
    {
        $openingDate = OpeningDate::find($id);
        
        if (!$openingDate) {
            return response()->json(['message' => 'OpeningDate not found'], 404);
        }
        
        $openingDate->update($request->all());
        return response()->json(['message' => 'OpeningDate data updated successfully', 'data' => $openingDate], 200);
    }
    
    public function destroy($id)
    {
        $openingDate = OpeningDate::find($id);
        
        if (!$openingDate) {
            return response()->json(['message' => 'OpeningDate not found'], 404);
        }
        
        $openingDate->delete();
        return response()->json(['message' => 'OpeningDate data removed successfully', 'id' => $id]);
    }
}
