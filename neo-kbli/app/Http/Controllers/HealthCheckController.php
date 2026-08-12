<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\App;
use Exception;

class HealthCheckController extends Controller
{
    /**
     * Endpoint to check application health for deployment monitoring.
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $components = [];

        // 1. Database Check
        try {
            DB::connection()->getPdo();
            $components['database'] = 'connected';
        } catch (Exception $e) {
            $components['database'] = 'unhealthy';
            $status = 'unhealthy';
        }

        // 2. Cache Check
        try {
            Cache::store()->set('health_check', 'ok', 10);
            $cacheStatus = Cache::store()->get('health_check') === 'ok' ? 'connected' : 'unhealthy';
            $components['cache'] = $cacheStatus;
            if ($cacheStatus === 'unhealthy') $status = 'unhealthy';
        } catch (Exception $e) {
            $components['cache'] = 'unhealthy';
            $status = 'unhealthy';
        }

        // 3. Queue Check (Basic connection check)
        try {
            $queueConnection = config('queue.default');
            $components['queue'] = 'configured: ' . $queueConnection;
        } catch (Exception $e) {
            $components['queue'] = 'unhealthy';
            $status = 'unhealthy';
        }

        // 4. Environment Variables & App Info
        $environment = [
            'app_env' => App::environment(),
            'app_debug' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'version' => app()->version(),
            'php_version' => PHP_VERSION,
        ];

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'components' => $components,
            'environment' => $environment,
        ], $status === 'healthy' ? 200 : 503);
    }
}
