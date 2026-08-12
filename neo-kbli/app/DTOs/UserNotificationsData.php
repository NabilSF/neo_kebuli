<?php

namespace App\DTOs;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserNotificationsData
{
    public function __construct(
        public readonly int $total,
        public readonly int $unreadCount,
        public readonly int $readCount,
        public readonly LengthAwarePaginator $notifications
    ) {}
}
