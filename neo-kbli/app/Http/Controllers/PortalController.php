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
        $len = strlen($code);

        if (!in_array($len, [2, 3, 4, 5])) {
            abort(404);
        }

        if ($len === 5) {
            $item = KbliItem::where('code', $code)
                ->where('is_active', true)
                ->select(['id', 'code', 'name', 'description', 'section_id'])
                ->with(['section:id,code,name,category_id', 'section.category:id,code,name'])
                ->firstOrFail();

            return view('portal.kbli.show', compact('item'));
        }

        // For 2, 3, or 4 digit codes (Hierarchical Drilldown)
        $golonganPokokCode = substr($code, 0, 2);
        $section = \App\Models\KbliSection::where('code', $golonganPokokCode)->with('category')->first();

        if ($len === 2) {
            $title = \App\Helpers\Kbli2025Helper::getName($code);
            $levelName = "Golongan Pokok (2 Digit)";
            $description = "Mengelompokkan kegiatan yang masih sejenis dalam satu kategori utama.";
            
            // Get 3-digit children
            $children = KbliItem::where('code', 'LIKE', $code . '%')
                ->where('is_active', true)
                ->selectRaw('LEFT(code, 3) as child_code, COUNT(*) as total_items')
                ->groupBy('child_code')
                ->orderBy('child_code')
                ->get()
                ->map(function ($item) {
                    return [
                        'code' => $item->child_code,
                        'name' => \App\Helpers\Kbli2025Helper::getName($item->child_code),
                        'total_items' => $item->total_items,
                        'subtitle' => "3 Digit • Merinci jenis aktivitas"
                    ];
                });

            return view('portal.kbli.hierarchy', compact('code', 'len', 'title', 'levelName', 'description', 'section', 'children'));
        }

        if ($len === 3) {
            $title = \App\Helpers\Kbli2025Helper::getName($code);
            $levelName = "Golongan (3 Digit)";
            $description = "Merinci golongan pokok " . $golonganPokokCode . " (" . \App\Helpers\Kbli2025Helper::getName($golonganPokokCode) . ") berdasarkan jenis aktivitas.";

            // Get 4-digit children
            $children = KbliItem::where('code', 'LIKE', $code . '%')
                ->where('is_active', true)
                ->selectRaw('LEFT(code, 4) as child_code, COUNT(*) as total_items')
                ->groupBy('child_code')
                ->orderBy('child_code')
                ->get()
                ->map(function ($item) {
                    return [
                        'code' => $item->child_code,
                        'name' => \App\Helpers\Kbli2025Helper::getName($item->child_code),
                        'total_items' => $item->total_items,
                        'subtitle' => "4 Digit • Merinci kegiatan dalam golongan"
                    ];
                });

            return view('portal.kbli.hierarchy', compact('code', 'len', 'title', 'levelName', 'description', 'section', 'children'));
        }

        if ($len === 4) {
            $title = \App\Helpers\Kbli2025Helper::getName($code);
            $levelName = "Subgolongan (4 Digit)";
            $description = "Merinci lagi kegiatan dalam golongan " . substr($code, 0, 3) . " (" . \App\Helpers\Kbli2025Helper::getName(substr($code, 0, 3)) . ") menjadi kelompok spesifik.";

            // Get 5-digit children
            $children = KbliItem::where('code', 'LIKE', $code . '%')
                ->where('is_active', true)
                ->select(['code', 'name', 'description'])
                ->orderBy('code')
                ->get()
                ->map(function ($item) {
                    return [
                        'code' => $item->code,
                        'name' => \App\Helpers\Kbli2025Helper::getName($item->code),
                        'total_items' => 1,
                        'subtitle' => "5 Digit • Kelompok (Jenis usaha spesifik)"
                    ];
                });

            return view('portal.kbli.hierarchy', compact('code', 'len', 'title', 'levelName', 'description', 'section', 'children'));
        }
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
