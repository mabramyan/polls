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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('login', 'API\UserController@login');
//Route::post('register', 'API\UserController@register');
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('details', 'API\UserController@details');
    Route::get('poll', 'API\PollController@getPoll');
    Route::get('poll/{id?}', 'API\PollController@getPoll');
    Route::post('vote', 'API\UserController@vote');
    Route::put('vote', 'API\UserController@voteUpdate');

   // Route::get('poll', 'API\PollController@getPoll');
});
