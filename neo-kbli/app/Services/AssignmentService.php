<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationAssignment;
use App\Models\User;
use App\Enums\WorkflowState;
use App\Enums\AssignmentState;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    public function __construct(
        private WorkflowService $workflowService,
        private ActivityLogService $activityLogService
    ) {}

    /**
     * Menugaskan sebuah aplikasi ke reviewer.
     */
    public function assignReviewer(Application $application, User $reviewer, User $assigner, ?string $notes = null): ApplicationAssignment
    {
        return DB::transaction(function () use ($application, $reviewer, $assigner, $notes) {
            
            // 1. Simpan Assignment in-memory untuk event
            $assignment = new ApplicationAssignment(
                application_id: $application->id,
                reviewer_id: $reviewer->id,
                assigned_by: $assigner->id,
                notes: $notes
            );

            // 2. Update kepemilikan review pada entitas utama (source of truth)
            $application->update(['reviewed_by' => $reviewer->id]);

            // 4. Integrasi WorkflowService (Aturan Bisnis: Jika baru Diajukan, otomatis menjadi Sedang Ditinjau)
            if ($application->status === WorkflowState::SUBMITTED) {
                $this->workflowService->transition(
                    entity: $application,
                    targetState: WorkflowState::UNDER_REVIEW,
                    action: 'Permohonan ditugaskan kepada ' . $reviewer->name,
                    notes: $notes,
                    userId: $assigner->id
                );
            } else {
                // Jika status lain, hanya log perubahan penugasan menggunakan ActivityLogService
                $this->activityLogService->logActivity(
                    entity: $application,
                    action: 'Permohonan ditugaskan ulang kepada ' . $reviewer->name,
                    oldState: $application->status,
                    newState: $application->status,
                    notes: $notes,
                    userId: $assigner->id
                );
            }

            DB::afterCommit(fn() => event(new \App\Events\ReviewerAssigned($assignment, $application, $reviewer)));

            return $assignment;
        });
    }

    /**
     * Membatalkan penugasan tanpa menentukan pengganti.
     */
    public function cancelAssignment(Application $application, User $cancelledBy, ?string $notes = null): void
    {
        DB::transaction(function () use ($application, $cancelledBy, $notes) {
            $reviewer = $application->reviewer;
            $application->update(['reviewed_by' => null]);

            $this->activityLogService->logActivity(
                entity: $application,
                action: 'Penugasan dibatalkan' . ($reviewer ? ' dari ' . $reviewer->name : ''),
                oldState: $application->status,
                newState: $application->status,
                notes: $notes,
                userId: $cancelledBy->id
            );
        });
    }
}
