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

  Route::post('/login', [SocialAuthController::class, 'login']);

// Admin-Only Routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureUserIsAdmin::class])->group(function () {
    Route::post('/official-reviews', [DebaterReviewController::class, 'store']);
    Route::put('/debaters/{id}/official-score', [DebaterController::class, 'updateOfficialScore']);
});


// Super Admin Only Routes
Route::middleware([\App\Http\Middleware\EnsureUserIsSuperAdmin::class])->group(function () {
    Route::post('/users', [\App\Http\Controllers\Api\Admin\UserAdminController::class, 'store']); // <--- ADD THIS
    Route::put('/users/{id}/role', [\App\Http\Controllers\Api\Admin\UserAdminController::class, 'updateRole']);
    Route::delete('/users/{id}', [\App\Http\Controllers\Api\Admin\UserAdminController::class, 'destroy']);
});

// Admin Management Routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureUserIsAdmin::class])->prefix('admin')->group(function () {
    
    // Debaters Management
    Route::get('/debaters', [\App\Http\Controllers\Api\Admin\DebaterAdminController::class, 'index']);
    Route::post('/debaters', [\App\Http\Controllers\Api\Admin\DebaterAdminController::class, 'store']);
    Route::put('/debaters/{id}', [\App\Http\Controllers\Api\Admin\DebaterAdminController::class, 'update']);
    Route::delete('/debaters/{id}', [\App\Http\Controllers\Api\Admin\DebaterAdminController::class, 'destroy']);

    // Official Reviews & Scores Management
    Route::post('/debaters/{id}/official-review', [\App\Http\Controllers\Api\Admin\OfficialReviewAdminController::class, 'storeOrUpdate']);
    Route::post('/debaters/{id}/official-score', [\App\Http\Controllers\Api\Admin\OfficialScoreAdminController::class, 'storeOrUpdate']);

    // Audience Reviews Moderation
    Route::get('/audience-reviews', [\App\Http\Controllers\Api\Admin\AudienceReviewAdminController::class, 'index']);
    Route::delete('/audience-reviews/{id}', [\App\Http\Controllers\Api\Admin\AudienceReviewAdminController::class, 'destroy']);

    // Users Management (Super Admin Only for role changes)
    Route::get('/users', [\App\Http\Controllers\Api\Admin\UserAdminController::class, 'index']);
   
  
});