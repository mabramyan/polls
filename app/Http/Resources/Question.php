<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Answer as AnswersResource;

class Question extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => !empty($this->image) ? url('/') . '/' . $this->image : null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'fix_date' => $this->fix_date,
            'state' => $this->state,
            'answers' =>    AnswersResource::collection($this->answers->filter(function($value, $key){
                return $value->state ==1?$value:false;
                
                            })),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
