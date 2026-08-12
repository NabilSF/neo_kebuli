<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationApproval;
use App\Models\User;
use App\Enums\WorkflowState;
use App\Exceptions\ApprovalException;
use App\Exceptions\ReviewException;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function __construct(private WorkflowService $workflowService) {}

    public function approve(Application $application, User $approver, ?string $notes = null, ?string $code = null, ?int $sectionId = null): ApplicationApproval
    {
        return $this->processDecision($application, $approver, WorkflowState::APPROVED, $notes, $code, $sectionId);
    }

    public function reject(Application $application, User $approver, ?string $notes = null): ApplicationApproval
    {
        return $this->processDecision($application, $approver, WorkflowState::REJECTED, $notes);
    }

    public function requestRevision(Application $application, User $approver, ?string $notes = null): ApplicationApproval
    {
        return $this->processDecision($application, $approver, WorkflowState::NEED_REVISION, $notes);
    }

    private function processDecision(Application $application, User $approver, WorkflowState $decisionState, ?string $notes = null, ?string $code = null, ?int $sectionId = null): ApplicationApproval
    {
        $this->ensureCanApprove($application);

        return DB::transaction(function () use ($application, $approver, $decisionState, $notes, $code, $sectionId) {
            
            // 1. Simpan Keputusan Bisnis (In memory for events)
            $approval = new ApplicationApproval(
                application_id: $application->id,
                approver_id: $approver->id,
                decision: $decisionState->value,
                notes: $notes
            );

            // Create KbliItem for approved application (Idempotency)
            if ($decisionState === WorkflowState::APPROVED && !$application->kbli_item_id) {
                $kbliItem = \App\Models\KbliItem::create([
                    'code' => $code,
                    'name' => $application->product_name,
                    'description' => $application->product_description ?? $application->production_process ?? '-',
                    'section_id' => $sectionId,
                    'is_active' => true,
                ]);
                
                $application->kbli_item_id = $kbliItem->id;
                // Don't update application yet, let the transition handle it, but wait, transition only updates status.
                $application->save();
            }

            // 2. Dorong ke Workflow Foundation
            $this->workflowService->transition(
                entity: $application,
                targetState: $decisionState,
                action: 'Keputusan resmi: ' . $decisionState->label(),
                notes: $notes,
                userId: $approver->id
            );

            DB::afterCommit(function() use ($decisionState, $approval, $application) {
                match($decisionState) {
                    WorkflowState::APPROVED => event(new \App\Events\ApplicationApproved($approval, $application)),
                    WorkflowState::REJECTED => event(new \App\Events\ApplicationRejected($approval, $application)),
                    WorkflowState::NEED_REVISION => event(new \App\Events\RevisionRequested($approval, $application)),
                    default => null,
                };
            });

            return $approval;
        });
    }

    private function ensureCanApprove(Application $application): void
    {
        if ($application->status !== WorkflowState::UNDER_REVIEW) {
            throw ApprovalException::notUnderReview();
        }

        if ($application->reviewed_by !== auth()->id() && !auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            throw ReviewException::unauthorizedReviewer();
        }
    }
}
