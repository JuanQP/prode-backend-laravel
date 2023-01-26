<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\JoinRequestController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('hello-world', function() {
    return response()->json([
        'message' => 'Hello world! 👋 This is the Prode backend API',
    ]);
});

// Auth routes
Route::controller(AuthController::class)->group(function() {
    Route::post('/token', 'token');
    Route::post('/token/refresh', 'refresh');
    Route::post('/logout', 'logout');
    Route::get('/logged', 'me');
    Route::post('register', 'register');
});

// First the custom routes, then the resource routes
// https://laravel.com/docs/9.x/controllers#restful-supplementing-resource-controllers
Route::post('/teams/csv_upload', [TeamController::class, 'csv_upload']);
Route::apiResource('teams', TeamController::class);

Route::post('/competitions/{id}/csv_upload', [CompetitionController::class, 'csv_upload']);
Route::apiResource('competitions', CompetitionController::class);

Route::controller(ParticipantController::class)->group(function() {
    Route::get('/participants/ranking', 'ranking');
    Route::get('/participants/my_participations', 'my_participations');
});

Route::controller(UserController::class)->group(function() {
    Route::get('/users/logged', 'logged');
    Route::get('/users/me', 'me');
    Route::match(['PUT', 'PATCH'], '/users/me', 'update_me');
    Route::post('/users/{id}/change_password', 'change_password');
});
Route::apiResource('users', UserController::class)->only(['show']);

Route::controller(MatchController::class)->group(function() {
    Route::get('/matches/next_matches', 'next_matches');
    Route::post('/matches/{id}/finish', 'finish');
});
Route::apiResource('matches', MatchController::class);
Route::apiResource('predictions', PredictionController::class)->only(['show', 'update']);
Route::apiResource('join-requests', JoinRequestController::class)->only(['store', 'update']);

Route::controller(LeagueController::class)->group(function() {
    Route::post('/leagues/{id}/add_prediction', 'add_prediction');
    Route::get('/leagues/{id}/can_join', 'can_join');
    Route::get('/leagues/{id}/next_matches', 'next_matches');
    Route::get('/leagues/{id}/matches', 'matches');
    Route::get('/leagues/{id}/my_predictions', 'my_predictions');
    Route::get('/leagues/{id}/my_league', 'my_league');
    Route::get('/leagues/my_leagues', 'my_leagues');
    Route::get('/leagues-search/', 'search');
});
Route::apiResource('leagues', LeagueController::class);
