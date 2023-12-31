<?php

use App\Http\Controllers\InstitutionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('institution', InstitutionController::class)->only('show');
Route::middleware('auth:sanctum')->get('accepted-offers', [InstitutionController::class, 'acceptedOffers']);
