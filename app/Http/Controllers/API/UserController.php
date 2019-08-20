<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Exceptions\ApiException;
use App\Models\Answer;
use App\Models\UserAnswer;
use App\Models\Poll;
use App\Http\Resources\Answer as AppAnswer;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $token = Str::random(60);

            $user->forceFill([
                'api_token' => $token // hash('sha256', $token),
            ])->save();

            return ['token' => $token];



            // $success['token'] =  $user->createToken('MyApp')->accessToken;
            // return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    public function vote($user_id, $answer_id)
    {
        // $userId = (int) $request->input('user_id', 0);
        // $answerId = (int) $request->input('answer_id', 0);
        $userId = (int) $user_id;
        $answerId = (int)  $answer_id;
        if (empty($userId)) {
            throw new ApiException('Incorrect user_id', 2);
        }
        if (empty($answerId)) {
            throw new ApiException('Incorrect answer_id', 3);
        }
        $answer =  Answer::where([['id', $answerId], ['state', 1]])->first();

        if (empty($answer)) {
            throw new ApiException('Answer not found', 4);
        }
        if (UserAnswer::vote($userId, $answerId)) {
            return response()->json(['success' => true], $this->successStatus);
        }
        throw new ApiException('Unknown error', 100);
    }

    public function voteUpdate($user_id, $answer_id)
    {
        // $userId = (int) $request->input('user_id', 0);
        // $answerId = (int) $request->input('answer_id', 0);
        $userId = (int) $user_id;
        $answerId = (int)  $answer_id;
        if (empty($userId)) {
            throw new ApiException('Incorrect user_id', 2);
        }
        if (empty($answerId)) {
            throw new ApiException('Incorrect answer_id', 3);
        }
        $answer =  Answer::where([['id', $answerId], ['state', 1]])->first();

        if (empty($answer)) {
            throw new ApiException('Answer not found', 4);
        }
        if (UserAnswer::voteUpdate($userId, $answerId)) {
            return response()->json(['success' => true], $this->successStatus);
        }
        throw new ApiException('Unknown error', 100);
    }
    public function bulkVote(Request $request, $user_id, $poll_id)
    {
        $poll = Poll::where('id', '=', $poll_id)->first();
        if (empty($poll)) {
            throw new ApiException('Poll not found', 21);
        }
        $answers = $request->get('answers');
        if (empty($answers)) {
            throw new ApiException('No answers detected', 22);
        }
        if (empty($poll->questions)) {
            throw new ApiException('Poll has no questions', 23);
        }
        if ($poll->questions->filter(function($value, $key){
            return $value->state >0?$value:false;
            
                        })->count() != count($answers)) {
            throw new ApiException('Wrong number of answers', 25);
        }

        $countQuestions = \DB::select(\DB::raw("SELECT  count(*) as cnt from 
        (
        SELECT count(*) 
         FROM answers as a 
         inner join questions as q on q.id=a.question_id
         inner join polls as p on p.id=q.poll_id
         where poll_id=:poll_id
         and a.id in (" . implode(',', $answers) . ")
         group by q.id
        ) as t 
        "), array(
            'poll_id' => $poll_id,
        ));

        if (empty($countQuestions)) {
            throw new ApiException('Wrong number of answers', 26);
        }

        if ($countQuestions[0]->cnt != $poll->questions->filter(function($value, $key){
            return $value->state >0?$value:false;
            
                        })->count()) {
            throw new ApiException('Wrong number of answers', 27);
        }
        $res = true;

        foreach ($answers as $one) {
            $t =   UserAnswer::voteUpdate($user_id, $one);
            $res = $res &&  $t;
        }
        if($res)
        {
            return response()->json(['success' => true], $this->successStatus);
        }
        throw new ApiException('Unknown error', 100);
    }
}
