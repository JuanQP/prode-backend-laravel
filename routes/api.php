<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::controller(AuthController::class)->group(function() {
    Route::post('/token', 'token');
    Route::post('/token/refresh', 'refresh');
    Route::post('/logout', 'logout');
    Route::get('/logged', 'me');
    Route::post('register', 'register');
});

Route::get('hello-world', function() {
    return response()->json([
        'message' => 'Hello world! 👋 This is the Prode backend API',
    ]);
});
