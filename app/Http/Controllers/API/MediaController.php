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
    const STRUCTURTE = [
        [
            'storage'   => 'main',
            'size'  => 1024,
            'field'     => 'url',
            'resize_type' => 'resize-aspect-ratio'
        ],
        [
            'storage'   => 'thumbnail',
            'size'  => 256,
            'field'     => 'thumbnail_url',
            'resize_type' => 'fit'
        ],

    ];

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

        $media = Auth::user()->media()->create($request->only(['category', 'type']));
        $this->saveImagesInStorage($request, $media);

        return response()->json(['success' => $media], ApiHelper::SUCCESS_STATUS);
    }

    protected function saveImagesInStorage(Request $request, Media $media) {
        $category = $request->get('category');
        foreach(self::STRUCTURTE as $structure) {
            $image = Image::make($request->file('file'));
            switch($structure['resize_type']) {
                case 'fit':
                    $image->fit($structure['size']);
                    break;
                default:
                    $image->resize($structure['size'], null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
            }
            $resized = $image->encode('jpg');

            $filename = $this->generateHashName($resized->__toString(), "media/{$category}/{$structure['storage']}/m-{$media->id}-", 'jpg');
            if (Storage::put($filename, $resized->__toString(), 'public')) {
                if ($media->{$structure['field']}) {
                    Storage::delete($media->{$structure['field']});
                }
                $media->{$structure['field']} = $filename;
            }
        }
        $media->save();
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
