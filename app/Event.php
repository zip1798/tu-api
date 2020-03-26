<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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

    const PERM_READ = 'read';
    const PERM_UPDATE = 'update';
    const PERM_DELETE = 'delete';
    const PERM_APPROVE = 'approve';
    const PERM_MAILING = 'mailing';
    const PERM_PARTICIPATE = 'participate';
    const PERM_ALL = [self::PERM_READ, self::PERM_UPDATE, self::PERM_DELETE, self::PERM_APPROVE, self::PERM_MAILING, self::PERM_PARTICIPATE];
    const PERM_AUTHOR = [self::PERM_READ, self::PERM_UPDATE, self::PERM_MAILING, self::PERM_PARTICIPATE];

    const statuses = [self::STATUS_PENDING, self::STATUS_PUBLIC, self::STATUS_HIDDEN, self::STATUS_DELETED, self::STATUS_ARCHIVED];
    const categories = [self::CATEGORY_REGULAR, self::CATEGORY_UNREGULAR, self::CATEGORY_SEMINAR, self::CATEGORY_OTHER];

    use SoftDeletes;

    protected $fillable = [
        'title', 'place', 'date', 'expire_from', 'category', 'status', 'is_allow_online', 'brief'
        , 'description', 'media_id', 'is_open_registration', 'registration_fields'
        , 'html_after_registration', 'is_approved', 'is_private', 'external_link'
    ];

//    protected $appends = ['interested'];

    /**
     * Calculated Attributes
     */

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
            $result = $this->registered_user()->where('id', $user->id)->count() > 0;
        }

        return $result;
    }

    public function getCurrentUserRelationsAttribute()
    {
        $result = [];
        $user = auth('api')->user();
        if ($user) {
            $list = $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->where('user_id', $user->id)->withPivot('type')->get();
            foreach($list as $item) {
                if (!in_array($item->pivot->type, $result)) {
                    $result[] = $item->pivot->type;
                }
            }
        }

        return $result;
    }

    public function getUserRelationsAttribute()
    {
        $result = [];
        $list = $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->withPivot('type')->get()->toArray();
        foreach($list as $item) {
            if (!isset($result[$item['id']])) {
                $item['relations'] = [$item['pivot']['type']];
                unset($item['pivot']);
                $result[$item['id']] = $item;
            } else {
                if (!in_array($item['pivot']['type'], $result[$item['id']]['relations'])) {
                    $result[$item['id']]['relations'][] = $item['pivot']['type'];
                }
            }
        }

        return $result;
    }

    public function getPermsAttribute()
    {
        $user = auth('api')->user();
        if ($user->is_admin) return self::PERM_ALL;
        if ($this->attributes['user_id'] == $user->id) return self::PERM_AUTHOR;

        $relation_types = $this->getCurrentUserRelationsAttribute();
        if (in_array(Event2User::TYPE_SPONSOR, $relation_types)) return self::PERM_AUTHOR;

        $result = [];
        if ($this->attributes['is_private'] == 0 // public events
            || count(array_intersect($relation_types, [Event2User::TYPE_INVITED, Event2User::TYPE_PARTICIPATION, Event2User::TYPE_REGISTRAION]))
        ) {
            $result[] = self::PERM_READ;
        }
        if (in_array(Event2User::TYPE_PARTICIPATION, $relation_types)) {
            $result[] = self::PERM_PARTICIPATE;
        }

        return $result;
    }

    /**
     * Relations
     */

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
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->withPivot('type');
    }

    public function interested_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'interest');
    }

    public function participated_user()
    {
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'participation');
    }

    public function registered_user()
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
        return $this->belongsToMany('App\User', 'event2user', 'event_id', 'user_id')->wherePivot('type', 'spam');
    }

    /**
     * Scopes
     */

    public function scopePublic($query)
    {
        return $query->where('status', 'public');
    }

    public function scopeFuture($query)
    {
        return $query->where('expire_from', '>=', Carbon::now()->format('Y-m-d'));
    }

    public function scopePast($query)
    {
        return $query->where('expire_from', '<', Carbon::now()->format('Y-m-d'));
    }

}
