<?php

namespace App\DTOs;

use Carbon\Carbon;

class ApprovalQueueDTO
{
    public readonly object $user;
    public readonly object $kbliItem;

    public function __construct(
        public readonly string $tracking_code,
        ?string $userName,
        ?string $kbliCode,
        public readonly ?Carbon $submitted_at
    ) {
        $this->user = (object) ['name' => $userName ?? 'Anonim'];
        $this->kbliItem = (object) ['code' => $kbliCode ?? '-'];
    }

    public function toArray(): array
    {
        return [
            'tracking_code' => $this->tracking_code,
            'userName'      => $this->user->name ?? 'Anonim',
            'kbliCode'      => $this->kbliItem->code ?? '-',
            'submitted_at'  => $this->submitted_at ? $this->submitted_at->toIso8601String() : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        $submittedAt = !empty($data['submitted_at']) ? Carbon::parse($data['submitted_at']) : null;

        return new self(
            tracking_code: (string) ($data['tracking_code'] ?? ''),
            userName: (string) ($data['userName'] ?? ($data['user_name'] ?? 'Anonim')),
            kbliCode: (string) ($data['kbliCode'] ?? ($data['kbli_code'] ?? '-')),
            submitted_at: $submittedAt
        );
    }
}
