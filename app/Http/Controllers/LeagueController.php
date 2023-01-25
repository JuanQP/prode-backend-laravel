<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeagueCreateRequest;
use App\Http\Requests\LeagueUpdateRequest;
use App\Http\Requests\PredictionCreateRequest;
use App\Http\Resources\LeagueDetailResource;
use App\Http\Resources\LeaguePaginatedSearchResultResource;
use App\Http\Resources\LeagueResource;
use App\Http\Resources\MatchResource;
use App\Http\Resources\MyLeagueResource;
use App\Http\Resources\PredictionResource;
use App\Models\League;
use App\Models\Participant;
use App\Models\Prediction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'index',
            'show',
            'can_join',
            'next_matches',
            'matches',
            'search',
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

    /**
     * Returns if the current user (guest or not) can join this League.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function can_join($id)
    {
        $user = auth()->user();
        if(is_null($user)) {
            return response()->json([
                'can_join' => false,
                'is_participant' => false,
                'message' => 'Tenés que estar logueado.',
            ]);
        }

        $league = League::findOrFail($id);
        $is_participant = $league
            ->participants()
            ->where('user', $user->id)
            ->exists();
        $has_pending_join_request = $league
            ->join_requests()
            ->where('user', $user->id)
            ->whereNull('accepted')
            ->exists();
        $message = 'Podés unirte';

        if ($has_pending_join_request) {
            $message = 'Tenés una solicitud para unirte pendiente.';
        }
        else if ($is_participant) {
            $message = 'Ya estás participando de esta liga.';
        }

        return response()->json([
            'can_join' => !$has_pending_join_request && !$is_participant,
            'is_participant' => $is_participant,
            'message' => $message,
        ]);
    }

    /**
     * Returns the next matches of a League
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function next_matches($id)
    {
        $league = League::findOrFail($id);
        $today = Carbon::now()->format('Y-m-d');
        $matches = $league->Competition->matches()->where('datetime', '>=', $today)->get();
        $matches->load('teamA', 'teamB');

        return MatchResource::collection($matches);
    }

    /**
     * Returns all the matches of a League
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function matches($id)
    {
        $league = League::findOrFail($id);
        $matches = $league->Competition->matches()->get();
        $matches->load('teamA', 'teamB');

        return MatchResource::collection($matches);
    }

    /**
     * Returns the predictions of a user in a League
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function my_predictions($id)
    {
        $user = auth()->user();
        $league = League::findOrFail($id);
        $participant = $league
            ->participants()
            ->where('user', $user->id)
            ->where('league', $league->id)
            ->first();
        if (is_null($participant) && $league->is_public) {
            $empty_array = (object) [];
            return response()->json($empty_array);
        }
        else if (is_null($participant) && !$league->is_public) {
            return response()->json([
                'message' => 'No sos parte de esta liga.',
            ], 403);
        }

        // This user is a Participant of this League
        $predictions = $participant->predictions()->get();
        return PredictionResource::collection($predictions);
    }

    /**
     * Returns user created leagues with their join requests
     *
     * @return \Illuminate\Http\Response
     */
    public function my_leagues()
    {
        $user = auth()->user();
        $leagues = League::where('owner', $user->id)
            ->with('Competition', 'Owner', 'join_requests', 'participants')
            ->get();

        return MyLeagueResource::collection($leagues);
    }

    /**
     * Returns a user created league with their join requests
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function my_league($id)
    {
        $user = auth()->user();
        $league = League::findOrFail($id)
            ->with('Competition', 'Owner', 'join_requests', 'participants')
            ->get();

        if (!$user->is_staff && $league->Owner->id != $user->id) {
            return response()->json(['message' => 'This League is not yours.'], 403);
        }

        return new MyLeagueResource($league);
    }

    /**
     * Searches Leagues by name or competition name
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $search = $request->query('search') ?? '';
        $page = $request->query('page');

        $leagues = League::where('name', 'ilike', "%{$search}%")
            ->orWhereRelation('competition', 'name', 'ilike', "%{$search}%")
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'page', $page);

        // Search and page query params
        return new LeaguePaginatedSearchResultResource($leagues);
    }
}
