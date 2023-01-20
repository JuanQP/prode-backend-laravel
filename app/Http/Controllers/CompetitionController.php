<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminOnly;
use App\Http\Requests\CompetitionRequest;
use App\Http\Resources\CompetitionDetailResource;
use App\Http\Resources\CompetitionResource;
use App\Imports\MatchesImport;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CompetitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
        $this->middleware(AdminOnly::class, ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CompetitionResource::collection(Competition::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompetitionRequest $request)
    {
        $competition = Competition::create($request->validated());

        return new CompetitionResource($competition);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new CompetitionDetailResource(Competition::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompetitionRequest $request, $id)
    {
        $competition = Competition::findOrFail($id);
        $competition->update($request->validated());

        return new CompetitionResource($competition);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Competition::destroy($id);

        return response()->json([
            "message" => "Deleted",
        ]);
    }

    public function csv_upload(Request $request, $id)
    {
        DB::transaction(function() use($request, $id) {
            Excel::import(new MatchesImport($id), $request->file);
        });

        return response()->json([
            "message" => "Se cargaron correctamente todos los partidos.",
        ]);
    }
}
