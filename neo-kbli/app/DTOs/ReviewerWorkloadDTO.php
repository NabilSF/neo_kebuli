<?php

namespace App\DTOs;

class ReviewerWorkloadDTO
{
    public function __construct(
        public readonly string $reviewer_name,
        public readonly int $assigned_count
    ) {}

    public function toArray(): array
    {
        return [
            'reviewer_name'  => $this->reviewer_name,
            'assigned_count' => $this->assigned_count,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            reviewer_name: (string) ($data['reviewer_name'] ?? ($data['reviewerName'] ?? 'Unknown')),
            assigned_count: (int) ($data['assigned_count'] ?? ($data['assignedCount'] ?? 0))
        );
    }
}
