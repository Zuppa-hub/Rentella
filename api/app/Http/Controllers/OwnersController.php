<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerRequest;
use App\Models\Owner;
use Illuminate\Http\Request;

class OwnersController extends Controller
{
    public function index()
    {
        $owners = Owner::all();
        return response()->json($owners);
    }

    public function show($id)
    {
        $owner = Owner::find($id);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        return response()->json($owner);
    }

    public function store(OwnerRequest $request)
    {
        $owner = Owner::create($request->all());
        return response()->json(['message' => 'Owner data saved successfully', 'data' => $owner], 201);
    }

    public function update(OwnerRequest $request, $id)
    {
        $owner = Owner::find($id);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        $owner->update($request->all());
        return response()->json(['message' => 'Owner data updated successfully', 'data' => $owner], 200);
    }

    public function destroy($id)
    {
        $owner = Owner::find($id);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        $owner->delete();
        return response()->json(['message' => 'Owner data removed successfully', 'id' => $id]);
    }
}
