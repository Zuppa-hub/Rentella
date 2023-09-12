<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeachRequest;
use App\Models\Beach;

use Illuminate\Http\Request;

class BeachController extends Controller
{
    public function index(Request $request)
    {
        // if request has location paramenters 
        if ($request->has('cityId')) {
            return response()->json(
                Beach::where(
                    'location_id',
                    $request->input('cityId')
                )->get()
            );
        }
        return response()->json(
            Beach::all()
        );
    }

    public function show($id)
    {
        $beach = Beach::find($id);

        if (!$beach) {
            return response()->json(['message' => 'Beach not found'], 404);
        }

        return response()->json($beach);
    }

    public function store(BeachRequest $request)
    {
        $beach = Beach::create($request->all());
        return response()->json(['message' => 'Beach data saved successfully', 'data' => $beach], 201);
    }

    public function update(BeachRequest $request, $id)
    {
        $beach = Beach::find($id);

        if (!$beach) {
            return response()->json(['message' => 'Beach not found'], 404);
        }

        $beach->update($request->all());
        return response()->json(['message' => 'Beach data updated successfully', 'data' => $beach], 200);
    }

    public function destroy($id)
    {
        $beach = Beach::find($id);

        if (!$beach) {
            return response()->json(['message' => 'Beach not found'], 404);
        }

        $beach->delete();
        return response()->json(['message' => 'Beach data removed successfully', 'id' => $id]);
    }
}
