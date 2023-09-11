<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PriceRequest;
use App\Models\Price;

class PricesController extends Controller
{
    public function index()
    {
        $prices = Price::all();
        return response()->json($prices);
    }

    public function show($id)
    {
        $price = Price::find($id);

        if (!$price) {
            return response()->json(['message' => 'Price not found'], 404);
        }

        return response()->json($price);
    }

    public function store(PriceRequest $request)
    {
        $price = Price::create($request->all());
        return response()->json(['message' => 'Price data saved successfully', 'data' => $price], 201);
    }

    public function update(PriceRequest $request, $id)
    {
        $price = Price::find($id);

        if (!$price) {
            return response()->json(['message' => 'Price not found'], 404);
        }

        $price->update($request->all());
        return response()->json(['message' => 'Price data updated successfully', 'data' => $price], 200);
    }

    public function destroy($id)
    {
        $price = Price::find($id);

        if (!$price) {
            return response()->json(['message' => 'Price not found'], 404);
        }

        $price->delete();
        return response()->json(['message' => 'Price data removed successfully', 'id' => $id]);
    }
}
