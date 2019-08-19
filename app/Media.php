<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{

    protected $fillable = [
        'category', 'type',
    ];

    protected $appends = ['full_url', 'full_thumbnail_url'];

    public function getFullUrlAttribute()
    {
        return $this->attributes['url']  ? Storage::url($this->attributes['url']) : '';
    }

    public function getFullThumbnailUrlAttribute()
    {
        return $this->attributes['thumbnail_url']  ? Storage::url($this->attributes['thumbnail_url']) : '';
    }

}
