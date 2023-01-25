<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaguePaginatedSearchResultResource extends JsonResource
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
            'count' => $this->total(),
            'next' => $this->nextPageUrl(),
            'previous' => $this->previousPageUrl(),
            'currentPage' => $this->currentPage(),
            'pages' => $this->lastPage(),
            'results' => LeagueResource::collection($this->resource),
        ];
    }
}
