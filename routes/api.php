<?php

use App\Http\Controllers\SecretController;
use Illuminate\Support\Facades\Route;

Route::get('/secret/{hash}', [SecretController::class, 'getSecretByHash'])->where('hash', '.*');
Route::post('/secret', [SecretController::class, 'addSecret']);
