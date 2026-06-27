<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Bots\BotController;
use App\Http\Controllers\Workspace\WorkspaceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('guest')->group(function () {
    Route::get('/signup', [AuthController::class, 'signUp'])->name('signup');
    Route::post('/signup', [AuthController::class, 'storeSignup'])->name('signup.store');

    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'storeLogin'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('workspaces', WorkspaceController::class)->except(['show']);
        Route::resource('bots', BotController::class);


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});