<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\KbliController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\PortalController;
use App\Http\Controllers\ApplicationController;

// Public Portal Routes
Route::get('/', [PortalController::class, 'index'])->name('home');
Route::get('/search', [PortalController::class, 'search'])->name('search');
Route::get('/kbli/{code}', [PortalController::class, 'show'])->name('kbli.show');

// Public News Routes
Route::get('/news', [PortalController::class, 'newsList'])->name('news.index');
Route::get('/news/{article:slug}', [PortalController::class, 'newsShow'])->name('news.show');



// Application & Tracking Routes
Route::middleware('auth')->group(function () {
    Route::get('/apply', [ApplicationController::class, 'create'])->name('application.create');
    Route::post('/apply', [ApplicationController::class, 'store'])->name('application.store')->middleware('throttle:5,1');
});
Route::get('/track', [ApplicationController::class, 'track'])->name('tracking.index')->middleware('throttle:15,1');
Route::get('/track/status', [ApplicationController::class, 'showTracking'])->name('tracking.show')->middleware('throttle:15,1');

// SEO Routes
Route::get('/sitemap.xml', function () {
    $kblis = \App\Models\KbliItem::where('is_active', true)->select('code')->get();
    $news = \App\Models\News::where('status', 'published')->select('slug', 'updated_at')->get();
    return response()->view('sitemap', compact('kblis', 'news'))->header('Content-Type', 'application/xml');
});

Route::get('/robots.txt', function () {
    $content = "User-agent: *\nDisallow: /admin/\nDisallow: /login\nAllow: /\nSitemap: " . url('/sitemap.xml');
    return response($content)->header('Content-Type', 'text/plain');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('throttle:6,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::get('/admin/documents/{media}/download', [\App\Http\Controllers\Admin\DocumentDownloadController::class, 'download'])
    ->name('admin.documents.download')
    ->middleware(['auth', 'role:admin,operator', 'throttle:30,1']);
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('kbli')->name('kbli.')->group(function () {
        Route::get('/categories', [KbliController::class, 'categories'])->name('categories');
        Route::get('/sections', [KbliController::class, 'sections'])->name('sections');
        Route::get('/items', [KbliController::class, 'items'])->name('items');
        Route::get('/items/{id}', [KbliController::class, 'showItem'])->name('items.show');
    });

    // Applications Review
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [\App\Http\Controllers\Admin\ApplicationController::class, 'show'])->name('show');
        Route::patch('/{application}/status', [\App\Http\Controllers\Admin\ApplicationController::class, 'updateStatus'])->name('update_status');
    });

    // News Management
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NewsController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NewsController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [\App\Http\Controllers\Admin\NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'destroy'])->name('destroy');
    });



    // User Management (Super Admin only)
    Route::prefix('users')->name('users.')->middleware('role:super-admin')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('resetPassword');
        Route::post('/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('toggleActive');
    });
});
