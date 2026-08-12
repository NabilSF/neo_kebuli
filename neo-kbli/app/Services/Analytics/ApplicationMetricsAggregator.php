<?php

namespace App\Services\Analytics;

use App\Models\Application;
use App\Models\User;
use App\Enums\WorkflowState;
use App\DTOs\DashboardStatisticsDTO;

class ApplicationMetricsAggregator
{
    public function aggregateDashboardStats(): DashboardStatisticsDTO
    {
        // 1. Single query untuk menghitung jumlah berdasarkan status (efisien)
        $statusCounts = Application::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        $totalApplications = array_sum($statusCounts);
        
        $totalDraft = $statusCounts[WorkflowState::DRAFT->value] ?? 0;
        $totalSubmitted = $statusCounts[WorkflowState::SUBMITTED->value] ?? 0;
        $totalUnderReview = $statusCounts[WorkflowState::UNDER_REVIEW->value] ?? 0;
        $totalApproved = $statusCounts[WorkflowState::APPROVED->value] ?? 0;
        $totalRejected = $statusCounts[WorkflowState::REJECTED->value] ?? 0;
        $totalRevision = $statusCounts[WorkflowState::NEED_REVISION->value] ?? 0;
        
        // 2. Active reviewers: User dengan role terkait yang aktif
        // Ini adalah fallback dasar. Bisa diperluas jika ada spesifik activity log reviewer
        $activeReviewers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Reviewer', 'Admin']);
        })->where('is_active', true)->count(); 
        
        // 3. Approval Rate: Approved / (Approved + Rejected)
        $decided = $totalApproved + $totalRejected;
        $approvalRate = $decided > 0 ? round(($totalApproved / $decided) * 100, 2) : 0;
        
        // 4. Average Review Time (jam)
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite') {
            $avgReviewTimeRaw = Application::whereNotNull('submitted_at')
                ->whereNotNull('reviewed_at')
                ->selectRaw('AVG((julianday(reviewed_at) - julianday(submitted_at)) * 24) as avg_hours')
                ->value('avg_hours');
        } else {
            $avgReviewTimeRaw = Application::whereNotNull('submitted_at')
                ->whereNotNull('reviewed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, submitted_at, reviewed_at)) as avg_hours')
                ->value('avg_hours');
        }
            
        $avgReviewTimeHours = $avgReviewTimeRaw ? round((float)$avgReviewTimeRaw, 2) : 0;
        
        $totalUsers = User::count();
        $totalKbli = \App\Models\KbliItem::count();
        $totalNews = \App\Models\News::count();

        return new DashboardStatisticsDTO(
            totalApplications: $totalApplications,
            totalDraft: $totalDraft,
            totalSubmitted: $totalSubmitted,
            totalUnderReview: $totalUnderReview,
            totalApproved: $totalApproved,
            totalRejected: $totalRejected,
            totalRevision: $totalRevision,
            activeReviewers: $activeReviewers,
            approvalRate: $approvalRate,
            averageReviewTimeHours: $avgReviewTimeHours,
            totalUsers: $totalUsers,
            totalKbli: $totalKbli,
            totalNews: $totalNews
        );
    }
}
