<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->loadCount(['matches', 'leagues']);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'match_count' => $this->matches_count,
            'league_count' => $this->leagues_count,
        ];
    }
}
