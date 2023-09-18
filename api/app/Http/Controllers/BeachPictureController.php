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
        return response()->json(BeachPicture::create($request->all()), 201);
    }
    public function update(BeachPictureRequest $request, $id)
    {
        BeachPicture::findOrFail($id)->update($request->all());
        return response()->json(BeachPicture::findOrFail($id), 200);
    }
    public function destroy($id)
    {
        BeachPicture::findOrFail($id)->delete();
        return response()->json($id);
    }
}
