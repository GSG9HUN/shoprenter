<?php

use App\Http\Controllers\SecretController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/v1')->group(function () {
    Route::get('/secret/{hash}', [SecretController::class, 'getSecretByHash'])->where('hash', '.*');
    Route::post('/secret', [SecretController::class, 'addSecret']);
});
