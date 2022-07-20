<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', 'login')->name('login');
    Route::post('/register', 'register')->name('register');
    Route::post('/refresh', 'refresh')->name('refresh')->middleware('auth:api');
});

Route::apiResource('addresses', AddressController::class)
    ->middleware('auth:api');
