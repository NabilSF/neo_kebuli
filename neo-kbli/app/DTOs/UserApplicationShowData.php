<?php

namespace App\DTOs;

use App\Models\Application;
use Illuminate\Support\Collection;

class UserApplicationShowData
{
    public function __construct(
        public readonly Application $application,
        public readonly Collection $activityLogs,
        public readonly Collection $approvalHistories,
        public readonly array $progress
    ) {}
}
