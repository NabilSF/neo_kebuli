<?php

namespace App\Services;

use App\DTOs\UserDashboardData;
use App\Models\Application;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Cache;

class UserDashboardService
{
    public function getDashboardData(int $userId): UserDashboardData
    {
        $cacheKey = \App\Helpers\CacheKey::userDashboard($userId);
        
        $payload = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId) {
            $counts = Application::selectRaw('count(*) as total')
                ->selectRaw("count(case when status in (?, ?) then 1 end) as pending", [\App\Enums\WorkflowState::SUBMITTED->value, \App\Enums\WorkflowState::UNDER_REVIEW->value])
                ->selectRaw("count(case when status = ? then 1 end) as revision", [\App\Enums\WorkflowState::NEED_REVISION->value])
                ->selectRaw("count(case when status = ? then 1 end) as approved", [\App\Enums\WorkflowState::APPROVED->value])
                ->selectRaw("count(case when status = ? then 1 end) as rejected", [\App\Enums\WorkflowState::REJECTED->value])
                ->where('user_id', $userId)
                ->first();

            $recentApplications = Application::with(['kbliItem' => function ($q) {
                    $q->select('id', 'code', 'name');
                }])
                ->select(['id', 'tracking_code', 'product_name', 'status', 'kbli_item_id', 'created_at', 'updated_at'])
                ->where('user_id', $userId)
                ->latest()
                ->take(5)
                ->get()
                ->map(function($app) {
                    return [
                        'id' => $app->id,
                        'tracking_code' => $app->tracking_code,
                        'product_name' => $app->product_name,
                        'kbli_code' => $app->kbliItem?->code,
                        'kbli_name' => $app->kbliItem?->name,
                        'created_at' => $app->created_at?->toIso8601String(),
                        'updated_at' => $app->updated_at?->toIso8601String(),
                        'status' => $app->status->value,
                    ];
                })->toArray();

            $recentActivities = ActivityLog::select(['id', 'action', 'properties', 'created_at', 'user_id'])
                ->where('user_id', $userId)
                ->latest()
                ->take(5)
                ->get()
                ->map(function($act) {
                    return [
                        'action' => $act->action,
                        'notes' => $act->properties['notes'] ?? null,
                        'created_at' => $act->created_at?->toIso8601String(),
                    ];
                })->toArray();

            return [
                'totalApplications' => $counts->total ?? 0,
                'pendingReview'     => $counts->pending ?? 0,
                'needRevision'      => $counts->revision ?? 0,
                'approved'          => $counts->approved ?? 0,
                'rejected'          => $counts->rejected ?? 0,
                'recentApplications' => $recentApplications,
                'recentActivities'  => $recentActivities,
            ];
        });

        // Reconstruct collections for Blade compatibility
        $appsCollection = collect($payload['recentApplications'])->map(function($app) {
            $obj = new \stdClass();
            $obj->id = $app['id'];
            $obj->tracking_code = $app['tracking_code'];
            $obj->product_name = $app['product_name'];
            $obj->kbliItem = new \stdClass();
            $obj->kbliItem->code = $app['kbli_code'];
            $obj->kbliItem->name = $app['kbli_name'];
            $obj->created_at = \Carbon\Carbon::parse($app['created_at']);
            $obj->updated_at = \Carbon\Carbon::parse($app['updated_at']);
            $obj->status = \App\Enums\WorkflowState::tryFrom($app['status']) ?? \App\Enums\WorkflowState::SUBMITTED;
            return $obj;
        });

        $actsCollection = collect($payload['recentActivities'])->map(function($act) {
            $obj = new \stdClass();
            $obj->action = $act['action'];
            $obj->properties = ['notes' => $act['notes']];
            $obj->created_at = \Carbon\Carbon::parse($act['created_at']);
            return $obj;
        });

        return new UserDashboardData(
            $payload['totalApplications'],
            $payload['pendingReview'],
            $payload['needRevision'],
            $payload['approved'],
            $payload['rejected'],
            $appsCollection,
            $actsCollection
        );
    }
}
