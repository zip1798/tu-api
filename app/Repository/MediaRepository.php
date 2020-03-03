<?php

namespace App\Repository;


use App\Media;
use Intervention\Image\Facades\Image;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class MediaRepository
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

    public function saveImagesToStorage($file, Media $media) {
        Image::configure(array('driver' => 'imagick'));
        foreach(self::STRUCTURTE as $structure) {
            $image = Image::make($file);
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

            $filename = $this->generateHashName($resized->__toString(), "media/{$media->category}/{$structure['storage']}/m-{$media->id}-", 'jpg');
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


    public function removeImagesFromStorage(Media $media) {
        foreach(self::STRUCTURTE as $structure) {
            if (!empty($media->{$structure['field']})) {
                Storage::delete($structure['field']);
            }
        }
    }

}
