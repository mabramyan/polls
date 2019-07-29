<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Poll as Poll;
use App\Models\Answer;
use App\Http\Resources\Poll as PollResource; 
use App\Http\Resources\Polls as Polls; 
use App\Http\Resources\Answer as AnswerResource;
use App\Exceptions\ApiException;
class PollController extends Controller
{
    //
    public function getPoll(Request $request, $id = null)
    {
        if (empty($id)) {
            return new PollResource($this->getActivePoll()) ;
        }

        $fined = Poll::where('id', $id)->first();
        if(empty($fined))
        {
            throw new ApiException("", 1);

        }
        return response()->json(['success'=> new PollResource($fined)]);
    }
    public function getPolls($campaign_id)
    {
        if (empty($campaign_id)) {
           throw new ApiException("Campaign not found", 50);
        }

        $fined = Poll::where([['campaign_id', $campaign_id],['state',1]])->get();
        if(empty($fined))
        {
            throw new ApiException("", 1);
        }

       return response()->json(['success'=>Polls::collection($fined)])  ;
    }

    public function getUserAnswers($campaign_id,$user_id,$poll_id=null)
    {
        $where = [['campaign_id', $campaign_id],['user_id'=>$user_id],['state',1]];
        if(!empty($poll_id))
        {
            $where[]=['poll_id'=>$poll_id];
        }
       $answers =  Answer::where($where)->get();
       return response()->json(['success'=> AnswerResource::collection($answers)]);

    }

    public function getActivePoll()
    {
        return Poll::first();
    }
    
}
