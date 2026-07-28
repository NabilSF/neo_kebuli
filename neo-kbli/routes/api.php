<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KbliApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ApplicationApiController;

Route::prefix('v1')->group(function () {
    // Auth Public Endpoints
    Route::post('/auth/register', [AuthApiController::class, 'register'])->name('api.v1.auth.register');
    Route::post('/auth/login', [AuthApiController::class, 'login'])->name('api.v1.auth.login');

    // Public Application Endpoints
    Route::get('/applications/track/{tracking_code}', [ApplicationApiController::class, 'track'])->name('api.v1.applications.track');
    Route::get('/applications/statuses', [ApplicationApiController::class, 'statuses'])->name('api.v1.applications.statuses');

    // Auth Private Endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthApiController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [AuthApiController::class, 'logout'])->name('api.v1.auth.logout');

        // Protected Application Endpoints
        Route::get('/applications', [ApplicationApiController::class, 'index'])->name('api.v1.applications.index');
        Route::post('/applications', [ApplicationApiController::class, 'store'])->name('api.v1.applications.store');
        Route::get('/applications/{id}', [ApplicationApiController::class, 'show'])->name('api.v1.applications.show');
        Route::post('/applications/{id}/media', [ApplicationApiController::class, 'uploadMedia'])->name('api.v1.applications.upload_media');
    });

    // KBLI Public Endpoints
    Route::get('/kbli/categories', [KbliApiController::class, 'categories'])->name('api.v1.kbli.categories');
    Route::get('/kbli/search', [KbliApiController::class, 'search'])->name('api.v1.kbli.search');
    Route::get('/kbli/{code}', [KbliApiController::class, 'show'])->name('api.v1.kbli.show');

    // News Public Endpoints
    Route::get('/news', [NewsApiController::class, 'index'])->name('api.v1.news.index');
    Route::get('/news/{slug}', [NewsApiController::class, 'show'])->name('api.v1.news.show');
});
