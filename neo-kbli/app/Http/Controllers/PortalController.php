<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KbliCategory;
use App\Models\KbliItem;
use App\Models\SearchLog;
use App\Services\SearchService;
use App\Services\NewsService;
use App\Enums\NewsStatus;

class PortalController extends Controller
{
    public function __construct(
        private NewsService $newsService,
        private SearchService $searchService
    ) {}
    public function index()
    {
        // 2-digit Categories ONLY (No deep hierarchy loading)
        $categories = KbliCategory::where('is_active', true)
            ->select('id', 'code', 'name', 'description')
            ->orderBy('sort_order', 'asc')
            ->get();

        $totalKbli = KbliItem::where('is_active', true)->count();
        
        // Popular Searches from real tracking
        $popularSearches = SearchLog::select(['id', 'keyword', 'frequency', 'last_searched_at'])
            ->orderBy('frequency', 'desc')
            ->limit(5)
            ->get();

        return view('portal.home', compact('categories', 'totalKbli', 'popularSearches'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $items = $this->searchService->searchKbli($query);

        return view('portal.search', compact('items', 'query'));
    }

    public function show($code)
    {
        $item = KbliItem::where('code', $code)
            ->where('is_active', true)
            ->select(['id', 'code', 'name', 'description', 'section_id'])
            ->with(['section:id,code,name,category_id', 'section.category:id,code,name'])
            ->firstOrFail();

        return view('portal.kbli.show', compact('item'));
    }
    public function newsList(Request $request)
    {
        $search = $request->input('q');
        $categorySlug = $request->input('category');
        
        $news = $this->newsService->getPaginatedNews(10, $search, $categorySlug);
        $latestNews = $this->newsService->getLatestNews(5);
        $newsCategories = $this->newsService->getCategoriesWithCount();

        return view('portal.news.index', compact('news', 'latestNews', 'newsCategories'));
    }

    public function newsShow(\App\Models\News $article)
    {
        if ($article->status !== NewsStatus::PUBLISHED->value) {
            abort(404);
        }
        $article->load(['category:id,name,slug', 'author:id,name']);
        
        $others = $this->newsService->getRelatedNews($article->id, 3);

        return view('portal.news.show', compact('article', 'others'));
    }
}
