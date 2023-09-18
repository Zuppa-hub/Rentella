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
        return response()->json(BeachType::all());
    }

    public function show($id)
    {
        return response()->json(BeachType::findOrFail($id));
    }

    public function store(BeachTypeRequest $request)
    {
        return response()->json(BeachType::create($request->all()), 201);
    }

    public function update(BeachTypeRequest $request, $id)
    {
        return response()->json(BeachType::findOrFail($id)->update($request->all()), 200);
    }

    public function destroy($id)
    {
        $beachType = BeachType::findOrFail($id)->delete();
        return response()->json($id);
    }
}
