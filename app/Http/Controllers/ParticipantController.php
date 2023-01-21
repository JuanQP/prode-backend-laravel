<?php

namespace App\Http\Controllers;

use App\Http\Resources\ParticipantResource;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['ranking']]);
    }

    /**
     * Display a ranking with best participants scores.
     *
     * @return \Illuminate\Http\Response
     */
    public function ranking()
    {
        $participants = Participant::orderByDesc('score')->take(10)->get();

        return ParticipantResource::collection($participants);
    }

    /**
     * Retrieve current user participations in leagues
     *
     * @return \Illuminate\Http\Response
     */
    public function my_participations()
    {
        $user = auth()->user();

        return ParticipantResource::collection($user->participations);
    }
}
