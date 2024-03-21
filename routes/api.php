<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\pollController;

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


Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login',  [AuthController::class, 'login']);
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::post('/refresh',  [AuthController::class, 'refresh']);
    Route::post('/me',  [AuthController::class, 'me']);
    Route::post('/pass_generator', [AuthController::class, 'password_generator']);
    Route::post('/reset_password', [AuthController::class, 'reset_password']);
    Route::post('/is_default_password', [AuthController::class, 'is_default_password']);
});


// Route::get('/is_default_password', [AuthController::class, 'is_default_password']);

Route::group(['prefix' => "poll", 'middleware' => 'api'], function($router) {
    Route::get('/', [pollController::class, 'get']);
    Route::get('/{id}', [pollController::class, 'detail']);

    Route::post('/create', [pollController::class, 'create']);
    Route::delete('/{id}', [pollController::class, 'delete']);
    Route::post('/{poll_id}/vote/{choice_id}', [pollController::class, 'vote']);
});

