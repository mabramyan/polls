<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Poll as Poll;
use App\Http\Resources\Poll as PollResource; 
use App\Http\Resources\Polls as Polls; 
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

        return new PollResource($fined);
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
     

        return  Polls::collection($fined);
    }

    public function getActivePoll()
    {
        return Poll::first();
    }

    

}
