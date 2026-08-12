<?php

namespace App\DTOs;

use App\Models\User;
use Illuminate\Support\Collection;

class UserProfileData
{
    public function __construct(
        public readonly User $user,
        public readonly int $totalApplications,
        public readonly int $approvedApplications,
        public readonly int $revisionApplications,
        public readonly int $rejectedApplications,
        public readonly Collection $recentActivities
    ) {}
}
