<?php

namespace App\Services;

use App\DTOs\UserApplicationsData;
use App\Models\Application;
use App\Helpers\CacheKey;
use Illuminate\Support\Facades\Cache;

class UserApplicationService
{
    public function getApplicationsData(int $userId, array $params = []): UserApplicationsData
    {
        $cacheKey = CacheKey::userApplicationsIndex($userId, $params);

        $payload = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId, $params) {
            // Stats (aggregate query)
            $counts = Application::selectRaw('count(*) as total')
                ->selectRaw("count(case when status in (?, ?) then 1 end) as pending", [\App\Enums\WorkflowState::SUBMITTED->value, \App\Enums\WorkflowState::UNDER_REVIEW->value])
                ->selectRaw("count(case when status = ? then 1 end) as revision", [\App\Enums\WorkflowState::NEED_REVISION->value])
                ->selectRaw("count(case when status = ? then 1 end) as approved", [\App\Enums\WorkflowState::APPROVED->value])
                ->selectRaw("count(case when status = ? then 1 end) as rejected", [\App\Enums\WorkflowState::REJECTED->value])
                ->where('user_id', $userId)
                ->first();

            // Main Query
            $validStatuses = ['all', 'pending', 'revision', 'approved', 'rejected'];
            $status = in_array($params['status'] ?? 'all', $validStatuses) ? ($params['status'] ?? 'all') : 'all';

            $query = Application::query()
                ->select(['id', 'tracking_code', 'product_name', 'status', 'kbli_item_id', 'created_at', 'updated_at'])
                ->where('user_id', $userId)
                ->with(['kbliItem:id,code,name'])
                ->when($status !== 'all', function ($q) use ($status) {
                    return match ($status) {
                        'pending' => $q->whereIn('status', [\App\Enums\WorkflowState::SUBMITTED->value, \App\Enums\WorkflowState::UNDER_REVIEW->value]),
                        'revision' => $q->where('status', \App\Enums\WorkflowState::NEED_REVISION->value),
                        'approved' => $q->where('status', \App\Enums\WorkflowState::APPROVED->value),
                        'rejected' => $q->where('status', \App\Enums\WorkflowState::REJECTED->value),
                        default => $q,
                    };
                })
                ->when(!empty($params['search']), function ($q) use ($params) {
                    $search = $params['search'];
                    $q->where(function ($sub) use ($search) {
                        $sub->where('tracking_code', 'like', "%{$search}%")
                            ->orWhere('product_name', 'like', "%{$search}%");
                    });
                })
                ->latest('updated_at');

            $applications = $query->paginate(10)->withQueryString();

            return [
                'counts' => [
                    'total' => $counts->total ?? 0,
                    'pending' => $counts->pending ?? 0,
                    'revision' => $counts->revision ?? 0,
                    'approved' => $counts->approved ?? 0,
                    'rejected' => $counts->rejected ?? 0,
                ],
                'applications' => [
                    'items' => collect($applications->items())->map(function($app) {
                        return [
                            'id' => $app->id,
                            'tracking_code' => $app->tracking_code,
                            'product_name' => $app->product_name,
                            'status' => $app->status->value,
                            'kbli_code' => $app->kbliItem?->code,
                            'kbli_name' => $app->kbliItem?->name,
                            'created_at' => $app->created_at?->toIso8601String(),
                            'updated_at' => $app->updated_at?->toIso8601String(),
                        ];
                    })->toArray(),
                    'total' => $applications->total(),
                    'per_page' => $applications->perPage(),
                    'current_page' => $applications->currentPage(),
                ]
            ];
        });

        $items = collect($payload['applications']['items'])->map(function($app) {
            $obj = new \stdClass();
            $obj->id = $app['id'];
            $obj->tracking_code = $app['tracking_code'];
            $obj->product_name = $app['product_name'];
            $obj->status = \App\Enums\WorkflowState::tryFrom($app['status']) ?? \App\Enums\WorkflowState::SUBMITTED;
            if ($app['kbli_code']) {
                $obj->kbliItem = new \stdClass();
                $obj->kbliItem->code = $app['kbli_code'];
                $obj->kbliItem->name = $app['kbli_name'];
            } else {
                $obj->kbliItem = null;
            }
            $obj->created_at = \Carbon\Carbon::parse($app['created_at']);
            $obj->updated_at = \Carbon\Carbon::parse($app['updated_at']);
            return $obj;
        });

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $payload['applications']['total'],
            $payload['applications']['per_page'],
            $payload['applications']['current_page'],
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return new UserApplicationsData(
            $payload['counts']['total'],
            $payload['counts']['pending'],
            $payload['counts']['revision'],
            $payload['counts']['approved'],
            $payload['counts']['rejected'],
            $paginator
        );
    }

    public function getApplicationData(Application $application): \App\DTOs\UserApplicationShowData
    {
        $cacheKey = CacheKey::userApplicationShow($application->id);

        $payload = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($application) {
            $application->load([
                'kbliItem.section', 
                'media', 
                'reviewer:id,name'
            ]);

            $activityLogs = \App\Models\ActivityLog::with('user:id,name')
                ->select(['id', 'action', 'properties', 'created_at', 'user_id'])
                ->where('subject_type', Application::class)
                ->where('subject_id', $application->id)
                ->latest()
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->action,
                        'properties' => $log->properties,
                        'created_at' => $log->created_at?->toIso8601String(),
                        'user_name' => $log->user?->name,
                    ];
                })->toArray();

            $approvalHistories = \App\Models\ApplicationHistory::with('actor:id,name')
                ->select(['id', 'status', 'notes', 'created_at', 'created_by', 'application_id'])
                ->where('application_id', $application->id)
                ->latest()
                ->get()
                ->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'status' => $history->status->value,
                        'notes' => $history->notes,
                        'created_at' => $history->created_at?->toIso8601String(),
                        'actor_name' => $history->actor?->name,
                    ];
                })->toArray();

            return [
                'activityLogs' => $activityLogs,
                'approvalHistories' => $approvalHistories
            ];
        });

        // Application object is inherently passed into this method, no need to cache it because route model binding queries it anyway.
        // Wait, route model binding might not eager load, so we need to ensure it's loaded:
        if (!$application->relationLoaded('kbliItem')) {
            $application->load(['kbliItem.section', 'media', 'reviewer:id,name']);
        }

        $logsCollection = collect($payload['activityLogs'])->map(function ($log) {
            $obj = new \stdClass();
            $obj->id = $log['id'];
            $obj->action = $log['action'];
            $obj->properties = $log['properties'];
            $obj->created_at = \Carbon\Carbon::parse($log['created_at']);
            
            if ($log['user_name']) {
                $obj->user = new \stdClass();
                $obj->user->name = $log['user_name'];
            } else {
                $obj->user = null;
            }
            return $obj;
        });

        $historiesCollection = collect($payload['approvalHistories'])->map(function ($history) {
            $obj = new \stdClass();
            $obj->id = $history['id'];
            $obj->status = \App\Enums\WorkflowState::tryFrom($history['status']) ?? \App\Enums\WorkflowState::SUBMITTED;
            $obj->notes = $history['notes'];
            $obj->created_at = \Carbon\Carbon::parse($history['created_at']);
            
            if ($history['actor_name']) {
                $obj->actor = new \stdClass();
                $obj->actor->name = $history['actor_name'];
            } else {
                $obj->actor = null;
            }
            return $obj;
        });

        $status = $application->status->value;
        $stages = [
            ['id' => 'draft', 'label' => 'Draft'],
            ['id' => 'submitted', 'label' => 'Dikirim'],
            ['id' => 'under_review', 'label' => 'Sedang Direview'],
        ];

        if ($status === 'need_revision' || $historiesCollection->contains('status.value', 'need_revision')) {
            $stages[] = ['id' => 'need_revision', 'label' => 'Revisi'];
        }

        $finalStageLabel = match ($status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Keputusan',
        };

        $stages[] = ['id' => 'final', 'label' => $finalStageLabel];

        $currentIndex = 0;
        if (in_array($status, ['submitted'])) $currentIndex = 1;
        if (in_array($status, ['under_review'])) $currentIndex = 2;
        if (in_array($status, ['need_revision'])) $currentIndex = 3;
        if (in_array($status, ['approved', 'rejected'])) $currentIndex = count($stages) - 1;

        $progressStages = [];
        foreach ($stages as $index => $stage) {
            $isCompleted = $index < $currentIndex;
            $isCurrent = $index === $currentIndex;
            $isError = $stage['id'] === 'final' && $status === 'rejected';
            $isSuccess = $stage['id'] === 'final' && $status === 'approved';
            $isWarning = $stage['id'] === 'need_revision' && $status === 'need_revision';

            $circleClasses = 'bg-white border-slate-200 text-slate-300';
            if ($isCompleted || $isSuccess) {
                $circleClasses = 'bg-primary border-primary text-white';
            } elseif ($isError) {
                $circleClasses = 'bg-red-500 border-red-500 text-white';
            } elseif ($isWarning) {
                $circleClasses = 'bg-amber-500 border-amber-500 text-white';
            } elseif ($isCurrent) {
                $circleClasses = 'bg-white border-primary text-primary';
            }

            $textClasses = 'text-slate-400';
            if ($isCurrent || $isCompleted || $isSuccess) {
                $textClasses = 'text-slate-900';
            } elseif ($isError) {
                $textClasses = 'text-red-600';
            } elseif ($isWarning) {
                $textClasses = 'text-amber-600';
            }

            $iconType = 'number';
            $iconValue = (string)($index + 1);
            if ($isCompleted || $isSuccess) {
                $iconType = 'success';
            } elseif ($isError) {
                $iconType = 'error';
            } elseif ($isWarning) {
                $iconType = 'warning';
            }

            $progressStages[] = [
                'id' => $stage['id'],
                'label' => $stage['label'],
                'circleClasses' => $circleClasses,
                'textClasses' => $textClasses,
                'iconType' => $iconType,
                'iconValue' => $iconValue,
            ];
        }

        $progress = [
            'percentage' => count($stages) > 1 ? ($currentIndex / (count($stages) - 1)) * 100 : 0,
            'stages' => $progressStages,
        ];

        return new \App\DTOs\UserApplicationShowData(
            $application,
            $logsCollection,
            $historiesCollection,
            $progress
        );
    }
}
