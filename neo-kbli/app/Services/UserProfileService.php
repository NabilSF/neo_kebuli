<?php

namespace App\Services;

use App\DTOs\UserProfileData;
use App\Helpers\CacheKey;
use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserProfileService
{
    /**
     * Get user profile data, including statistics and recent activities.
     */
    public function getProfileData(User $user): UserProfileData
    {
        $cacheKey = CacheKey::userProfile($user->id);

        $payload = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            $stats = Application::selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as revision,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected
                ', [
                    \App\Enums\WorkflowState::APPROVED->value,
                    \App\Enums\WorkflowState::NEED_REVISION->value,
                    \App\Enums\WorkflowState::REJECTED->value
                ])
                ->where('user_id', $user->id)
                ->first();

            $recentActivities = ActivityLog::select(['id', 'action', 'properties', 'created_at'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($act) {
                    return [
                        'id' => $act->id,
                        'action' => $act->action,
                        'properties' => $act->properties,
                        'created_at' => $act->created_at?->toIso8601String(),
                    ];
                })->toArray();

            return [
                'total' => (int) ($stats->total ?? 0),
                'approved' => (int) ($stats->approved ?? 0),
                'revision' => (int) ($stats->revision ?? 0),
                'rejected' => (int) ($stats->rejected ?? 0),
                'recentActivities' => $recentActivities
            ];
        });

        $actsCollection = collect($payload['recentActivities'])->map(function($act) {
            $obj = new \stdClass();
            $obj->id = $act['id'];
            $obj->action = $act['action'];
            $obj->properties = $act['properties'];
            $obj->created_at = \Carbon\Carbon::parse($act['created_at']);
            return $obj;
        });

        return new UserProfileData(
            $user,
            $payload['total'],
            $payload['approved'],
            $payload['revision'],
            $payload['rejected'],
            $actsCollection
        );
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(User $user, array $data): bool
    {
        $updated = $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        if ($updated) {
            CacheKey::invalidateUserProfile($user->id);
            // Also invalidate dashboard cache as name/email might be shown there
            CacheKey::invalidateUserDashboard($user->id);
        }

        return $updated;
    }

    /**
     * Update user password.
     */
    public function updatePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}
