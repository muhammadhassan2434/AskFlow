<?php

use App\Http\Controllers\Auth\AuthController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function () {
    return Inertia::render('Dashboard');
});
Route::get('/signup', [AuthController::class, 'signUp']);
Route::get('/login', [AuthController::class, 'login']);