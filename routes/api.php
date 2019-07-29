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
    Route::get('get-user-answers/{campaign_id}/{poll_id}', 'API\PollController@getUserAnswers');
    Route::get('polls/{campaign_id?}', 'API\PollController@getPolls');
    Route::get('poll/{id?}', 'API\PollController@getPoll');
    Route::get('vote', 'API\UserController@vote');
    Route::get('voteupdate', 'API\UserController@voteUpdate');
   // Route::get('poll', 'API\PollController@getPoll');
});
