<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KbliCategory;
use App\Models\KbliItem;
use App\Models\KbliSection;
use Illuminate\Http\Request;

class KbliController extends Controller
{
    public function categories(Request $request)
    {
        $query = KbliCategory::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('sort_order', 'asc')->paginate(15);
        return view('admin.kbli.categories', compact('categories'));
    }

    public function sections(Request $request)
    {
        $query = KbliSection::with('category');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $sections = $query->orderBy('code', 'asc')->paginate(15);
        $categories = KbliCategory::select('id', 'code', 'name')->orderBy('sort_order', 'asc')->get();
        
        return view('admin.kbli.sections', compact('sections', 'categories'));
    }

    public function items(Request $request)
    {
        $query = KbliItem::with(['section.category']);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter Category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->whereHas('section', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filter Section
        if ($request->has('section_id') && $request->section_id != '') {
            $query->where('section_id', $request->section_id);
        }

        $items = $query->orderBy('code', 'asc')->paginate(15);
        
        $categories = KbliCategory::select('id', 'code', 'name')->orderBy('sort_order', 'asc')->get();
        // If category is selected, load sections for that category, else load all or none. Let's load all for simplicity or based on request
        $sections = KbliSection::select('id', 'category_id', 'code', 'name')->orderBy('code', 'asc')->get();

        return view('admin.kbli.items', compact('items', 'categories', 'sections'));
    }

    public function showItem($id)
    {
        $item = KbliItem::with(['section.category'])->findOrFail($id);
        return view('admin.kbli.show', compact('item'));
    }
}
