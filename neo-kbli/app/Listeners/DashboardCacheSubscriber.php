<?php

namespace App\Listeners;

use App\Events\ApplicationApproved;
use App\Events\ApplicationRejected;
use App\Events\ApplicationSubmitted;
use App\Events\ReviewerAssigned;

use App\Events\RevisionRequested;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Cache;

class DashboardCacheSubscriber
{
    /**
     * Invalidate dashboard caches when an application workflow changes.
     */
    public function invalidateCache($event = null): void
    {
        \App\Helpers\CacheKey::invalidateAnalyticsDashboard('statistics');
        \App\Helpers\CacheKey::invalidateAnalyticsDashboard('status');
        \App\Helpers\CacheKey::invalidateAnalyticsDashboard('reviewers');
        \App\Helpers\CacheKey::invalidateAnalyticsDashboard('activities');
        \App\Helpers\CacheKey::invalidateAnalyticsDashboard('queue');
        \App\Helpers\CacheKey::invalidateAnalyticsDashboard('recent_apps');
        
        $application = null;
        if ($event) {
            if (isset($event->application)) {
                $application = $event->application;
            } elseif (isset($event->approval) && isset($event->approval->application_id)) {
                $application = \App\Models\Application::find($event->approval->application_id);
            } elseif (isset($event->assignment) && isset($event->assignment->application)) {
                $application = $event->assignment->application;
            }
        }

        if ($application && $application->user_id) {
            \App\Helpers\CacheKey::invalidateUserDashboard($application->user_id);
            \App\Helpers\CacheKey::invalidateUserApplicationsIndex($application->user_id);
            \App\Helpers\CacheKey::invalidateUserApplicationShow($application->id);
            \App\Helpers\CacheKey::invalidateUserNotifications($application->user_id);
            \App\Helpers\CacheKey::invalidateUserProfile($application->user_id);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            [
                ApplicationSubmitted::class,
                ReviewerAssigned::class,

                ApplicationApproved::class,
                ApplicationRejected::class,
                RevisionRequested::class,
            ],
            [DashboardCacheSubscriber::class, 'invalidateCache']
        );
    }
}
