<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationAssignment
{
    public function __construct(
        public int $application_id,
        public int $reviewer_id,
        public int $assigned_by,
        public ?string $notes = null
    ) {}
}
