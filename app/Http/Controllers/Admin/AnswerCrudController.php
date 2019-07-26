<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AnswerRequest as StoreRequest;
use App\Http\Requests\AnswerRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class AnswerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AnswerCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Answer');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/answer');
        $this->crud->setEntityNameStrings('answer', 'answers');
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
            // [
            //     // 1-n relationship
            //     'label' => "Poll", // Table column heading
            //     'type' => "select",
            //     'name' => 'poll_id', // the column that contains the ID of that connected entity;
            //     'entity' => 'poll', // the method that defines the relationship in your Model
            //     'attribute' => "name", // foreign key attribute that is shown to user
            //     'model' => "App\Models\Poll", // foreign key model
            // ]
            [ // select_from_array
                'name' => 'poll_id',
                'label' => "Poll",
                'type' => 'select_from_array',
                'options' => \App\Models\Poll::all()->pluck('name', 'id')->toArray(),
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
                'label' => "Team", // Table column heading
                'type' => "select",
                'name' => 'team_id', // the column that contains the ID of that connected entity;
                'entity' => 'team', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Team", // foreign key model
            ]
        );


        $this->crud->addColumn(
            [
                'name' => 'correct',
                'label' => "Correct",
                'type' => 'check'
            ]
        );


        $this->crud->addColumn(
            [
                'name' => 'state',
                'label' => 'published',
                'type' => 'boolean',
                // optionally override the Yes/No texts
                'options' => [0 => 'Unpublished', 1 => 'Published']
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
            'label' => "Question",
            'type' => 'select2',
            'name' => 'question_id', // the db column for the foreign key
            'entity' => 'question', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Question", // foreign key model

            'options' => (function ($query) {
                return $query->orderBy('id', 'DESC')->get();
            }),
            'attributes' => [
                'class' => 'form-control changed_team',
                
              ], 
        ]);

        $this->crud->addField([ // Select2
            'label' => "Team",
            'type' => 'select2',
            'name' => 'team_id', // the db column for the foreign key
            'entity' => 'team', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Team", // foreign key model

            'options' => (function ($query) {
                if (!empty($this->crud->entry) && $this->crud->entry->id) {
                    $ans =  \App\Models\Answer::find($this->crud->entry->id);
                    if (!empty($ans->question->team_1) && !empty($ans->question->team_2)) {
                        $query->whereIn('id', [$ans->question->team_1, $ans->question->team_2]);
                    }
                }

                return $query->orderBy('id', 'DESC')->get();
            }),
            'attributes' => [
                'class' => 'form-control target_team',
                
              ], 
        ]);

        $this->crud->addField(

            [
                'name'        => 'correct', // the name of the db column
                'label'       => 'Correct', // the input label
                'type'        => 'radio',
                'options'     => [ // the key will be stored in the db, the value will be shown as label; 
                    0 => "No",
                    1 => "Yes"
                ],
                // optional
                //'inline'      => false, // show the radios all on the same line?
            ]
        );

        $this->crud->addField([
            'name' => 'state', // the name of the db column
            'label' => 'Published', // the input label
            'type' => 'radio',
            'default' => 1,
            'options' => [ // the key will be stored in the db, the value will be shown as label; 
                0 => "Unpublished",
                1 => "Published"
            ],
        ]);




        $this->crud->addFilter([ // select2 filter
            'name' => 'question_id',
            'type' => 'select2',
            'label' => 'Question'
        ], function () {
            return \App\Models\Question::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'question_id', $value);
        });



        // add asterisk for fields that are required in AnswerRequest
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

    public function reorder()
    {
        // $options['name'] = 'question_id';
        // $this->crud->addFilter($options,2);
        $queryId = request('question_id');
        if (!empty($queryId)) {
            $this->crud->addClause('where', 'question_id', $queryId);
        }

        return parent::reorder();
    }


    public function getEntries()
    {

        return parent::getEntries();
    }
    public function edit($id)
    {
        $answer = \App\Models\Answer::findOrFail($id);
        if ($answer->id && $answer->question->poll->finished) {
            \Alert::warning(trans('Poll is finished'))->flash();
            return back();
        }
        return parent::edit($id);
    }
}
