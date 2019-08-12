<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PollRequest as StoreRequest;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\PollRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\CrudPanel;
use Illuminate\Support\Carbon;

/**
 * Class PollCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PollCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel('App\Models\Poll');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/poll');
        $this->crud->setEntityNameStrings('poll', 'polls');
        $this->crud->addButtonFromView('line', 'finish', 'finish', 'beginning');
        $this->crud->addButtonFromView('line', 'customedit', 'customedit', 'end');
        $this->crud->addButtonFromView('line', 'customdelete', 'customdelete', 'end');
        $this->crud->removeButton('update');
        $this->crud->removeButton('delete');
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
                'name' => 'start_date',
                'label' => 'Start Date',
                'type' => 'datetime',

            ]
        );
        $this->crud->addColumn(
            [
                'name' => 'end_date',
                'label' => 'End Date',
                'type' => 'datetime',

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
        $this->crud->addField([
            'name' => 'number',
            'type' => 'hidden',
        ]);
        $this->crud->addField([ // Select2
            'label' => "Campaign",
            'type' => 'select2',
            'name' => 'campaign_id', // the db column for the foreign key
            'entity' => 'campaign', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Campaign", // foreign key model
            'options' => (function ($query) {
                return $query->orderBy('id', 'DESC')->get();
            }),
        ]);

        $this->crud->addField([
            'name' => 'start_date', // the name of the db column
            'label' => 'Start Date', // the input label
            'type' => 'datetime_picker',
        ]);
        $this->crud->addField([
            'name' => 'end_date', // the name of the db column
            'label' => 'End Date', // the input label
            'type' => 'datetime_picker',
        ]);

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
            'name' => 'campaign_id',
            'type' => 'select2',
            'label' => 'Campaign',
        ], function () {
            return \App\Models\Campaign::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'campaign_id', $value);
        });

        // add asterisk for fields that are required in PollRequest
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
    public function edit($id)
    {
        $poll = \App\Models\Poll::findOrFail($id);

        if ($poll->id && !$poll->canEdit) {
            \Alert::warning(trans('Poll is finished'))->flash();
            return back();
        }
        return parent::edit($id);
    }

    public function finish($id)
    {
        $poll = \App\Models\Poll::findOrFail($id);
        $current = Carbon::now();

        if (!$poll->finished) {
            $poll->finished = 1;
            $poll->finished_date = $current;
            if ($poll->save()) {
                \Alert::success(trans('Saved sccess'))->flash();
                $questions = $poll->questions;
                foreach ($questions as $question) {
                    $answerIds = $question->answers()->where('correct', 1)->pluck('id')->toArray();
                    \App\Models\UserAnswer::whereIn('answer_id', $answerIds)->update(['correct' => 1]);
                }

                return back();
            }
            \Alert::error(trans('Error: can\'t save'))->flash();
            return back();
        }
        \Alert::warning(trans('Poll is finished'))->flash();
        return back();
    }
}
