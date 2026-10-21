<?php

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DebaterController;
use App\Http\Controllers\Api\DebaterReviewController;
use Illuminate\Http\Request; 

// Debaters & Leaderboard
Route::get('/debaters', [DebaterController::class, 'index']);
Route::get('/debaters/{debater}/reviews', [DebaterController::class, 'audienceReviews']);

// Written Debate Reviews & Social Media Links
Route::get('/debater-reviews', [DebaterReviewController::class, 'index']);
Route::post('/debater-reviews', [DebaterReviewController::class, 'store']);
Route::post('/debater-reviews/{id}/like', [DebaterReviewController::class, 'like']);
Route::get('/debaters/{id}', [DebaterController::class, 'show']);

// Social Auth Endpoints
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

// Authenticated Audience Routes (Logged-in users)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/debaters/{debater}/reviews', [DebaterController::class, 'storeAudienceReview']);
    Route::put('/audience-reviews/{review}', [DebaterController::class, 'updateAudienceReview']);
    Route::delete('/audience-reviews/{review}', [DebaterController::class, 'destroyAudienceReview']);
    Route::get('/user/me', function (Request $request) {
        return $request->user();
    });
});

// Admin-Only Routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureUserIsAdmin::class])->group(function () {
    Route::post('/official-reviews', [DebaterReviewController::class, 'store']);
    Route::put('/debaters/{id}/official-score', [DebaterController::class, 'updateOfficialScore']);
});