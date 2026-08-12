<?php

namespace App\Services;

use App\DTOs\UserNotificationsData;
use App\Helpers\CacheKey;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Get paginated notifications with count statistics.
     */
    public function getNotificationsData(User $user, int $page = 1): UserNotificationsData
    {
        $cacheKey = CacheKey::userNotifications($user->id, $page);

        $payload = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
            $total = $user->notifications()->count();
            $unreadCount = $user->unreadNotifications()->count();
            $readCount = $total - $unreadCount;
            
            $notifications = $user->notifications()->paginate(15);

            return [
                'counts' => [
                    'total' => $total,
                    'unread' => $unreadCount,
                    'read' => $readCount,
                ],
                'notifications' => [
                    'items' => collect($notifications->items())->map(function($notif) {
                        return [
                            'id' => $notif->id,
                            'type' => $notif->type,
                            'data' => $notif->data,
                            'read_at' => $notif->read_at?->toIso8601String(),
                            'created_at' => $notif->created_at?->toIso8601String(),
                        ];
                    })->toArray(),
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                ]
            ];
        });

        $items = collect($payload['notifications']['items'])->map(function($notif) {
            $obj = new \stdClass();
            $obj->id = $notif['id'];
            $obj->type = $notif['type'];
            $obj->data = $notif['data'];
            $obj->read_at = $notif['read_at'] ? \Carbon\Carbon::parse($notif['read_at']) : null;
            $obj->created_at = \Carbon\Carbon::parse($notif['created_at']);
            
            // Re-implement read() and unread() logic if used in Blade
            $obj->unread = function() use ($obj) {
                return $obj->read_at === null;
            };
            
            return $obj;
        });

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $payload['notifications']['total'],
            $payload['notifications']['per_page'],
            $payload['notifications']['current_page'],
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return new UserNotificationsData(
            $payload['counts']['total'],
            $payload['counts']['unread'],
            $payload['counts']['read'],
            $paginator
        );
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(User $user, string $notificationId): void
    {
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if ($notification && $notification->unread()) {
            $notification->markAsRead();
            CacheKey::invalidateUserNotifications($user->id);
        }
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
        CacheKey::invalidateUserNotifications($user->id);
    }

    /**
     * Delete a single notification.
     */
    public function delete(User $user, string $notificationId): void
    {
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $notification->delete();
            CacheKey::invalidateUserNotifications($user->id);
        }
    }

    /**
     * Resolve application URL dynamically based on user role.
     */
    public function getApplicationUrl(User $user, int $applicationId): string
    {
        $staffRoles = ['admin', 'Admin', 'super-admin', 'Super Admin', 'operator', 'Operator', 'Administrator', 'reviewer', 'Reviewer'];
        if ($user->hasAnyRole($staffRoles)) {
            return route('admin.applications.show', $applicationId);
        }
        
        return route('user.applications.show', $applicationId);
    }
}
