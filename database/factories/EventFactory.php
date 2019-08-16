<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Event;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return App\User::where('email', 'alexandr.ts@gmail.com')->first()->id;
        },
	    'title' => $faker->sentence(7),
	    'place' => $faker->country. ', ' .$faker->city,
	    'show_date' => $faker->dateTimeBetween('now', '+2years'),
	    'event_date' => $faker->dayOfMonth(). ' ' . $faker->monthName(),
	    'category' => $faker->randomElement(Event::categories),
	    'status' => Event::STATUS_PUBLIC,
	    'allow_online' => $faker->randomElement([0, 1]),
	    'brief' => nl2br($faker->paragraph(4)),
	    'description' => nl2br($faker->paragraphs(10, true)),
        'is_open_registration' => $faker->randomElement([0, 1]),
        'registration_fields' => '',
        'html_after_registration'  => '',
        'media_id' => function () {
            return App\Media::inRandomOrder()->first()->id;
        },
	];
});
