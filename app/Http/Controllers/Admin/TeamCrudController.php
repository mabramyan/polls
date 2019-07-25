<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TeamRequest as StoreRequest;
use App\Http\Requests\TeamRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class TeamCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TeamCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Team');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/team');
        $this->crud->setEntityNameStrings('team', 'teams');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
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
            ]);
            $this->crud->addField([
                'name' => 'name', // the name of the db column
                'label' => 'Name', // the input label
                'type' => 'text',
            ]);

        // add asterisk for fields that are required in TeamRequest
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
