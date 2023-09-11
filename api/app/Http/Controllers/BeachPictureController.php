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
        $beachPic = BeachPicture::create($request->all());
        return response()->json(['message' => 'Picture data saved successfully', 'data' => $beachPic], 201);
    }
    public function update(BeachPictureRequest $request, $id)
    {
        $beachPic = BeachPicture::findOrFail($id);
        $beachPic->update($request->all());
        return response()->json(['message' => 'Picture data updated successfully', 'data' => BeachPicture::findOrFail($id)], 200);
    }
    public function destroy($id)
    {
        $beachPic = BeachPicture::findOrFail($id);
        $beachPic->delete();
        return response()->json(['message' => 'Picture data removed successfully', 'id' => $id]);
    }
}
