<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeachPictureRequest;
use App\Models\BeachPicture;

use Illuminate\Http\Request;

class BeachPictureController extends Controller
{
    public function index()
    {
        return response()->json(BeachPicture::all());
    }
    public function show($id)
    {
        // Information related to specific location 
        return response()->json(BeachPicture::find($id));
    }
    public function store(BeachPictureRequest $request)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $beach = \App\Models\Beach::findOrFail($request->input('beach_id'));
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden: not beach owner'], 403);
        }
        return response()->json(BeachPicture::create($request->validated()), 201);
    }
    public function update(BeachPictureRequest $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $picture = BeachPicture::findOrFail($id);
        $beach = $picture->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden: not beach owner'], 403);
        }
        $picture->update($request->validated());
        return response()->json($picture, 200);
    }
    public function destroy($id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $picture = BeachPicture::findOrFail($id);
        $beach = $picture->beach;
        if (!$beach->owner || $beach->owner->email !== $authUser->email) {
            return response()->json(['error' => 'Forbidden: not beach owner'], 403);
        }
        $picture->delete();
        return response()->json(null, 204);
    }
}
