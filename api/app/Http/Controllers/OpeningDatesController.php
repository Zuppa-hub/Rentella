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
        return response()->json( OpeningDate::findOrfail($id)->update($request->all()), 200);
    }
    
    public function destroy($id)
    {   
        return response()->json(OpeningDate::findOrFail($id)->delete(),200);
    }
}
