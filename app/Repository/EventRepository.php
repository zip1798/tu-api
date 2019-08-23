<?php

namespace App\Repository;


use App\Event;
use App\Media;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EventRepository
{

    protected $hidden_list_attributes = ['description', 'html_after_registration'];

    public function toogleInterested($id)
    {
        $event = Event::find($id);
        $user = auth('api')->user();
        if ($user && $event) {
            if ($event->is_interested) {
                $event->interested_user()->detach($user->id);
                return false;
            } else {
                $event->interested_user()->attach([$user->id => ['value' => '', 'data' => '']]);
                return true;
            }
        }

        return false;
    }

    public function getFuturePublicEventsList()
    {
        return Event::with('media')->future()->public()->get()->makeHidden($this->hidden_list_attributes)->each->setAppends(['is_interested', 'is_registered']);
    }

    public function getFullEventList()
    {
        return Event::with('media')->all()->makeHidden($this->hidden_list_attributes);
    }

    public function getUserEventList()
    {
        $user = Auth::user();
        $author_list = $user->events()->with('media')->get()->makeHidden($this->hidden_list_attributes);
        $sponored_list = $user->sponsored_events()->with('media')->get()->makeHidden($this->hidden_list_attributes);

        $participated_list = $user->participated_events()->with('media')->past()->public()->get()->makeHidden($this->hidden_list_attributes);
        $backed_list = $user->backed_events()->with('media')->public()->get()->makeHidden($this->hidden_list_attributes);
        $interested_list = $user->interested_events()->with('media')->future()->public()->get()->makeHidden($this->hidden_list_attributes);
        $registered_list = $user->registrated_events()->with('media')->future()->public()->get()->makeHidden($this->hidden_list_attributes);

        $list = $author_list
            ->merge($sponored_list)
            ->merge($participated_list)
            ->merge($backed_list)
            ->merge($interested_list)
            ->merge($registered_list);

        return $list->sortBy(function($event) {
            return $event->id; // maybe change sort condition
        });

    }

}
