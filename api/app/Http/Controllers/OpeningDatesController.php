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
        return response()->json(OpeningDate::all());
    }
    
    public function show($id)
    {        
        return response()->json(OpeningDate::findOrFail($id));
    }
    
    public function store(OpeningDateRequest $request)
    {
        return response()->json(OpeningDate::create($request->all()), 201);
    }
    
    public function update(OpeningDateRequest $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $openingDate = OpeningDate::findOrFail($id);
        $beach = $openingDate->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $openingDate->update($request->validated());
        return response()->json($openingDate, 200);
    }
    
    public function destroy($id)
    {   
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $openingDate = OpeningDate::findOrFail($id);
        $beach = $openingDate->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $openingDate->delete();
        return response()->json(null, 204);
    }
}
