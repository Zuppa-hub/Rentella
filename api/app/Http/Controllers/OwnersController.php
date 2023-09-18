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
        return response()->json(Owner::findOrFail($id));
    }

    public function store(OwnerRequest $request)
    {
        return response()->json(Owner::create($request->all()), 201);
    }

    public function update(OwnerRequest $request, $id)
    {
        return response()->json(Owner::findOrFail($id)->update($request->all()), 200);
    }

    public function destroy($id)
    {
        return response()->json(Owner::findOrFail($id)->delete());
    }
}
