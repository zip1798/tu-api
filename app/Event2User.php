<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event2User extends Model
{
    const TYPE_INTEREST = 'interest';
    const TYPE_PARTICIPATION = 'partisipation';
    const TYPE_REGISTRAION = 'registraion';
    const TYPE_SPONSOR = 'sponsor';
    const TYPE_BACKER = 'backer';
    const TYPE_SPAM = 'spam';
    const TYPE_INVITED = 'invited';

    const TYPES = [self::TYPE_INTEREST, self::TYPE_PARTICIPATION, self::TYPE_REGISTRAION, self::TYPE_SPONSOR,
        self::TYPE_BACKER, self::TYPE_SPAM, self::TYPE_INVITED
        ];

    protected $table = 'event2user';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function event()
    {
        return $this->belongsTo('App\Event');
    }

}