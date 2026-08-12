<?php

namespace App\Services;

use App\Enums\WorkflowState;
use App\Exceptions\WorkflowException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    public function __construct(private ActivityLogService $activityLogService) {}

    /**
     * Validasi dan eksekusi transisi status sebuah entitas bisnis.
     */
    public function transition(Model $entity, WorkflowState $targetState, string $action, ?string $notes = null, ?int $userId = null): void
    {
        $currentState = WorkflowState::tryFrom($entity->status?->value ?? $entity->status);
        
        if ($currentState && !$currentState->canTransitionTo($targetState)) {
            throw WorkflowException::invalidTransition($currentState->label(), $targetState->label());
        }

        DB::transaction(function () use ($entity, $currentState, $targetState, $action, $notes, $userId) {
            // Update entity status
            $entity->update(['status' => $targetState->value]);

            // Log activity using dedicated service
            $this->activityLogService->logActivity(
                entity: $entity,
                action: $action,
                oldState: $currentState,
                newState: $targetState,
                notes: $notes,
                userId: $userId
            );

            // Dispatch ApplicationSubmitted if transitioning to SUBMITTED
            if ($targetState === WorkflowState::SUBMITTED && $entity instanceof \App\Models\Application) {
                DB::afterCommit(fn() => event(new \App\Events\ApplicationSubmitted($entity)));
            }
        });
    }
}
