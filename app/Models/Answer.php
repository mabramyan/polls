<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
     */

    protected $table = 'answers';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'question_id', 'state', 'correct', 'team_id'];
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
    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }
    public function team()
    {
        return $this->belongsTo('App\Models\Team');
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
    public function getPollIdAttribute()
    {

        return $this->question->poll->id;
    }
    public function getCanEditAttribute()
    {
        return true;
    }

    /*
|--------------------------------------------------------------------------
| MUTATORS
|--------------------------------------------------------------------------
 */
}
