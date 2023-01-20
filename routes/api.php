<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\TeamController;

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
