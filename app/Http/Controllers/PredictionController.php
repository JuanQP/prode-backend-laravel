<?php

namespace App\Http\Controllers;

use App\Http\Requests\PredictionRequest;
use App\Http\Resources\PredictionResource;
use App\Http\Resources\PredictionUpdateResource;
use App\Models\Prediction;

class PredictionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['show']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prediction = Prediction::with('Match', 'Participant')->findOrFail($id);

        return new PredictionResource($prediction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PredictionRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PredictionRequest $request, $id)
    {
        $user = auth()->user();
        $prediction = Prediction::findOrFail($id);

        if(!$user->is_staff && !$prediction->is_owner($user)) {
            return response()->json(['message' => 'This Prediction is not yours.'], 401);
        }
        if($prediction->Match->match_started_or_finished()) {
            return response()->json(['message' => 'You can\'t update this Prediction because game has started or has finished.'], 401);
        }

        $prediction->update($request->validated());

        return new PredictionUpdateResource($prediction);
    }
}
