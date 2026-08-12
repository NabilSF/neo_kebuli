<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->validateEnvironment();

        \Illuminate\Validation\Rules\Password::defaults(function () {
            return \Illuminate\Validation\Rules\Password::min(8)
                ->letters()
                ->numbers();
        });

        // Inject Request ID for distributed tracing and observability
        \Illuminate\Support\Facades\Log::shareContext([
            'request_id' => \Illuminate\Support\Str::uuid()->toString(),
            'env' => config('app.env'),
        ]);

        \Illuminate\Support\Facades\Event::subscribe(
            \App\Listeners\ApplicationEventSubscriber::class
        );
        \Illuminate\Support\Facades\Event::subscribe(
            \App\Listeners\DashboardCacheSubscriber::class
        );
    }

    /**
     * Ensure critical configurations exist for production stability.
     */
    private function validateEnvironment(): void
    {
        if ($this->app->environment('production')) {
            $required = [
                'app.url' => config('app.url'),
                'app.key' => config('app.key'),
                'database.connections.mysql.host' => config('database.connections.mysql.host'),
                'cache.default' => config('cache.default'),
                'session.driver' => config('session.driver'),
            ];
            
            foreach ($required as $key => $value) {
                if (empty($value)) {
                    throw new \RuntimeException("Production Environment Validation Failed: Konfigurasi penting '{$key}' tidak ditemukan. Harap periksa file .env atau konfigurasi Anda.");
                }
            }
        }
    }
}
