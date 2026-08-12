<?php

namespace App\DTOs;

class DashboardStatisticsDTO
{
    public function __construct(
        public readonly int $totalApplications,
        public readonly int $totalDraft,
        public readonly int $totalSubmitted,
        public readonly int $totalUnderReview,
        public readonly int $totalApproved,
        public readonly int $totalRejected,
        public readonly int $totalRevision,
        public readonly int $activeReviewers,
        public readonly float $approvalRate,
        public readonly float $averageReviewTimeHours,
        public readonly int $totalUsers = 0,
        public readonly int $totalKbli = 0,
        public readonly int $totalNews = 0
    ) {}

    public function toArray(): array
    {
        return [
            'totalApplications'      => $this->totalApplications,
            'totalDraft'             => $this->totalDraft,
            'totalSubmitted'         => $this->totalSubmitted,
            'totalUnderReview'       => $this->totalUnderReview,
            'totalApproved'          => $this->totalApproved,
            'totalRejected'          => $this->totalRejected,
            'totalRevision'          => $this->totalRevision,
            'activeReviewers'        => $this->activeReviewers,
            'approvalRate'           => $this->approvalRate,
            'averageReviewTimeHours' => $this->averageReviewTimeHours,
            'totalUsers'             => $this->totalUsers,
            'totalKbli'              => $this->totalKbli,
            'totalNews'              => $this->totalNews,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            totalApplications: (int) ($data['totalApplications'] ?? 0),
            totalDraft: (int) ($data['totalDraft'] ?? 0),
            totalSubmitted: (int) ($data['totalSubmitted'] ?? 0),
            totalUnderReview: (int) ($data['totalUnderReview'] ?? 0),
            totalApproved: (int) ($data['totalApproved'] ?? 0),
            totalRejected: (int) ($data['totalRejected'] ?? 0),
            totalRevision: (int) ($data['totalRevision'] ?? 0),
            activeReviewers: (int) ($data['activeReviewers'] ?? 0),
            approvalRate: (float) ($data['approvalRate'] ?? 0),
            averageReviewTimeHours: (float) ($data['averageReviewTimeHours'] ?? 0),
            totalUsers: (int) ($data['totalUsers'] ?? 0),
            totalKbli: (int) ($data['totalKbli'] ?? 0),
            totalNews: (int) ($data['totalNews'] ?? 0)
        );
    }
}
