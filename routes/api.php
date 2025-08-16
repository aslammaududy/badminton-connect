<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register'])->middleware('throttle:register');
    Route::post('login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login'])->middleware('throttle:login');

    Route::apiResource('courts', \App\Http\Controllers\Api\V1\CourtController::class);
    Route::apiResource('bookings', \App\Http\Controllers\Api\V1\BookingController::class);
    Route::apiResource('tournaments', \App\Http\Controllers\Api\V1\TournamentController::class);
    Route::apiResource('matches', \App\Http\Controllers\Api\V1\MatchController::class);
    Route::apiResource('partner-requests', \App\Http\Controllers\Api\V1\PartnerRequestController::class);

    // Example protected route (requires Bearer token from Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::get('me', function (\Illuminate\Http\Request $request) {
            return $request->user();
        });
    });
});
