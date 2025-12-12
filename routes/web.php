<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


// routes/web.php
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
//Route::post('/auth/google/mobile', [AuthController::class, 'mobileGoogleAuth']);

require __DIR__.'/auth.php';
