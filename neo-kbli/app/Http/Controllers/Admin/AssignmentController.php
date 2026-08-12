<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Services\AssignmentService;
use Exception;

class AssignmentController extends Controller
{
    public function __construct(private AssignmentService $assignmentService) {}

    /**
     * Store a newly created assignment via Thin Controller Principle.
     */
    public function store(Request $request, Application $application)
    {
        $validated = $request->validate([
            'reviewer_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:500']
        ]);

        $reviewer = User::findOrFail($validated['reviewer_id']);

        try {
            $this->assignmentService->assignReviewer(
                application: $application, 
                reviewer: $reviewer, 
                assigner: $request->user(), 
                notes: $validated['notes'] ?? null
            );
            return back()->with('success', 'Reviewer berhasil ditugaskan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
