<?php

namespace App\Repository;


use App\Event;
use App\Event2User;
use App\Media;
use App\Notifications\EventRegisterConfirmation;
use App\Notifications\EventRegisterNotification;
use App\User;
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
                $event->interested_user()->attach([$user->id => ['type' => 'interest', 'value' => '', 'data' => '']]);
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

    public function eventRegistration($data)
    {
        $UserRepository = new UserRepository();
        $result = 'OK';
        $data = $this->prepareEventRegistrationData($data);
        if (!$user = $UserRepository->findByEmail($data['email'])) {
            $user = $UserRepository->createUser($data);
        }
        $this->createEventRegistrationForUser($user, $data);
        if ($event = Event::with('media')->find($data['event_id'])) {
            $event->user->notify(new EventRegisterNotification($event, $data));
            $user->notify(new EventRegisterConfirmation($event));
        }

        return $result;
    }

    public function getEventMembers($event_id)
    {
        $result = [];
        $event2user_list = Event2User::where('event_id', $event_id)->with('user')->get()->toArray();
        foreach($event2user_list as $event2user_item) {
            if (!isset($result[$event2user_item['user_id']])) {
                $result[$event2user_item['user_id']] = $event2user_item['user'];
            }
            $result[$event2user_item['user_id']][$event2user_item['type']] = $result[$event2user_item['user_id']][$event2user_item['type']] ?? [];
            $result[$event2user_item['user_id']][$event2user_item['type']][] = $event2user_item['data'];
        }

        return $result;
    }

    protected function prepareEventRegistrationData($data) {
        $default_data = ['name' => 'n/a', 'email' => 'n/a', 'city' => 'n/a', 'phone' => 'n/a', 'comments' => 'n/a'];
        return array_merge($default_data, $data);
    }

    protected function createEventRegistrationForUser(User $user, $data)
    {
        $user->registered_events()->attach([
            $data['event_id'] => [
                'type'  => 'registration',
                'value' => $data['name'],
                'data'  => $this->prepareDataEventRegistration($data)
            ]
        ]);
    }

    private function prepareDataEventRegistration($data)
    {
        $result = "Name: {$data['name']}\n";
        $result .= "Email: {$data['email']}\n";
        $result .= "City: {$data['city']}\n";
        $result .= "Phone: {$data['phone']}\n\n";
        $result .= "Comments: {$data['comments']}";

        return $result;
    }

}
