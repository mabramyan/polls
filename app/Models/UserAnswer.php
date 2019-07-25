<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use App\Http\Resources\Answer;
use App\Models\Answer as AnswerModel;
use App\Exceptions\ApiException;

class UserAnswer extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'user_answers';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['user_id', 'poll_id', 'question_id', 'answer_id', 'campaign_id', 'state'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function vote($userId, $answerId)
    {
        $answer = AnswerModel::where('id', $answerId)->first();



        if (!self::canAdd($userId, $answer->question->id)) {
            throw new ApiException('Already has answer', 5);
        }

        $question = $answer->question;
        $poll = $question->poll;
        $campaign = $poll->campaign;


        $tmp = new UserAnswer();
        $tmp->user_id = $userId;
        $tmp->answer_id = $answer->id;
        $tmp->question_id = $question->id;
        $tmp->poll_id = $poll->id;
        $tmp->campaign_id = $campaign->id;

        return $tmp->save();
    }


    public static function canAdd($userId, $questionId)
    {
        if (UserAnswer::where([['user_id', $userId], ['question_id', $questionId]])->first()) {
            return false;
        }

        return true;
    }



    public static function voteUpdate($userId, $answerId)
    {
        $answer = AnswerModel::where('id', $answerId)->first();

        if (!self::canUpdate($userId, $answer->question->id)) {
            throw new ApiException('Already has answer', 5);
        }
        UserAnswer::where([['user_id', $userId], ['question_id', $answer->question->id]])->update(['state' => '0']);
        $question = $answer->question;
        $poll = $question->poll;
        $campaign = $poll->campaign;


        $tmp = new UserAnswer();
        $tmp->user_id = $userId;
        $tmp->answer_id = $answer->id;
        $tmp->question_id = $question->id;
        $tmp->poll_id = $poll->id;
        $tmp->campaign_id = $campaign->id;

        return $tmp->save();
    }


    public static function canUpdate()
    {
        return true;
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
    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }
    public function answer()
    {
        return $this->belongsTo('App\Models\Answer');
    }
    public function campaign()
    {
        return $this->belongsTo('App\Models\Campaign');
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
    public function getCorrectAttribute($value)
    {
        return isset($value) ? $value : null;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
