<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeagueCreateRequest;
use App\Http\Requests\LeagueUpdateRequest;
use App\Http\Requests\PredictionCreateRequest;
use App\Http\Resources\LeagueDetailResource;
use App\Http\Resources\LeagueResource;
use App\Http\Resources\PredictionResource;
use App\Models\League;
use App\Models\Participant;
use App\Models\Prediction;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'index',
            'show',

        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leagues = League::with('Competition', 'Owner')->get();
        return LeagueResource::collection($leagues);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\LeagueCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeagueCreateRequest $request)
    {
        $league = new League($request->validated());
        $league->owner = auth()->user()->id;
        $league->save();

        return new LeagueResource($league);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $league = League::with('Competition', 'Owner', 'participants.User')->findOrFail($id);
        return new LeagueDetailResource($league);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\LeagueUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeagueUpdateRequest $request, $id)
    {
        $league = League::findOrFail($id);
        $user = auth()->user();

        if(!$user->is_staff && $league->Owner->id != $user->id) {
            return response()->json([
                'message' => 'This league is not yours.',
            ], 401);
        }

        $league->fill($request->validated());
        $league->save();

        return new LeagueDetailResource($league);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $league = League::findOrFail($id);
        $user = auth()->user();

        if(!$user->is_staff && $league->Owner->id != $user->id) {
            return response()->json([
                'message' => 'This league is not yours.',
            ], 401);
        }

        $league->delete();

        return response()->json(['message' => 'Deleted.']);
    }

    /**
     * Adds a Prediction to this League
     *
     * @param  \App\Http\Requests\PredictionCreateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function add_prediction(PredictionCreateRequest $request, $id)
    {
        $user = auth()->user();
        $league = League::findOrFail($id);
        $participant = Participant::where('user', $user->id)
            ->where('league', $league->id)
            ->first();
        $match = $league->Competition->matches()->find($request->validated()['match']);

        if(is_null($match)) {
            return response()->json([
                'message' => 'This Match does not belong to this League.',
            ], 400);
        }
        if(!$league->is_public && is_null($participant)) {
            return response()->json([
                'message' => 'This League is private.',
            ], 403);
        }

        // At this point the League is public
        if(is_null($participant)) {
            Participant::create([
                'user' => $user->id,
                'league' => $league->id,
            ]);
        }

        $prediction = new Prediction($request->validated());
        $prediction->participant = $participant->id;
        $prediction->save();

        return new PredictionResource($prediction);
    }
}
