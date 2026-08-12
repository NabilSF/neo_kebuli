<?php

namespace App\DTOs;

use Carbon\Carbon;

class RecentActivityDTO
{
    public readonly object $user;

    public function __construct(
        ?string $userName,
        public readonly string $description,
        public readonly ?Carbon $created_at
    ) {
        $this->user = (object) ['name' => $userName ?? 'Sistem'];
    }

    public function toArray(): array
    {
        return [
            'userName'    => $this->user->name ?? 'Sistem',
            'description' => $this->description,
            'created_at'  => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        $createdAt = !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null;

        return new self(
            userName: (string) ($data['userName'] ?? ($data['user_name'] ?? 'Sistem')),
            description: (string) ($data['description'] ?? ''),
            created_at: $createdAt
        );
    }
}
