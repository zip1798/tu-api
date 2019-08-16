<?php

namespace App\Http\Controllers\API;

use App\Media;
use App\Repository\MediaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use App\Repository\ApiHelper;
use Validator;

class MediaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);
        $this->middleware('role:moderator', ['only' => ['store', 'update']]);
        $this->middleware('role:admin',   ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['success' => Media::all()], ApiHelper::SUCCESS_STATUS);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $repository = new MediaRepository();
        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'type' => 'required',
            'file' => 'image',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], ApiHelper::ERROR_VALIDATE_STATUS);
        }

        $media = Auth::user()->media()->create($request->only(['category', 'type']));
        $repository->saveImagesToStorage($request->file('file'), $media);

        return response()->json(['success' => $media], ApiHelper::SUCCESS_STATUS);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $media = Media::findOrFail($id);
        $repository = new MediaRepository();
        $repository->removeImagesFromStorage($media);
        $media->delete();

        return response()->json(['success' => 'OK'], ApiHelper::SUCCESS_STATUS);
    }
}
