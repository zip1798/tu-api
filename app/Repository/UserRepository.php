<?php

namespace App\Repository;


use App\User;
use App\Jobs\SendEmailRegisterConfirmation;
use App\Notifications\RegisterConfirmation;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UserRepository
{
    use DispatchesJobs;

    public function createUser($data)
    {
        if (empty($data['password'])) {
            $data['password'] = Str::random(10);
        }
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $this->dispatch(new SendEmailRegisterConfirmation($user));
        $user->notify(new RegisterConfirmation());

        return $user;
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

}
