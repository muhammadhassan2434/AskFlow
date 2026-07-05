<?php

use App\Http\Controllers\Api\BotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('ai')->middleware('ai.key')->group(function () {

        Route::get('/bots/{bot}',[BotController::class, 'show']);

    });
