<?php

use App\Http\Controllers\Api\V1\CompteBancaireController;
use Illuminate\Http\Request;
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

// Routes API versionnées
Route::prefix('v1')->group(function () {

    // Routes pour les comptes bancaires
    Route::apiResource('comptes', CompteBancaireController::class);

    // Route alternative pour plus de clarté
    Route::get('comptes-bancaires', [CompteBancaireController::class, 'index']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
