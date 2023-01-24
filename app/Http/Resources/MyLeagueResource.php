<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyLeagueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $participants = $this->participants()->orderByDesc('score')->get();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_public' => $this->is_public,
            'competition' => $this->competition,
            'competition_name' => $this->Competition->name,
            'owner_username' => $this->Owner->username,
            'join_requests' => JoinRequestResource::collection($this->join_requests),
            'participants' => ParticipantWithoutLeagueResource::collection($participants),
        ];
    }
}
