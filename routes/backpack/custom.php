<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::post('/get_campaigns',  'CampaignCrudController@getCampaings');
    Route::get('/get_user_answers/{campaignId}/{id}',  'UserAnswerCrudController@getUserAnsers');
    
    CRUD::resource('poll', 'PollCrudController');
    CRUD::resource('question', 'QuestionCrudController');
    CRUD::resource('answer', 'AnswerCrudController');
    CRUD::resource('campaign', 'CampaignCrudController');
    CRUD::resource('user_answer', 'UserAnswerCrudController');
}); // this should be the absolute last line of this file