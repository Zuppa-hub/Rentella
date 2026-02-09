<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerRequest;
use App\Models\Owner;
use Illuminate\Http\Request;

class OwnersController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // admin emails configured in config/app.php 'admin_emails'
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $owners = Owner::all();
        return response()->json($owners);
    }

    public function show($id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json(Owner::findOrFail($id));
    }

    public function store(OwnerRequest $request)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json(Owner::create($request->validated()), 201);
    }

    public function update(OwnerRequest $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $owner = Owner::findOrFail($id);
        $owner->update($request->validated());
        return response()->json($owner, 200);
    }

    public function destroy($id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admins = config('app.admin_emails', []);
        if (!in_array($authUser->email, $admins)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $owner = Owner::findOrFail($id);
        $owner->delete();
        return response()->json(null, 204);
    }
}
