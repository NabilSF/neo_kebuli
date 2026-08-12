<?php

namespace App\Enums;

enum ReviewState: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
}
