<?php

namespace App\Listeners;

use App\Events\ApplicationSubmitted;
use App\Events\ReviewerAssigned;

use App\Events\ApplicationApproved;
use App\Events\ApplicationRejected;
use App\Events\RevisionRequested;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\ApplicationSubmittedNotification;
use App\Notifications\ReviewerAssignedNotification;

use App\Notifications\ApplicationApprovedNotification;
use App\Notifications\ApplicationRejectedNotification;
use App\Notifications\RevisionRequestedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationEventSubscriber implements ShouldQueue
{
    public int $tries;
    public array $backoff;

    public function __construct()
    {
        $this->tries = config('workflow.queue.tries', 3);
        $this->backoff = config('workflow.queue.backoff', [60, 180]);
    }

    public function handleApplicationSubmitted(ApplicationSubmitted $event): void
    {
        $admins = User::where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'Admin', 'super-admin', 'Super Admin']);
            })
            ->select(['id', 'name', 'email'])
            ->get();
        Notification::send($admins, new ApplicationSubmittedNotification($event->application));
    }

    public function handleReviewerAssigned(ReviewerAssigned $event): void
    {
        // Notify the assigned reviewer
        $event->reviewer->notify(new ReviewerAssignedNotification($event->assignment, $event->application));
    }



    public function handleApplicationApproved(ApplicationApproved $event): void
    {
        $application = $event->application;

        // If the application doesn't have a KBLI item yet, it means it's proposing a new one.
        // We cannot promote it automatically if we don't have a valid 5-digit code and section_id.
        // However, if the application SOMEHOW has a code proposed (e.g., via future UI where admin sets it during approval),
        // we would create it here. Since currently there's no code provided during approval,
        // we skip the automatic creation to avoid SQL errors or inserting tracking_code.
        // The admin must manually create the KBLI item first and link it, or we need to update the Approval form to accept `code` and `section_id`.
        
        if ($applicant = $application->user) {
            $applicant->notify(new ApplicationApprovedNotification($event->approval, $application));
        }
    }

    public function handleApplicationRejected(ApplicationRejected $event): void
    {
        if ($applicant = $event->application->user) {
            $applicant->notify(new ApplicationRejectedNotification($event->approval, $event->application));
        }
    }

    public function handleRevisionRequested(RevisionRequested $event): void
    {
        if ($applicant = $event->application->user) {
            $applicant->notify(new RevisionRequestedNotification($event->approval, $event->application));
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            ApplicationSubmitted::class,
            [ApplicationEventSubscriber::class, 'handleApplicationSubmitted']
        );
        $events->listen(
            ReviewerAssigned::class,
            [ApplicationEventSubscriber::class, 'handleReviewerAssigned']
        );

        $events->listen(
            ApplicationApproved::class,
            [ApplicationEventSubscriber::class, 'handleApplicationApproved']
        );
        $events->listen(
            ApplicationRejected::class,
            [ApplicationEventSubscriber::class, 'handleApplicationRejected']
        );
        $events->listen(
            RevisionRequested::class,
            [ApplicationEventSubscriber::class, 'handleRevisionRequested']
        );
    }
}
