<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinRequestCreateRequest;
use App\Http\Requests\JoinRequestUpdateRequest;
use App\Http\Resources\JoinRequestResource;
use App\Models\JoinRequest;
use App\Models\League;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class JoinRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\JoinRequestCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JoinRequestCreateRequest $request)
    {
        $user = auth()->user();
        $league = League::findOrFail($request->validated()['league']);
        if($league->is_public) {
            return response()->json([
                'message' => 'This League is public. Join request is not necessary.',
            ], 400);
        }

        $hasUnansweredRequest = JoinRequest::where('user', $user->id)
            ->where('league', $league->id)
            ->whereNull('accepted')
            ->exists();
        if($hasUnansweredRequest) {
            return response()->json([
                'message' => 'You have unanswered join requests.',
            ], 400);
        }

        $joinRequest = JoinRequest::create([
            'user' => $user->id,
            'league' => $league->id,
        ]);

        return new JoinRequestResource($joinRequest);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\JoinRequestUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JoinRequestUpdateRequest $request, $id)
    {
        $user = auth()->user();
        $joinRequest = JoinRequest::findOrFail($id);

        if(!$user->is_staff && $joinRequest->League->Owner->id != $user->id) {
            return response()->json([
                'message' => 'You are not the owner of the League.',
            ], 403);
        }
        if(!is_null($joinRequest->accepted)) {
            return response()->json([
                'message' => 'This join request has already been answered.',
            ], 403);
        }

        // Accept or deny join request
        $isAccepted = $request->validated()['accepted'];
        DB::transaction(function() use($joinRequest, $isAccepted) {
            $joinRequest->update([
                'accepted' => $isAccepted,
            ]);
            if($isAccepted) {
                $participant = new Participant([
                    'user' => $joinRequest->User->id,
                ]);
                // Add new Participant to League
                $joinRequest->League->participants()->save($participant);
            }
        });

        return new JoinRequestResource($joinRequest);
    }
}
