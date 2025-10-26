<?php

use App\Http\Controllers\Api\V1\AuthController;
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

    // Routes d'authentification (publiques)
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('auth/user', [AuthController::class, 'user'])->middleware('auth:api');

    // Routes pour les comptes bancaires (protégées)
    Route::middleware(['auth:api'])->group(function () {
        Route::get('comptes', [CompteBancaireController::class, 'index']);
        Route::get('comptes/{compte}', [CompteBancaireController::class, 'show']);
        Route::put('comptes/{compte}', [CompteBancaireController::class, 'update']);
        Route::delete('comptes/{compte}', [CompteBancaireController::class, 'destroy']);

        // Route alternative pour plus de clarté
        Route::get('comptes-bancaires', [CompteBancaireController::class, 'index']);
    });

    // Route publique pour créer un compte bancaire (sans authentification)
    Route::post('comptes', [CompteBancaireController::class, 'store']);

    // Routes admin seulement (avec vérification de rôle)
    Route::middleware(['auth:api', 'auth.middleware', 'role:admin'])->group(function () {
        // Routes spécifiques aux administrateurs
        Route::get('admin/users', function () {
            return response()->json(['message' => 'Liste des utilisateurs (admin seulement)']);
        });
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
