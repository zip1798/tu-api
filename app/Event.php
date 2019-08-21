<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    use SoftDeletes;

    protected $fillable = [
        'title', 'place', 'event_date', 'show_date', 'category', 'status', 'allow_online', 'brief'
        , 'description', 'media_id'
    ];

//    protected $appends = ['interested'];

    public function getIsInterestedAttribute()
    {
        $result = false;
        $user = auth('api')->user();
        if ($user) {
            $result = $this->interested_user()->where('id', $user->id)->count() > 0;
        }

        return $result;
    }

    public function getIsRegisteredAttribute()
    {
        $result = false;
        $user = auth('api')->user();
        if ($user) {
            $result = $this->registrated_user()->where('id', $user->id)->count() > 0;
        }

        return $result;
    }


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

    public function interested_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'interest');
    }

    public function participated_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'participation');
    }

    public function registrated_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'registration');
    }

    public function sponsored_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'sponsor');
    }

    public function backed_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'backer');
    }

    public function spam_marked_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'backer');
    }

}
