<?php

use App\Http\Controllers\TenderController;
use Illuminate\Support\Facades\Route;


Route::apiResource('/trender', TenderController::class)->except('store');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/trender', TenderController::class)->only('store');
    Route::get('/mine-trender', [TenderController::class, 'myTrenders']);
    Route::post('/trender/{tender}/close', [TenderController::class, 'close']);
});
