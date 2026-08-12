<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Enums\WorkflowState;
use Exception;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with([
            'kbliItem:id,code,name',
            'user:id,name'
        ])->select(['id', 'tracking_code', 'product_name', 'status', 'submitted_at', 'created_at', 'kbli_item_id', 'user_id']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        if ($statusFilter = $request->input('status')) {
            $query->where('status', $statusFilter);
        }

        $applications = $query->orderBy('submitted_at', 'desc')->paginate(15)->withQueryString();
        $statuses = WorkflowState::cases();

        return view('admin.applications.index', compact('applications', 'statuses'));
    }

    public function show(Application $application)
    {
        $application->load([
            'reviewer',
            'activityLogs.user',
            'media',
            'kbliItem.section'
        ]);

        $reviewers = \App\Models\User::where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Reviewer', 'reviewer', 'operator', 'Operator', 'Admin', 'admin', 'Super Admin', 'super-admin']);
            })
            ->pluck('name', 'id');
        return view('admin.applications.show', compact('application', 'reviewers'));
    }

    public function addMemo(
        Request $request, 
        Application $application, 
        \App\Services\ActivityLogService $activityLogService
    ) {
        $validated = $request->validate([
            'comment' => ['required', 'string']
        ]);
        
        try {
            $activityLogService->logActivity(
                entity: $application,
                action: 'Catatan Internal (Memo) Ditambahkan',
                oldState: $application->status,
                newState: $application->status,
                notes: $validated['comment'],
                userId: Auth::id()
            );

            return redirect()->back()->with('success', 'Catatan internal berhasil disimpan.');
            
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
