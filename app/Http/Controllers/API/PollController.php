<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Poll as Poll;
use App\Http\Resources\Poll as PollResource; 

class PollController extends Controller
{
    //
    public function getPoll(Request $request, $id = null)
    {
       // $pollId = $request->get('id');
        if (empty($id)) {

            return new PollResource($this->getActivePoll()) ;
        }
        return Poll::where('id', $pollId)->first();
    }

    public function getActivePoll()
    {
        return Poll::first();
    }
}
