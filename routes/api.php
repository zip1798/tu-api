<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::post('password/create', 'API\PasswordResetController@create');
Route::post('password/reset', 'API\PasswordResetController@reset');

Route::group(['middleware' => 'auth:api'], function(){
	Route::post('details', 'API\UserController@details');
    Route::post('profile', 'API\ProfileController@info');
    Route::post('profile/update', 'API\ProfileController@update');
    Route::post('profile/update_avatar', 'API\ProfileController@updateAvatar');
    Route::post('password/update', 'API\ProfileController@updatePassword');
});

Route::apiResource('photos', 'PhotoController');