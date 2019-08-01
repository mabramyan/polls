<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Poll extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'polls';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'start_date', 'end_date', 'state', 'finished', 'finished_date', 'campaign_id', 'number'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function campaign()
    {
        return $this->belongsTo('App\Models\Campaign');
    }
    public function questions()
    {
        return $this->hasMany('App\Models\Question')->orderBy('lft','asc');
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
    public function getCanEditAttribute()
    {
        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setNumberAttribute($value)
    {
      
        $maxNumber = Poll::max('number');
        if(empty($this->attributes['id']))
        {if (empty($maxNumber)) {
            $this->attributes['number']  = 1;
            return 1;
        } else {
            $this->attributes['number']  = $maxNumber + 1;
            return $maxNumber + 1;
        }}
    }
    // public function setEndDateAttribute($value)
    // {
    //     $this->attributes['end_date'] =empty($value) ? date('Y-m-d H:i:s') : $value;
    // }
}
