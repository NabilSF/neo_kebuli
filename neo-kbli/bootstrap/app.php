<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        
        $middleware->preventRequestForgery(except: [
            //
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\SecureHeaders::class,
        ]);

        $middleware->redirectUsersTo(function () {
            if (auth()->check()) {
                if (auth()->user()->hasAnyRole(['Super Admin', 'super-admin', 'Admin', 'Administrator', 'Operator'])) {
                    return route('admin.dashboard');
                }
            }
            return route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sesi Anda telah berakhir. Silakan login kembali (401).',
                    ], 401);
                }
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki hak akses (403).',
                    ], 403);
                }
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sumber daya atau endpoint tidak ditemukan (404).',
                    ], 404);
                }
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi input gagal (422).',
                        'errors'  => $e->errors(),
                    ], 422);
                }
                return response()->json([
                    'success' => false,
                    'message' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan internal pada server (500).',
                ], 500);
            }
        });
    })->create();
