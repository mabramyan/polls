<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Question extends Model
{
    use CrudTrait;

    protected $defaults = array(
        'start_date' => '',
        'end_date' => '',
    );
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'questions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'poll_id', 'start_date', 'end_date', 'state'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function __construct(array $attributes = array())
    {
        $this->defaults = array(
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s'),
        );

        $this->setRawAttributes($this->defaults, true);
        parent::__construct($attributes);
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function poll()
    {
        return $this->belongsTo('App\Models\Poll');
    }
    public function answers()
    {
        return $this->hasMany('App\Models\Answer');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setStartDateAttribute($value)
    {

        $this->attributes['start_date'] = empty($value) ? date('Y-m-d H:i:s') : $value;
    }
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? date('Y-m-d H:i:s') : $value;
    }
}
