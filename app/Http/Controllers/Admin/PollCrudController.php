<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\PollRequest as StoreRequest;
use App\Http\Requests\PollRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

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
                'label' => 'State',
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
            'label' => 'State', // the input label
            'type' => 'radio',
            'default' => 1,
            'options' => [ // the key will be stored in the db, the value will be shown as label; 
                0 => "Unpublished",
                1 => "Published"
            ],
        ]);



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
}
