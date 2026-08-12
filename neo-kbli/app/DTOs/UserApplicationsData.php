<?php

namespace App\DTOs;

use Illuminate\Pagination\LengthAwarePaginator;

class UserApplicationsData
{
    public function __construct(
        public readonly int $totalApplications,
        public readonly int $pendingReview,
        public readonly int $needRevision,
        public readonly int $approved,
        public readonly int $rejected,
        public readonly LengthAwarePaginator $applications
    ) {}
}
