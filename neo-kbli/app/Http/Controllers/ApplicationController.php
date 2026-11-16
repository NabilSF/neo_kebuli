<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\KbliItem;
use App\Models\Application;
use App\Models\ApplicationHistory;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        $selectedKbli = null;
        if ($request->has('kbli')) {
            $selectedKbli = KbliItem::where('code', $request->kbli)
                ->where('is_active', true)
                ->first();
        }
        
        $kblis = KbliItem::where('is_active', true)
            ->select('id', 'code', 'name')
            ->orderBy('code', 'asc')
            ->get();
        if ($selectedKbli && !$kblis->contains('id', $selectedKbli->id)) {
            $kblis->prepend($selectedKbli);
        }
        
        // Master data Satker
        $satkers = config('satker', []);
        
        return view('portal.application.create', compact('selectedKbli', 'kblis', 'satkers'));
    }

    public function store(\App\Http\Requests\StoreApplicationRequest $request, \App\Services\SubmissionService $submissionService)
    {
        $application = $submissionService->submitApplication(
            $request->validated(),
            $request->file('document'),
            \Illuminate\Support\Facades\Auth::user()
        );

        return redirect()->route('tracking.show', ['code' => $application->tracking_code])
                         ->with('success', 'Permohonan berhasil diajukan! Harap simpan kode pelacakan Anda.');
    }

    public function track()
    {
        return view('portal.application.track');
    }

    public function showTracking(Request $request)
    {
        $code = $request->input('code') ?? $request->route('code');
        
        if (empty($code)) {
            return redirect()->route('tracking.index')->withErrors(['code' => 'Masukkan kode pelacakan yang valid.']);
        }

        $application = Application::where('tracking_code', $code)
            ->with(['kbliItem', 'histories' => function($query) {
                $query->orderBy('created_at', 'desc')->with('actor:id,name');
            }])
            ->first();

        if (!$application) {
            return redirect()->route('tracking.index')->withErrors(['code' => 'Permohonan tidak ditemukan dengan kode pelacakan yang diberikan.']);
        }

        return view('portal.application.tracking_result', compact('application'));
    }
}
