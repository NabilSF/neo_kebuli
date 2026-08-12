<?php

namespace App\Events;

use App\Models\ApplicationAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewerAssigned
{
    use Dispatchable, SerializesModels;
    public function __construct(
        public ApplicationAssignment $assignment,
        public \App\Models\Application $application,
        public \App\Models\User $reviewer
    ) {}
}
