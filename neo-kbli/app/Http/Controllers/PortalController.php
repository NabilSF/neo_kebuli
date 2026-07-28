<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KbliCategory;
use App\Models\KbliItem;
use App\Models\SearchLog;

class PortalController extends Controller
{
    public function index()
    {
        // 2-digit Categories ONLY (No deep hierarchy loading)
        $categories = KbliCategory::where('is_active', true)
            ->select('id', 'code', 'name', 'description')
            ->orderBy('sort_order', 'asc')
            ->get();

        $totalKbli = KbliItem::where('is_active', true)->count();
        
        // Popular Searches from real tracking
        $popularSearches = SearchLog::orderBy('frequency', 'desc')
            ->limit(5)
            ->get();

        return view('portal.home', compact('categories', 'totalKbli', 'popularSearches'));
    }

    public function search(Request $request, \App\Services\SearchService $searchService)
    {
        $query = $request->input('q');
        $items = $searchService->searchKbli($query);

        return view('portal.search', compact('items', 'query'));
    }

    public function show($code)
    {
        $item = KbliItem::where('code', $code)
            ->where('is_active', true)
            ->with(['section.category'])
            ->firstOrFail();

        return view('portal.kbli.show', compact('item'));
    }
    public function newsList()
    {
        $news = \App\Models\News::with(['category', 'author'])
            ->where('status', 'published')
            ->latest()
            ->paginate(10);
        return view('portal.news.index', compact('news'));
    }

    public function newsShow(\App\Models\News $article)
    {
        if ($article->status !== 'published') {
            abort(404);
        }
        $article->load(['category', 'author']);
        
        $others = \App\Models\News::where('status','published')
            ->where('id', '!=', $article->id)
            ->latest()
            ->limit(3)
            ->get();

        return view('portal.news.show', compact('article', 'others'));
    }
}
