<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace' => 'App\Http\Controllers\Admin',
    'except' => ['getquestion/'],
], function () { // custom admin routes
    Route::post('/get_campaigns', 'CampaignCrudController@getCampaings');
    Route::get('/get_user_answers/{pollId}', 'UserAnswerCrudController@getUserAnsers');
    Route::get('poll/{id}/finish', 'PollCrudController@finish');
    Route::get('/getquestion/{id}', 'QuestionCrudController@getQuestion');
    Route::get('answer/{id}/number_seven', 'AnswerCrudController@saveNumberSeven')->name('number_seven');
    CRUD::resource('poll', 'PollCrudController');
    CRUD::resource('question', 'QuestionCrudController');
    CRUD::resource('answer', 'AnswerCrudController');
    CRUD::resource('campaign', 'CampaignCrudController');
    CRUD::resource('user_answer', 'UserAnswerCrudController');
    CRUD::resource('team', 'TeamCrudController');
}); // this should be the absolute last line of this file
