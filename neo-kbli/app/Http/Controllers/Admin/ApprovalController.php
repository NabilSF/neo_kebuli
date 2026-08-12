<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Services\ApprovalService;
use Exception;

class ApprovalController extends Controller
{
    public function __construct(private ApprovalService $approvalService) {}

    public function store(Request $request, Application $application)
    {
        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:approved,rejected,need_revision'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'code' => ['exclude_unless:decision,approved', 'required', 'numeric', 'digits:5', 'unique:kbli_items,code'],
            'section_id' => ['exclude_unless:decision,approved', 'required', 'exists:kbli_sections,id'],
        ]);

        try {
            $notes = $validated['notes'] ?? null;
            $approver = $request->user();

            match($validated['decision']) {
                'approved' => $this->approvalService->approve($application, $approver, $notes, $validated['code'], $validated['section_id']),
                'rejected' => $this->approvalService->reject($application, $approver, $notes),
                'need_revision' => $this->approvalService->requestRevision($application, $approver, $notes),
            };

            return back()->with('success', 'Keputusan resmi berhasil ditetapkan dan riwayat persetujuan telah dicatat.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
