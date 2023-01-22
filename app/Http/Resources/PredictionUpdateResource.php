<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PredictionUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'team_a_score' => $this->team_a_score,
            'team_b_score' => $this->team_b_score,
        ];
    }
}
