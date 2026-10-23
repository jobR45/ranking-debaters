<?php

use App\Http\Controllers\Api\Admin\AudienceReviewAdminController;
use App\Http\Controllers\Api\Admin\DebaterAdminController;
use App\Http\Controllers\Api\Admin\OfficialReviewAdminController;
use App\Http\Controllers\Api\Admin\OfficialScoreAdminController;
use App\Http\Controllers\Api\Admin\UserAdminController;
use App\Http\Controllers\Api\DebaterController;
use App\Http\Controllers\Api\DebaterReviewController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsSuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('/login', [SocialAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [SocialAuthController::class, 'logout']);


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
Route::middleware(['auth:sanctum', EnsureUserIsAdmin::class])->group(function () {
    Route::post('/official-reviews', [DebaterReviewController::class, 'store']);
    Route::put('/debaters/{id}/official-score', [DebaterController::class, 'updateOfficialScore']);
});

// Super Admin Only Routes
Route::middleware([EnsureUserIsSuperAdmin::class])->prefix('admin')->group(function () {
    Route::post('/users', [UserAdminController::class, 'store']); // <--- ADD THIS
    Route::put('/users/{id}/role', [UserAdminController::class, 'updateRole']);
    Route::delete('/users/{id}', [UserAdminController::class, 'destroy']);
});

// Admin Management Routes
Route::middleware(['auth:sanctum', EnsureUserIsAdmin::class])->prefix('admin')->group(function () {

    // Debaters Management
    Route::get('/debaters', [DebaterAdminController::class, 'index']);
    Route::get('/debaters/{debater}', [DebaterAdminController::class, 'show']);
    Route::post('/debaters', [DebaterAdminController::class, 'store']);
    Route::put('/debaters/{debater}', [DebaterAdminController::class, 'update']);
    Route::delete('/debaters/{id}', [DebaterAdminController::class, 'destroy']);

    // Official Reviews & Scores Management
    Route::post('/debaters/{id}/official-review', [OfficialReviewAdminController::class, 'storeOrUpdate']);
    Route::post('/debaters/{id}/official-score', [OfficialScoreAdminController::class, 'storeOrUpdate']);

    // Audience Reviews Moderation
    Route::get('/audience-reviews', [AudienceReviewAdminController::class, 'index']);
    Route::delete('/audience-reviews/{id}', [AudienceReviewAdminController::class, 'destroy']);

    // Users Management (Super Admin Only for role changes)
    Route::get('/users', [UserAdminController::class, 'index']);

});
