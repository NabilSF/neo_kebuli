<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Seeder;

class NewsCategorySeeder extends Seeder
{
    /**
     * Seed the news_categories table.
     * Idempotent: safe to run multiple times.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Pengumuman',     'slug' => 'pengumuman'],
            ['name' => 'Regulasi',       'slug' => 'regulasi'],
            ['name' => 'Berita Umum',    'slug' => 'berita-umum'],
            ['name' => 'KBLI Update',    'slug' => 'kbli-update'],
            ['name' => 'Perizinan',      'slug' => 'perizinan'],
        ];

        foreach ($categories as $category) {
            NewsCategory::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
