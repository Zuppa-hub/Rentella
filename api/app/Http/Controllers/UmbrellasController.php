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
        $umbrellas = Umbrella::all();
        return response()->json($umbrellas);
    }

    public function show($id)
    {
        $umbrella = Umbrella::find($id);

        if (!$umbrella) {
            return response()->json(['message' => 'Umbrella not found'], 404);
        }

        return response()->json($umbrella);
    }

    public function store(UmbrellaRequest $request)
    {
        $umbrella = Umbrella::create($request->all());
        return response()->json(['message' => 'Umbrella data saved successfully', 'data' => $umbrella], 201);
    }

    public function update(UmbrellaRequest $request, $id)
    {
        $umbrella = Umbrella::find($id);

        if (!$umbrella) {
            return response()->json(['message' => 'Umbrella not found'], 404);
        }

        $umbrella->update($request->all());
        return response()->json(['message' => 'Umbrella data updated successfully', 'data' => $umbrella], 200);
    }

    public function destroy($id)
    {
        $umbrella = Umbrella::find($id);

        if (!$umbrella) {
            return response()->json(['message' => 'Umbrella not found'], 404);
        }

        $umbrella->delete();
        return response()->json(['message' => 'Umbrella data removed successfully', 'id' => $id]);
    }
}
