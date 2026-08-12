<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class UserDashboardData
{
    public function __construct(
        public readonly int $totalApplications,
        public readonly int $pendingReview,
        public readonly int $needRevision,
        public readonly int $approved,
        public readonly int $rejected,
        public readonly Collection $recentApplications,
        public readonly Collection $recentActivities
    ) {}
}
