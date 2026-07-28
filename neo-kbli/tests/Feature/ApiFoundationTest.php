<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_kbli_categories_returns_json_contract()
    {
        $response = $this->getJson('/api/v1/kbli/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total']
            ]);
    }

    public function test_kbli_search_requires_query()
    {
        $response = $this->getJson('/api/v1/kbli/search');

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['q']
            ]);
    }

    public function test_get_news_list_returns_json_contract()
    {
        $response = $this->getJson('/api/v1/news');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta'
            ]);
    }

    public function test_non_existing_kbli_code_returns_404_json()
    {
        $response = $this->getJson('/api/v1/kbli/9999999999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message'
            ]);
    }

    public function test_non_existing_news_slug_returns_404_json()
    {
        $response = $this->getJson('/api/v1/news/non-existing-slug-12345');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message'
            ]);
    }
}
