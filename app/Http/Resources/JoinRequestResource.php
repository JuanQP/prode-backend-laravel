<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JoinRequestResource extends JsonResource
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
            'user' => new UserResource($this->User),
            'league' => new LeagueResource($this->League),
            'accepted' => $this->accepted,
        ];
    }
}
