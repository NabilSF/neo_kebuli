<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Enums\WorkflowState;

/**
 * ActivityLogService memisahkan tanggung jawab pencatatan jejak audit (Audit Trail)
 * dari transisi alur kerja (WorkflowService), mengikuti Responsibility Segregation Principle.
 */
class ActivityLogService
{
    /**
     * Catat aktivitas ke dalam riwayat log (Immutable, Append-Only).
     */
    public function logActivity(Model $entity, string $action, ?WorkflowState $oldState = null, ?WorkflowState $newState = null, ?string $notes = null, ?int $userId = null, ?array $properties = null): void
    {
        $mergedProperties = array_merge($properties ?? [], [
            'old_status' => $oldState?->value,
            'new_status' => $newState?->value,
            'notes' => $notes,
            'metadata' => [
                'user_agent' => request()->userAgent()
            ]
        ]);

        $entity->activityLogs()->create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'properties' => $mergedProperties,
            'ip_address' => request()->ip()
        ]);

        if ($entity instanceof \App\Models\Application) {
            \App\Models\ApplicationHistory::create([
                'application_id' => $entity->id,
                'status' => $newState->value,
                'notes' => $notes ?? $action,
                'created_by' => $userId ?? auth()->id(),
            ]);
            
            \App\Helpers\CacheKey::invalidateUserDashboard($entity->user_id);
            \App\Helpers\CacheKey::invalidateUserApplicationShow($entity->id);
            \App\Helpers\CacheKey::invalidateUserApplicationsIndex($entity->user_id);
        }
    }
}
