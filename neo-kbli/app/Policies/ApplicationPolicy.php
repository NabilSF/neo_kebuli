<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can view the list (scoped in queries)
    }

    public function view(User $user, Application $application): bool
    {
        return $user->can('application.view') || $application->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true; // Any authenticated user can apply
    }

    public function assign(User $user, Application $application): bool
    {
        return $user->can('application.assign');
    }

    public function review(User $user, Application $application): bool
    {
        return $user->can('application.review') && $application->reviewed_by === $user->id;
    }

    public function approve(User $user, Application $application): bool
    {
        return $user->can('application.approve');
    }

    /**
     * User can upload media to their own application if it is not yet finalized.
     */
    public function uploadMedia(User $user, Application $application): bool
    {
        return $application->user_id === $user->id
            && !in_array($application->status, [
                \App\Enums\WorkflowState::APPROVED,
                \App\Enums\WorkflowState::REJECTED,
                \App\Enums\WorkflowState::ARCHIVED,
            ]);
    }

    /**
     * User can delete media from their own application if it is not yet finalized.
     */
    public function deleteMedia(User $user, Application $application): bool
    {
        return $this->uploadMedia($user, $application);
    }
}
