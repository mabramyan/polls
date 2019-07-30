<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Team as TeamResource;
use App\Models\Team;



class SharedController extends Controller
{
    //
    public function getAllTeams()
    {
        return response()->json(['success'=> TeamResource::collection(Team::all())]);
        
    }
}
