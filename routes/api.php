<?php

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
Route::get('teams', 'API\SharedController@getAllTeams');
//Route::post('register', 'API\UserController@register');
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('get-report/{poll_id}', 'API\PollController@getReport');
    Route::get('get-user-answers/{campaign_id}/{user_id}/{poll_id?}', 'API\PollController@getUserAnswers');
    Route::get('polls/{campaign_id?}', 'API\PollController@getPolls');
    Route::get('poll/{id?}', 'API\PollController@getPoll');
    Route::get('poll-with-answers/{id?}/{user_id}', 'API\PollController@getPollWithAnswers');
    Route::get('vote/{user_id}/{answer_id}', 'API\UserController@vote');
    Route::get('voteupdate/{user_id}/{answer_id}', 'API\UserController@voteUpdate');
    Route::get('poll-users-answers/{poll_id}','API\PollController@getPollAnswers');
    Route::post('bulk-vote/{user_id}/{poll_id}', 'API\UserController@bulkVote');
    
// Route::get('poll', 'API\PollController@getPoll');
});
