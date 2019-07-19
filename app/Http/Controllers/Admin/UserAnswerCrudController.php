<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\UserAnswerRequest as StoreRequest;
use App\Http\Requests\UserAnswerRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Symfony\Component\Console\Input\Input;

/**
 * Class UserAnswerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class UserAnswerCrudController extends CrudController
{
    public function setup()
    {
        /* 
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\UserAnswer');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user_answer');
        $this->crud->setEntityNameStrings('UserAnswer', 'UserAnswers');

        $this->crud->denyAccess('create');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('update');
        $this->crud->removeAllButtonsFromStack('line');

        $t = request()->input('state');
        if (!isset($t)) {
            $this->crud->addClause('where', 'state', 1);
        }


        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        // $this->crud->setFromDb();
        $this->crud->addColumn(
            [
                'name' => 'id',
                'label' => 'ID',
                'type' => 'text',
            ]
        );
        $this->crud->addColumn(
            [
                'name' => 'user_id',
                'label' => 'User ID',
                'type' => 'text',
            ]
        );
        $this->crud->addColumn(
            [
                // 1-n relationship
                'label' => "Campaign", // Table column heading
                'type' => "select",
                'name' => 'campaign_id', // the column that contains the ID of that connected entity;
                'entity' => 'campaign', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Campaign", // foreign key model
            ]
        );
        $this->crud->addColumn(
            [
                // 1-n relationship
                'label' => "Poll", // Table column heading
                'type' => "select",
                'name' => 'poll_id', // the column that contains the ID of that connected entity;
                'entity' => 'poll', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Poll", // foreign key model
            ]
        );

        $this->crud->addColumn(
            [
                // 1-n relationship
                'label' => "Question", // Table column heading
                'type' => "select",
                'name' => 'question_id', // the column that contains the ID of that connected entity;
                'entity' => 'question', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Question", // foreign key model
            ]
        );
        $this->crud->addColumn(
            [
                // 1-n relationship
                'label' => "Answer", // Table column heading
                'type' => "select",
                'name' => 'answer_id', // the column that contains the ID of that connected entity;
                'entity' => 'answer', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Answer", // foreign key model
            ]
        );


        $this->crud->addColumn(
            [
                'name' => "created_at",
                'label' => "Created",
                'type' => "date",
            ]
        );


        //filters
        $this->crud->addFilter(
            [ // simple filter
                'type' => 'text',
                'name' => 'user_id',
                'label' => 'User ID'
            ],
            false,
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'user_id', 'LIKE', "%$value%");
            }
        );


        $this->crud->addFilter([ // select2 filter
            'name' => 'campaign_id',
            'type' => 'select2',
            'label' => 'Campaign'
        ], function () {
            return \App\Models\Campaign::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'campaign_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'poll_id',
            'type' => 'select2',
            'label' => 'Poll'
        ], function () {
            return \App\Models\Campaign::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'poll_id', $value);
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'question_id',
            'type' => 'select2',
            'label' => 'Question'
        ], function () {
            return \App\Models\Question::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'question_id', $value);
        });


        $this->crud->addFilter([ // select2 filter
            'name' => 'answer_id',
            'type' => 'select2',
            'label' => 'Answer'
        ], function () {
            return \App\Models\Campaign::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'answer_id', $value);
        });

        $this->crud->addFilter(
            [ // daterange filter
                'type' => 'date_range',
                'name' => 'from_to',
                'label' => 'Date range'
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'created_at', '>=', $dates->from);
                $this->crud->addClause('where', 'created_at', '<=', $dates->to . ' 23:59:59');
            }
        );
        $this->crud->addFilter([ // select2 filter
            'name' => 'state',
            'type' => 'select2',
            'label' => 'Published'
        ], function () {
            return [

                0 => 'All',
                1 => 'Active',

            ];
        }, function ($value) { // if the filter is active
            if ($value > 0) {
                $this->crud->addClause('where', 'state', ($value));
            }
        });



        // add asterisk for fields that are required in UserAnswerRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
