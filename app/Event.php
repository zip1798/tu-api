<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLIC = 'public';
    const STATUS_HIDDEN = 'hidden';
    const STATUS_DELETED = 'deleted';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'title', 'place', 'event_date', 'show_date', 'category', 'status', 'allow_online', 'brief'
        , 'description', 'media_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
