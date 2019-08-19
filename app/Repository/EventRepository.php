<?php

namespace App\Repository;


use App\Event;
use App\Media;
use Intervention\Image\Facades\Image;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EventRepository
{

    public function toogleInterested($id) {
        $event = Event::find('id');
        $user = auth('api')->user();
        if ($user) {
            if ($event->is_interested) {
                $event->interested_user()->detach($user->id);
                return false;
            } else {
                $event->interested_user()->detach([$user->id => ['value' => '', 'data' => '']]);
                return true;
            }
        }

        return false;
    }

}
