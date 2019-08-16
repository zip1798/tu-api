<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Media;
use Faker\Generator as Faker;

$factory->define(Media::class, function (Faker $faker) {
    $user = App\User::where('email', 'alexandr.ts@gmail.com')->first();
    return [
        'user_id' => ($user ? $user->id : null),
        'category' => 'event',
        'type' => 'image'
    ];
});

$factory->afterCreating(App\Media::class, function ($media, $faker) {
    $repository = new \App\Repository\MediaRepository();
    $test_image_dir = env('TEST_IMAGE_DIR');
    $files = glob($test_image_dir . "*.jpg");
    if (is_array($files) && count($files) > 0) {
        $filename = $files[rand(0, count($files)-1)];
        if (file_exists($filename)) {
            $repository->saveImagesToStorage(file_get_contents($filename), $media);
        } else {
            echo 'File dont exists' . $filename;
        }
    }
});
