<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Services\AdminReviewService;
use App\Enums\ApplicationStatus;
use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display a paginated listing of all applications.
     */
    public function index(Request $request)
    {
        // Use Product Schema Only
        $query = Application::with([
            'kbliItem:id,code,name',
            'user:id,name'
        ])->select([
            'id', 
            'tracking_code', 
            'status', 
            'product_name', 
            'submitted_at', 
            'kbli_item_id',
            'user_id'
        ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        if ($statusFilter = $request->input('status')) {
            $query->where('status', $statusFilter);
        }

        $applications = $query->orderBy('submitted_at', 'desc')->paginate(15);

        return view('admin.applications.index', compact('applications'));
    }

    /**
     * Display the comprehensive details of a specific application.
     */
    public function show(Application $application)
    {
        $application->load([
            'reviewer',
            'histories.actor',
            'media',
            'kbliItem'
        ]);

        return view('admin.applications.show', compact('application'));
    }

    /**
     * Delegate application status mutation to the orchestrating service.
     */
    public function updateStatus(
        UpdateApplicationStatusRequest $request, 
        Application $application, 
        AdminReviewService $reviewService
    ) {
        $validated = $request->validated();
        
        $targetStatus = ApplicationStatus::from($validated['status']);

        try {
            $reviewService->updateStatus(
                $application,
                $targetStatus,
                $validated['notes'] ?? null,
                Auth::user()
            );

            return redirect()->back()->with('success', 'Status permohonan berhasil diperbarui.');
            
        } catch (InvalidStatusTransitionException $e) {
            return redirect()->back()->withErrors(['status' => $e->getMessage()]);
        }
    }
}
