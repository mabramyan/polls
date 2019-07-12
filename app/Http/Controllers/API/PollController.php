<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Poll as Poll;
use App\Http\Resources\Poll as PollResource; 
use App\Exceptions\ApiException;
class PollController extends Controller
{
    //
    public function getPoll(Request $request, $id = null)
    {
       // $pollId = $request->get('id');
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

    public function getActivePoll()
    {
        return Poll::first();
    }
}
