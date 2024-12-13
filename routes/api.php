<?php

use App\Http\Controllers\Api\ExchangeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(\App\Http\Middleware\CheckToken::class)->group(function () {
    Route::get('rates',    [ExchangeController::class, 'rates']);
    Route::post('convert', [ExchangeController::class, 'convertCurrency']);
});
