<?php

namespace App\Repository;


use App\Event;
use App\Notifications\EventRegisterNotification;
use App\User;
use App\Jobs\SendEmailRegisterConfirmation;
use Illuminate\Notifications\Notifiable;
use App\Notifications\RegisterConfirmation;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UserRepository
{
    use DispatchesJobs;
    use Notifiable;

    public function createUser($data)
    {
        if (empty($data['password'])) {
            $data['password'] = Str::random(10);
        }
        $mail_data = $data;
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $user->notify(new RegisterConfirmation($mail_data));

        return $user;
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public static function testMail()
    {
        // $this->dispatch(new SendEmailRegisterConfirmation($user));
//        $user = User::all()->first();
//        $user->notify(new RegisterConfirmation([
//            'email'     => $user->email,
//            'password'  => '111 test password 111'
//        ]));

        if ($event = Event::with('media')->find(5)) {
            $event->user->notify(new EventRegisterNotification($event, [
                'name'      => 'TEst name',
                'email'     => 'test.test@localhost.com',
                'city'      => 'Kyiv',
                'phone'     => '234234234',
                'comments'  => 'This is Sparta!',
            ]));

        }

    }

}
