<?php

namespace App\Services\Analytics;

use App\DTOs\DashboardStatisticsDTO;
use App\Models\ActivityLog;
use App\Models\KbliItem;
use App\Models\Application;
use App\Models\User;
use App\Enums\WorkflowState;
use App\Helpers\CacheKey;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    public function __construct(
        private ApplicationMetricsAggregator $aggregator
    ) {}

    public function getDashboardStatistics(): DashboardStatisticsDTO
    {
        $cacheKey = CacheKey::analyticsDashboard('statistics');
        $data = Cache::remember($cacheKey, config('analytics.cache_ttl.statistics', 60), function () {
            return $this->aggregator->aggregateDashboardStats()->toArray();
        });

        if ($data instanceof DashboardStatisticsDTO) {
            return $data;
        }

        if (is_array($data)) {
            return DashboardStatisticsDTO::fromArray($data);
        }

        // Self-healing: if an old invalid cache entry exists, flush only this key and recompute
        $cacheKey = CacheKey::analyticsDashboard('statistics');
        Cache::forget($cacheKey);
        $stats = $this->aggregator->aggregateDashboardStats();
        Cache::put($cacheKey, $stats->toArray(), config('analytics.cache_ttl.statistics', 60));

        return $stats;
    }

    public function getSystemStatus(): string
    {
        $cacheKey = CacheKey::analyticsDashboard('status');
        return Cache::remember($cacheKey, config('analytics.cache_ttl.status', 300), function () {
            $activeItems = KbliItem::where('is_active', true)->count();
            return $activeItems > 0 ? 'Beroperasi' : 'Perlu Konfigurasi';
        });
    }

    public function getRecentActivities(int $limit = 5)
    {
        $cacheKey = CacheKey::analyticsDashboard('activities', $limit);
        $raw = Cache::remember($cacheKey, config('analytics.cache_ttl.activities', 30), function () use ($limit) {
            return ActivityLog::with(['user' => function ($q) { $q->select('id', 'name'); }])
                ->select(['id', 'action', 'properties', 'created_at', 'user_id'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($activity) {
                    $description = '';
                    $value = $activity->properties;
                    $props = is_array($value) ? $value : (is_string($value) ? json_decode($value, true) : []);
                    $props = is_array($props) ? $props : [];
                    
                    if (isset($props['description'])) {
                        $description = $props['description'];
                    }
                    if (!$description) {
                        $description = $activity->action;
                    }

                    return [
                        'userName'    => $activity->user ? $activity->user->name : 'Sistem',
                        'description' => $description,
                        'created_at'  => $activity->created_at ? $activity->created_at->toIso8601String() : null,
                    ];
                })
                ->toArray();
        });

        if (!is_array($raw)) {
            Cache::forget(CacheKey::analyticsDashboard('activities', $limit));
            return collect([]);
        }

        return collect($raw)->map(function ($item) {
            if ($item instanceof \App\DTOs\RecentActivityDTO) {
                return $item;
            }
            if (is_array($item)) {
                return \App\DTOs\RecentActivityDTO::fromArray($item);
            }
            return new \App\DTOs\RecentActivityDTO(
                userName: 'Sistem',
                description: (string) $item,
                created_at: null
            );
        });
    }

    public function getApprovalQueue(int $limit = 5)
    {
        $cacheKey = CacheKey::analyticsDashboard('queue', $limit);
        $raw = Cache::remember($cacheKey, config('analytics.cache_ttl.queue', 60), function () use ($limit) {
            return Application::with(['user' => function ($q) { $q->select('id', 'name'); }, 'kbliItem' => function ($q) { $q->select('id', 'code'); }])
                ->select(['id', 'tracking_code', 'user_id', 'kbli_item_id', 'submitted_at'])
                ->where('status', WorkflowState::UNDER_REVIEW->value)
                ->orderBy('submitted_at', 'asc')
                ->limit($limit)
                ->get()
                ->map(function ($app) {
                    return [
                        'tracking_code' => $app->tracking_code,
                        'userName'      => $app->user ? $app->user->name : 'Anonim',
                        'kbliCode'      => $app->kbliItem ? $app->kbliItem->code : '-',
                        'submitted_at'  => $app->submitted_at ? $app->submitted_at->toIso8601String() : null,
                    ];
                })
                ->toArray();
        });

        if (!is_array($raw)) {
            Cache::forget(CacheKey::analyticsDashboard('queue', $limit));
            return collect([]);
        }

        return collect($raw)->map(function ($item) {
            if ($item instanceof \App\DTOs\ApprovalQueueDTO) {
                return $item;
            }
            if (is_array($item)) {
                return \App\DTOs\ApprovalQueueDTO::fromArray($item);
            }
            return new \App\DTOs\ApprovalQueueDTO(
                tracking_code: (string) $item,
                userName: 'Anonim',
                kbliCode: '-',
                submitted_at: null
            );
        });
    }

    public function getReviewerWorkload()
    {
        $cacheKey = CacheKey::analyticsDashboard('reviewers');
        $raw = Cache::remember($cacheKey, config('analytics.cache_ttl.reviewers', 60), function () {
            return Application::with(['reviewer' => function ($q) { $q->select('id', 'name'); }])
                ->where('status', WorkflowState::UNDER_REVIEW->value)
                ->whereNotNull('reviewed_by')
                ->selectRaw('reviewed_by, count(*) as count')
                ->groupBy('reviewed_by')
                ->get()
                ->map(function ($item) {
                    return [
                        'reviewer_name'  => $item->reviewer ? $item->reviewer->name : 'Reviewer #' . $item->reviewed_by,
                        'assigned_count' => (int) $item->count,
                    ];
                })
                ->toArray();
        });

        if (!is_array($raw)) {
            Cache::forget(CacheKey::analyticsDashboard('reviewers'));
            return collect([]);
        }

        return collect($raw)->map(function ($item) {
            if ($item instanceof \App\DTOs\ReviewerWorkloadDTO) {
                return $item;
            }
            if (is_array($item)) {
                return \App\DTOs\ReviewerWorkloadDTO::fromArray($item);
            }
            if (is_object($item)) {
                return new \App\DTOs\ReviewerWorkloadDTO(
                    reviewer_name: $item->reviewer_name ?? 'Unknown',
                    assigned_count: (int) ($item->assigned_count ?? 0)
                );
            }
            return new \App\DTOs\ReviewerWorkloadDTO(reviewer_name: (string) $item, assigned_count: 0);
        });
    }

    public function getRecentApplications(int $limit = 10)
    {
        $cacheKey = CacheKey::analyticsDashboard('recent_apps', $limit);
        $raw = Cache::remember($cacheKey, config('analytics.cache_ttl.recent_apps', 60), function () use ($limit) {
            return Application::with(['user' => function ($q) { $q->select('id', 'name'); }, 'kbliItem' => function ($q) { $q->select('id', 'code'); }])
                ->select(['id', 'tracking_code', 'product_name', 'user_id', 'status', 'submitted_at', 'created_at', 'kbli_item_id'])
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'tracking_code' => $app->tracking_code,
                        'product_name'  => $app->product_name,
                        'userName'      => $app->user ? $app->user->name : 'Anonim',
                        'status'        => $app->status->value,
                        'submitted_at'  => $app->submitted_at ? $app->submitted_at->toIso8601String() : null,
                        'created_at'    => $app->created_at ? $app->created_at->format('d M Y') : null,
                    ];
                })
                ->toArray();
        });

        if (!is_array($raw)) {
            Cache::forget(CacheKey::analyticsDashboard('recent_apps', $limit));
            return collect([]);
        }
        return collect($raw);
    }

    public function getPerformanceToday(): array
    {
        $cacheKey = CacheKey::analyticsDashboard('performance.today');
        return Cache::remember($cacheKey, 60, function () {
            $today = now()->startOfDay();

            $uploadsToday = \App\Models\Media::where('created_at', '>=', $today)->count();
            
            $approvalsToday = ActivityLog::where('action', 'like', '%disetujui%')
                ->where('created_at', '>=', $today)
                ->count();
                
            $pendingToday = Application::whereIn('status', [WorkflowState::SUBMITTED->value, WorkflowState::UNDER_REVIEW->value])
                ->where('updated_at', '>=', $today)
                ->count();

            return [
                'uploads' => $uploadsToday,
                'approvals' => $approvalsToday,
                'pending' => $pendingToday,
            ];
        });
    }

    public function getSystemHealth(): array
    {
        $cacheKey = CacheKey::analyticsDashboard('health');
        return Cache::remember($cacheKey, 60, function () {
            $dbStatus = true;
            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
            } catch (\Exception $e) {
                $dbStatus = false;
            }

            $storageStatus = true;
            try {
                \Illuminate\Support\Facades\Storage::disk('local')->exists('.');
            } catch (\Exception $e) {
                $storageStatus = false;
            }

            // Dummy queue & cache checks for basic dashboard
            return [
                'database' => $dbStatus ? 'Sehat' : 'Gangguan',
                'storage' => $storageStatus ? 'Sehat' : 'Gangguan',
                'cache' => 'Sehat',
                'queue' => 'Sehat',
            ];
        });
    }
}
