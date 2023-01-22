<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminOnly;
use App\Http\Requests\FinishMatchRequest;
use App\Http\Requests\MatchRequest;
use App\Http\Resources\MatchResource;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show', 'next_matches']]);
        $this->middleware(AdminOnly::class, ['except' => ['index', 'show', 'next_matches']]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MatchRequest $request)
    {
        $match = Game::create($request->validated());

        return new MatchResource($match);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new MatchResource(Game::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $match = Game::findOrFail($id);
        $match->update($request->validated());

        return new MatchResource($match);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Game::destroy($id);

        return response()->json([
            "message" => "Deleted",
        ]);
    }

    /**
     * Next matches to be played
     *
     * @return \Illuminate\Http\Response
     */
    public function next_matches()
    {
        $today = Carbon::now()->format('Y-m-d');
        $matches = Game::where('datetime', '>=', $today)
            ->orderByDesc('datetime')
            ->take(10)
            ->with('teamA', 'teamB')
            ->get();

        return MatchResource::collection($matches);
    }

    /**
     * Finishes a Match and updates all participants scores
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function finish(FinishMatchRequest $request, $id)
    {
        $match = Game::findOrFail($id);

        if($match->status == 'Finalizado') {
            return response()->json([
                'message' => 'Match won\'t update because it\'s finished.',
            ], 400);
        }

        $team_a_score = $request->validated()['team_a_score'];
        $team_b_score = $request->validated()['team_b_score'];
        $match->mark_as_finished($team_a_score, $team_b_score);

        return response()->json([
            'message' => "Match finished with result {$team_a_score}-{$team_b_score}",
        ]);
    }
}
