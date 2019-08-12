<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Poll as PollResource;
use App\Http\Resources\Polls as Polls;
use App\Models\Answer;
use App\Models\Poll as Poll;
use App\Models\UserAnswer;
use Illuminate\Http\Request;

class PollController extends Controller
{
    //
    public function getPoll(Request $request, $id = null)
    {
        if (empty($id)) {
            return new PollResource($this->getActivePoll());
        }

        $fined = Poll::where('id', $id)->first();
        if (empty($fined)) {
            throw new ApiException("", 1);
        }
        return response()->json(['success' => new PollResource($fined)]);
    }
    public function getPolls($campaign_id)
    {
        if (empty($campaign_id)) {
            throw new ApiException("Campaign not found", 50);
        }

        $fined = Poll::where([['campaign_id', $campaign_id], ['state', 1]])->orderBy('start_date', 'asc')->get();
        if (empty($fined)) {
            throw new ApiException("", 1);
        }

        return response()->json(['success' => Polls::collection($fined)]);
    }

    public function getUserAnswers($campaign_id, $user_id, $poll_id = null)
    {
        $where = [['campaign_id', $campaign_id], ['user_id', $user_id], ['state', 1]];
        if (!empty($poll_id)) {
            $where[] = ['poll_id', $poll_id];
        }
        $answers = UserAnswer::where($where)->get();
        foreach ($answers as $answer) {
            $answer['answer'] = Answer::where('id', $answer['answer_id'])->get();
        }

        return response()->json(['success' => $answers]);
    }

    public function getActivePoll()
    {
        return Poll::first();
    }
    public function getReport($poll_id)
    {
        $results = \DB::select(\DB::raw("SELECT
            au.user_id,
            count(a.id) as total,
            SUM(CASE WHEN a.correct=1 THEN 1 ELSE 0 END) as correct
        FROM user_answers as au
        inner join answers as  a on a.id=au.answer_id
        where au.poll_id=:poll_id
            and au.state=1
        group by au.user_id"), array(
            'poll_id' => $poll_id,
        ));

        return response()->json(['success' => $results]);
    }
}
