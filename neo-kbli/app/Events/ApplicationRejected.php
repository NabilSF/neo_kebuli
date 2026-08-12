<?php

namespace App\Events;

use App\Models\ApplicationApproval;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ApplicationApproval $approval,
        public \App\Models\Application $application
    ) {}
}
