<?php

namespace App\Enums;

enum AssignmentState: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}
