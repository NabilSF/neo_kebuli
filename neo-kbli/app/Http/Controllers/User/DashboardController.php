<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\UserDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private UserDashboardService $dashboardService)
    {
    }

    public function index(Request $request)
    {
        $dashboardData = $this->dashboardService->getDashboardData($request->user()->id);

        return view('user.dashboard', compact('dashboardData'));
    }
}
