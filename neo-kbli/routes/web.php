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
use App\Http\Controllers\HealthCheckController;

// Observability Routes
Route::get('/health', [HealthCheckController::class, 'check'])->name('health');

// Public Portal Routes
Route::get('/', [PortalController::class, 'index'])->name('home');
Route::get('/search', [PortalController::class, 'search'])->name('search');
Route::get('/kbli/{code}', [PortalController::class, 'show'])->name('kbli.show');

// Public News Routes
Route::get('/news', [PortalController::class, 'newsList'])->name('news.index');
Route::get('/news/{article:slug}', [PortalController::class, 'newsShow'])->name('news.show');



// Application & Tracking Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware(\App\Http\Middleware\RedirectAdmin::class);
    Route::get('/applications', [\App\Http\Controllers\User\ApplicationController::class, 'index'])->name('user.applications.index');
    Route::get('/applications/{application}', [\App\Http\Controllers\User\ApplicationController::class, 'show'])->name('user.applications.show');
    Route::post('/applications/{application}/media', [\App\Http\Controllers\User\ApplicationController::class, 'uploadMedia'])->name('user.applications.upload-media');
    Route::delete('/applications/{application}/media/{media}', [\App\Http\Controllers\User\ApplicationController::class, 'deleteMedia'])->name('user.applications.delete-media');
    Route::get('/applications/{application}/media/{media}/download', [\App\Http\Controllers\User\ApplicationController::class, 'downloadMedia'])->name('user.applications.download-media')->middleware('throttle:30,1');
    Route::get('/apply', [ApplicationController::class, 'create'])->name('application.create');
    Route::post('/apply', [ApplicationController::class, 'store'])->name('application.store')->middleware('throttle:5,1');

    Route::get('/notifications', [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('user.notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\User\NotificationController::class, 'markAllAsRead'])->name('user.notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\User\NotificationController::class, 'markAsRead'])->name('user.notifications.mark-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\User\NotificationController::class, 'destroy'])->name('user.notifications.destroy');

    Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'index'])->name('user.profile.index');
    Route::patch('/profile', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('user.profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\User\ProfileController::class, 'updatePassword'])->name('user.password.update');
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
    ->middleware(['auth', 'throttle:30,1']);
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Helpers\RoleList::ADMIN_ACCESS])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('kbli')->name('kbli.')->group(function () {
        Route::get('/categories', [KbliController::class, 'categories'])->name('categories');
        Route::get('/sections', [KbliController::class, 'sections'])->name('sections');
        Route::get('/items', [KbliController::class, 'index'])->name('items.index');
        Route::get('/items/create', [KbliController::class, 'create'])->name('items.create');
        Route::post('/items', [KbliController::class, 'store'])->name('items.store');
        Route::get('/items/{item}', [KbliController::class, 'show'])->name('items.show');
        Route::get('/items/{item}/edit', [KbliController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [KbliController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [KbliController::class, 'destroy'])->name('items.destroy');
        Route::post('/items-bulk', [KbliController::class, 'bulkUpdate'])->name('items.bulk-update');
        Route::get('/import/template/{format}', [KbliController::class, 'template'])->name('import.template');
        Route::get('/import', [KbliController::class, 'importForm'])->name('import.form');
        Route::get('/export', [KbliController::class, 'export'])->name('export');
        Route::post('/import/preview', [KbliController::class, 'importPreview'])->name('import.preview');
        Route::post('/import/confirm', [KbliController::class, 'importConfirm'])->name('import.confirm');
    });

    // Applications Review
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApplicationController::class, 'index'])
            ->name('index')->middleware('can:viewAny,App\Models\Application');
            
        Route::get('/{application}', [\App\Http\Controllers\Admin\ApplicationController::class, 'show'])
            ->name('show')->middleware('can:view,application');
            
        Route::post('/{application}/memo', [\App\Http\Controllers\Admin\ApplicationController::class, 'addMemo'])
            ->name('add_memo')->middleware('can:view,application');
            
        Route::post('/{application}/assign', [\App\Http\Controllers\Admin\AssignmentController::class, 'store'])
            ->name('assign')->middleware('can:assign,application');
            
        Route::post('/{application}/approve', [\App\Http\Controllers\Admin\ApprovalController::class, 'store'])
            ->name('approve')->middleware('can:approve,application');
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
    Route::prefix('users')->name('users.')->middleware(\App\Helpers\RoleList::SUPER_ADMIN_ACCESS)->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('resetPassword');
        Route::post('/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('toggleActive');
    });

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
});
