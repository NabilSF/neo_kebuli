<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case NEED_REVISION = 'need_revision';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Get the allowed target statuses this status can transition to.
     * 
     * Centralized workflow rules:
     * - DRAFT: Can only transition to SUBMITTED when applicant completes form.
     * - SUBMITTED: Can only transition to UNDER_REVIEW when Admin picks it up.
     * - UNDER_REVIEW: Can transition to APPROVED, REJECTED, or NEED_REVISION.
     * - NEED_REVISION: Can only transition back to SUBMITTED once user fixes it.
     * - APPROVED/REJECTED: Terminal states, no further transitions allowed.
     *
     * @return array<ApplicationStatus>
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::DRAFT => [self::SUBMITTED],
            self::SUBMITTED => [self::UNDER_REVIEW],
            self::UNDER_REVIEW => [self::APPROVED, self::REJECTED, self::NEED_REVISION],
            self::NEED_REVISION, self::APPROVED, self::REJECTED => [],
        };
    }

    /**
     * Check if the current status can transition to a target status.
     *
     * @param ApplicationStatus $target
     * @return bool
     */
    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * Determine if this status is a terminal state (end of workflow).
     *
     * @return bool
     */
    public function isTerminal(): bool
    {
        return empty($this->allowedTransitions());
    }
}
