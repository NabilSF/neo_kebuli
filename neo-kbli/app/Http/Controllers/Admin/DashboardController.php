<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Analytics\AnalyticsService;

class DashboardController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService
    ) {}

    public function index()
    {
        $statistics = $this->analyticsService->getDashboardStatistics();
        $systemHealth = $this->analyticsService->getSystemHealth();
        $performanceToday = $this->analyticsService->getPerformanceToday();
        
        $recentActivities = $this->analyticsService->getRecentActivities(10);
        $recentApplications = $this->analyticsService->getRecentApplications(10);

        return view('admin.dashboard', compact(
            'statistics',
            'systemHealth',
            'performanceToday',
            'recentActivities',
            'recentApplications'
        ));
    }
}
