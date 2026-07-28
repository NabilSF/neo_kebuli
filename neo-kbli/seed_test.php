<?php
$cat = App\Models\NewsCategory::firstOrCreate(['name' => 'Umum', 'slug' => 'umum']);
App\Models\News::create([
    'title' => 'Berita E2E Test', 
    'slug' => 'berita-e2e-test', 
    'content' => 'Ini adalah konten berita E2E Test.', 
    'status' => 'published', 
    'news_category_id' => $cat->id
]);
echo "Done";
