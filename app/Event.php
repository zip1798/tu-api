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
    const STATUS_DRAFT = 'draft';

    const CATEGORY_REGULAR = 'regular';
    const CATEGORY_UNREGULAR = 'unregular';
    const CATEGORY_SEMINAR = 'seminar';
    const CATEGORY_OTHER = 'other';

    const statuses = [self::STATUS_PENDING, self::STATUS_PUBLIC, self::STATUS_HIDDEN, self::STATUS_DELETED, self::STATUS_ARCHIVED];
    const categories = [self::CATEGORY_REGULAR, self::CATEGORY_UNREGULAR, self::CATEGORY_SEMINAR, self::CATEGORY_OTHER];


    protected $fillable = [
        'title', 'place', 'event_date', 'show_date', 'category', 'status', 'allow_online', 'brief'
        , 'description', 'media_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function media()
    {
        return $this->belongsTo('App\Media');
    }

    public function rel_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id');
    }

}
