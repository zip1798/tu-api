<?php

namespace App\Http\Controllers\API;

use App\Media;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'type' => 'required',
            'file' => 'image',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], ApiHelper::ERROR_VALIDATE_STATUS);
        }

        $media = Media::create($request->all());
        $category = $request->get('category');

        $resized = Image::make($request->file('file'))->fit(256)->encode('jpg');
        $filename = $this->generateHashName($resized->__toString(), "media/{$category}/thumbnail/-{$media->id}-", 'jpg');
        if (Storage::put($filename, $resized->__toString(), 'public')) {
            if ($media->thumbnail) {
                Storage::delete($media->thumbnail);
            }
            $media->update(['thumbnail' => $filename]);
        }


        return response()->json(['success' => $media], ApiHelper::SUCCESS_STATUS);
    }

    private function generateHashName($string, $prefix = '', $ext = 'jpg' ) {
        $now = Carbon::now()->toDateTimeString();
        return $prefix . md5($string.$now) . '.' . $ext;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
    public function destroy($id)
    {
        //
    }
}
