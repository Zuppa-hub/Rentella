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
        return response()->json(Umbrella::all());
    }

    public function show($id)
    {
        return response()->json(Umbrella::findOrfail($id));
    }

    public function store(UmbrellaRequest $request)
    {
        return response()->json(Umbrella::create($request->all()), 201);
    }

    public function update(UmbrellaRequest $request, $id)
    {
        return response()->json( Umbrella::findOrFail($id)->update($request->all()), 200);
    }

    public function destroy($id)
    {
        return response()->json(Umbrella::findOrfail($id)->delete());
    }
}
