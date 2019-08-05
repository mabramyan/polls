<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\QuestionRequest as StoreRequest;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\QuestionRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\CrudPanel;

/**
 * Class QuestionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class QuestionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel('App\Models\Question');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/question');
        $this->crud->setEntityNameStrings('question', 'questions');

        $this->crud->allowAccess('reorder');
        $this->crud->enableReorder('name', 1);
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
         */

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();

        $this->crud->addColumn(
            [
                'name' => 'id',
                'label' => 'ID',
                'type' => 'text',
            ]
        );
        $this->crud->addColumn(
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ]
        );
        // $this->crud->addColumn(
        //     [
        //         'name' => 'team_2',
        //         'label' => 'Team 2',
        //         'type' => 'text',
        //     ]
        // );

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
                'name' => 'state',
                'label' => 'Published',
                'type' => 'boolean',
                // optionally override the Yes/No texts
                'options' => [0 => 'Unpublished', 1 => 'Published'],
            ]
        );

        $this->crud->addField([
            'name' => 'name', // the name of the db column
            'label' => 'Name', // the input label
            'type' => 'text',
        ]);
        // $this->crud->addField([
        //     'name' => 'team_2', // the name of the db column
        //     'label' => 'Team 2', // the input label
        //     'type' => 'text',
        // ]);

        $this->crud->addField([ // Select2
            'label' => "Poll",
            'type' => 'select2',
            'name' => 'poll_id', // the db column for the foreign key
            'entity' => 'poll', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Poll", // foreign key model
            'options' => (function ($query) {
                return $query->orderBy('id', 'DESC')->get();
            }),
        ]);

        // $this->crud->addField([ // Select2
        //     'label' => "Team 1",
        //     'type' => 'select2',
        //     'name' => 'team_1', // the db column for the foreign key
        //     'entity' => 'team1', // the method that defines the relationship in your Model
        //     'attribute' => 'name', // foreign key attribute that is shown to user
        //     'model' => "App\Models\Team", // foreign key model
        //     'options' => (function ($query) {
        //         return $query->orderBy('name', 'ASC')->get();
        //     }),
        // ]);
        // $this->crud->addField([ // Select2
        //     'label' => "Team 2",
        //     'type' => 'select2',
        //     'name' => 'team_2', // the db column for the foreign key
        //     'entity' => 'team2', // the method that defines the relationship in your Model
        //     'attribute' => 'name', // foreign key attribute that is shown to user
        //     'model' => "App\Models\Team", // foreign key model
        //     'options' => (function ($query) {
        //         return $query->orderBy('name', 'ASC')->get();
        //     }),
        // ]);

        $this->crud->addField([
            'name' => 'start_date', // the name of the db column
            'label' => 'Start Date', // the input label
            'type' => 'datetime_picker',
        ]);
        // $this->crud->addField([
        //     'name' => 'end_date', // the name of the db column
        //     'label' => 'End Date', // the input label
        //     'type' => 'datetime_picker',
        // ]);

        // $this->crud->addField(
        //     [
        //         'name' => 'image',
        //         'type' => 'image',
        //         'label' => 'Image',
        //         'upload' => true,
        //         'crop' => true, // set to true to allow cropping, false to disable
        //         'aspect_ratio' => 0
        //     ]
        // );

        $this->crud->addField(
            [ // Upload
                'name' => 'image',
                'label' => 'Image',
                'type' => 'upload',
                'upload' => true,
                //'disk' => 'public_uploads' // if you store files in the /public folder, please ommit this; if you store them in /storage or S3, please specify it;
            ]
        );

        $this->crud->addColumn(
            [
                'name' => 'start_date',
                'label' => 'Start Date',
                'type' => 'datetime',

            ]
        );
        // $this->crud->addColumn(
        //     [
        //         'name' => 'end_date',
        //         'label' => 'End Date',
        //         'type' => 'datetime',

        //     ]
        // );
        $this->crud->addField([
            'name' => 'state', // the name of the db column
            'label' => 'Published', // the input label
            'type' => 'radio',
            'default' => 1,
            'options' => [ // the key will be stored in the db, the value will be shown as label;
                0 => "Unpublished",
                1 => "Published",
            ],
        ]);

        $this->crud->addFilter([ // select2 filter
            'name' => 'poll_id',
            'type' => 'select2',
            'label' => 'Poll',
        ], function () {
            return \App\Models\Poll::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'poll_id', $value);
        });

        // add asterisk for fields that are required in QuestionRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
        $this->crud->removeButton('delete');
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
    public function edit($id)
    {
        $question = \App\Models\Question::findOrFail($id);
        if ($question->id && $question->poll->finished) {
            \Alert::warning(trans('Poll is finished'))->flash();
            return back();
        }
        return parent::edit($id);
    }
    public function getQuestion($id)
    {
        $this->crud->setModel('App\Models\Question');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/question');
        return \App\Models\Question::findOrFail($id);
    }
}
