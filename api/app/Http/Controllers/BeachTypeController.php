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
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden: Admin only'], 403);
        }
        return response()->json(BeachType::create($request->validated()), 201);
    }

    public function update(BeachTypeRequest $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden: Admin only'], 403);
        }
        $type = BeachType::findOrFail($id);
        $type->update($request->validated());
        return response()->json($type, 200);
    }

    public function destroy($id)
    {
        $beachType = BeachType::findOrFail($id)->delete();
        return response()->json($id);
    }
}
