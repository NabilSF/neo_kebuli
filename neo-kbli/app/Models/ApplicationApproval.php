<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationApproval
{
    public function __construct(
        public int $application_id,
        public int $approver_id,
        public string $decision,
        public ?string $notes = null
    ) {}
}
