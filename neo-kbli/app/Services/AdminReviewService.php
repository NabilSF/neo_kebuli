<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationHistory;
use App\Models\User;
use App\Enums\ApplicationStatus;
use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Support\Facades\DB;

class AdminReviewService
{
    /**
     * Update the status of an application and record its history trail.
     *
     * @param Application $application
     * @param ApplicationStatus $targetStatus
     * @param string|null $notes
     * @param User $admin
     * @return Application
     * @throws InvalidStatusTransitionException
     */
    public function updateStatus(
        Application $application,
        ApplicationStatus $targetStatus,
        ?string $notes,
        User $admin
    ): Application {
        // Ensure the current status is an instance of ApplicationStatus
        $currentStatus = $application->status instanceof ApplicationStatus 
            ? $application->status 
            : ApplicationStatus::from($application->status);

        // Enforce centralized workflow transition logic
        if (!$currentStatus->canTransitionTo($targetStatus)) {
            throw InvalidStatusTransitionException::make($currentStatus, $targetStatus);
        }

        // Execute persistence within a single transaction boundary
        return DB::transaction(function () use ($application, $currentStatus, $targetStatus, $notes, $admin) {
            
            // 1. Update core application state
            $application->update([
                'status' => $targetStatus->value,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            // 2. Append to immutable history ledger
            ApplicationHistory::create([
                'application_id' => $application->id,
                'status' => $targetStatus->value,
                'notes' => $notes,
                'created_by' => $admin->id,
            ]);

            return $application;
        });
    }
}
