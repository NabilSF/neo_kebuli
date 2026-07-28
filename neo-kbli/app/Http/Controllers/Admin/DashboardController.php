<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KbliItem;
use App\Models\KbliSection;
use App\Models\Application;
use App\Models\ActivityLog;
use App\Models\News;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalItems = KbliItem::count();
        $totalApplications = Application::count();
        $pendingApplications = Application::whereIn('status', [
            \App\Enums\ApplicationStatus::SUBMITTED->value ?? 'DIAJUKAN', 
            \App\Enums\ApplicationStatus::UNDER_REVIEW->value ?? 'SEDANG DIREVIEW'
        ])->count();
        
        $activeItems = KbliItem::where('is_active', true)->count();
        $systemStatus = $activeItems > 0 ? 'Beroperasi' : 'Perlu Konfigurasi';

        // Recent Activities
        $recentActivities = ActivityLog::with('user')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();

        return view('admin.dashboard', compact(
            'totalItems',
            'totalApplications',
            'pendingApplications',
            'systemStatus',
            'recentActivities'
        ));
    }
}
