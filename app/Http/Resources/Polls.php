<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
//use App\Http\Resources\Question as QuestionsResource;

class Polls extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'campaign_id'=>(int) $this->campaign_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'end_date' => $this->end_date,
            'state' => $this->state,
            //'questions' =>  QuestionsResource::collection($this->questions),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
