<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PriceRequest;
use App\Models\Price;

class PricesController extends Controller
{
    public function index()
    {
        return response()->json(
            Price::all()
        );
    }

    public function show($id)
    {
        return response()->json(
            Price::findOrFail($id)
        );
    }

    public function store(PriceRequest $request)
    {
        return response()->json(Price::create($request->all()), 201);
    }

    public function update(PriceRequest $request, $id)
    {
        return response()->json(Price::findOrFail($id)->update($request->all()), 200);
    }

    public function destroy($id)
    {
        return response()->json(Price::findOrFail($id)->delete());
    }
}
