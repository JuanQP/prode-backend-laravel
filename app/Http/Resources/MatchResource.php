<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $detail = "{$this->teamA->name} - {$this->teamB->name}";

        return [
            'id' => $this->id,
            'competition' => $this->competition,
            'team_a' => $this->team_a,
            'team_a_detail' => new TeamResource($this->teamA),
            'team_a_score' => $this->team_a_score,
            'team_b' => $this->team_b,
            'team_b_detail' => new TeamResource($this->teamB),
            'team_b_score' => $this->team_b_score,
            'datetime' => $this->datetime,
            'stadium' => $this->stadium,
            'status' => $this->status,
            'description' => $this->description,
            'detail' => $detail,
        ];
    }
}
