<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'city', 'phone', 'icon'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['icon_url'];

    public function getIconUrlAttribute()
    {
        return $this->attributes['icon']  ? Storage::url($this->attributes['icon']) : '';
    }

    public function getIsAdminAttribute()
    {
        return $this->attributes['role']  ? $this->attributes['role'] == 'admin' : false;
    }

    public function getIsModeratorAttribute()
    {
        return $this->attributes['role']  ? $this->attributes['role'] == 'moderator' : false;
    }

    public function hasRole($role)
    {
        switch ($role) {
            case 'user':
                return in_array($this->role, ['user', 'moderator', 'admin']);
            case 'moderator':
                return in_array($this->role, ['moderator', 'admin']);
            default:
                return $role && $role == $this->role;
        }
    }

    /**
     * Relations
     */

    public function events()
    {
        return $this->hasMany('App\Event');
    }

    public function media()
    {
        return $this->hasMany('App\Media');
    }

    public function rel_events()
    {
        return $this->belongsToMany('App\Event', 'event2user', 'user_id', 'event_id');
    }

    public function interested_events()
    {
        return $this->belongsToMany('App\Event', 'event2user', 'user_id', 'event_id')->wherePivot('type', 'interest');
    }

    public function participated_events()
    {
        return $this->belongsToMany('App\Event', 'event2user', 'user_id', 'event_id')->wherePivot('type', 'participation');
    }

    public function registered_events()
    {
        return $this->belongsToMany('App\Event', 'event2user', 'user_id', 'event_id')->wherePivot('type', 'registration');
    }

    public function sponsored_events()
    {
        return $this->belongsToMany('App\Event', 'event2user', 'user_id', 'event_id')->wherePivot('type', 'sponsor');
    }

    public function backed_events()
    {
        return $this->belongsToMany('App\Event', 'event2user', 'user_id', 'event_id')->wherePivot('type', 'backer');
    }

    public function spam_marked_events()
    {
        return $this->belongsToMany('App\Event', 'event2user', 'user_id', 'event_id')->wherePivot('type', 'spam');
    }


}
