<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KbliItem;
use App\Models\KbliSection;
use Illuminate\Http\Request;
use App\Services\KbliItemService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KbliController extends Controller
{
    use AuthorizesRequests;

    protected KbliItemService $kbliItemService;

    public function __construct(KbliItemService $kbliItemService)
    {
        $this->kbliItemService = $kbliItemService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'section_id', 'is_active', 'category_id']);
        $sortBy = $request->input('sort', 'code');
        $direction = $request->input('direction', 'asc');

        $items = $this->kbliItemService->getPaginatedItems($filters, $sortBy, $direction);
        $sections = $this->kbliItemService->getSectionsForDropdown();
        $categories = $this->kbliItemService->getCategoriesForDropdown();

        return view('admin.kbli.index', compact('items', 'sections', 'categories', 'sortBy', 'direction'));
    }

    public function create()
    {
        $this->authorize('createDirect', KbliItem::class);
        $sections = KbliSection::pluck('name', 'id');
        return view('admin.kbli.create', compact('sections'));
    }

    public function store(\App\Http\Requests\Admin\StoreKbliItemRequest $request)
    {
        $this->authorize('createDirect', KbliItem::class);
        $validated = $request->validated();
        
        $this->kbliItemService->createKbliItem($validated);

        return redirect()->route('admin.kbli.items.index')
            ->with('success', 'Data KBLI berhasil ditambahkan.');
    }

    public function show(KbliItem $item)
    {
        $item->load('section.category');
        return view('admin.kbli.show', compact('item'));
    }

    public function edit(KbliItem $item)
    {
        $this->authorize('updateDirect', $item);
        $sections = KbliSection::pluck('name', 'id');
        return view('admin.kbli.edit', compact('item', 'sections'));
    }

    public function update(\App\Http\Requests\Admin\UpdateKbliItemRequest $request, KbliItem $item)
    {
        $this->authorize('updateDirect', $item);
        $validated = $request->validated();

        $this->kbliItemService->updateKbliItem($item, $validated);

        return redirect()->route('admin.kbli.items.index')
            ->with('success', 'Data KBLI berhasil diperbarui.');
    }

    public function destroy(KbliItem $item)
    {
        $this->authorize('deleteDirect', $item);
        
        try {
            $this->kbliItemService->deleteKbliItem($item);
            return redirect()->route('admin.kbli.items.index')
                ->with('success', 'Data KBLI berhasil dihapus.');
        } catch (\LogicException $e) {
            return redirect()->route('admin.kbli.items.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function template(string $format, \App\Services\KbliImportService $importService)
    {
        $this->authorize('import', KbliItem::class);
        abort_unless(in_array($format, ['csv', 'xlsx']), 404);
        
        return $importService->generateTemplate($format);
    }

    public function importForm()
    {
        $this->authorize('import', KbliItem::class);
        return view('admin.kbli.import');
    }

    public function importPreview(Request $request, \App\Services\KbliImportService $importService)
    {
        $this->authorize('import', KbliItem::class);
        
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240'
        ]);

        try {
            $cacheKey = $importService->parseAndPreview($request->file('file'));
            return view('admin.kbli.import_preview', [
                'cacheKey' => $cacheKey,
                'preview' => \Illuminate\Support\Facades\Cache::get($cacheKey)
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function importConfirm(Request $request, \App\Services\KbliImportService $importService)
    {
        $this->authorize('import', KbliItem::class);
        
        $request->validate([
            'cache_key' => 'required|string'
        ]);

        try {
            $importService->confirmImport($request->input('cache_key'));
            return redirect()->route('admin.kbli.items.index')
                ->with('success', 'Data KBLI berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('admin.kbli.import.form')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function bulkUpdate(\App\Http\Requests\Admin\BulkUpdateKbliRequest $request)
    {
        $validated = $request->validated();

        try {
            $affected = $this->kbliItemService->bulkUpdateStatus($validated['bulk_ids'], $validated['bulk_action']);
            $actionLabel = $validated['bulk_action'] === 'activate' ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->route('admin.kbli.items.index')
                ->with('success', "Berhasil: {$affected} KBLI telah {$actionLabel}.");
        } catch (\Exception $e) {
            return redirect()->route('admin.kbli.items.index')
                ->with('error', 'Bulk update gagal: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $this->authorize('export', KbliItem::class);
        $format = $request->input('format', 'xlsx');
        
        $fileName = 'kbli_export_' . date('Ymd_His') . '.' . $format;
        return (new \App\Services\KbliExportService)->download($fileName);
    }
}
