<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\KbliCategory;
use App\Models\KbliSection;
use App\Models\KbliItem;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    private function createKbliItem(): KbliItem
    {
        $category = KbliCategory::create([
            'code'        => 'A',
            'name'        => 'Pertanian, Kehutanan dan Perikanan',
            'description' => 'Kategori A',
        ]);

        $section = KbliSection::create([
            'category_id' => $category->id,
            'code'        => '01',
            'name'        => 'Pertanian Tanaman, Peternakan',
            'description' => 'Sektor 01',
        ]);

        return KbliItem::create([
            'section_id'  => $section->id,
            'code'        => '01111',
            'name'        => 'Pertanian Jagung',
            'description' => 'Budidaya tanaman jagung',
            'level'       => 'item',
        ]);
    }

    private function createTestApplication(array $attributes = []): Application
    {
        return Application::create(array_merge([
            'user_id'             => 1,
            'kbli_item_id'        => 1,
            'tracking_code'       => 'KBLI-20260728-DEF1234',
            'product_name'        => 'Produk Sample',
            'product_material'    => 'Bahan Baku Sample',
            'production_process'  => 'Proses Produksi Sample',
            'product_description' => 'Deskripsi Produk Sample',
            'saker_address'       => '3300',
            'status'              => 'submitted',
            'submitted_at'        => now(),
        ], $attributes));
    }

    public function test_user_can_submit_application(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;
        $kbli = $this->createKbliItem();

        $file = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/applications', [
                'kbli_item_id'        => $kbli->id,
                'product_name'        => 'Benih Jagung Hibrida',
                'product_material'    => 'Jagung Unggul',
                'production_process'  => 'Pengeringan & Pemilahan',
                'product_description' => 'Benih kualitas tinggi',
                'saker_address'       => '3300',
                'document'            => $file,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'tracking_code',
                    'product_name',
                    'status',
                    'histories',
                    'media',
                ],
            ]);

        $this->assertDatabaseHas('applications', [
            'user_id'      => $user->id,
            'product_name' => 'Benih Jagung Hibrida',
        ]);
    }

    public function test_user_can_list_their_applications(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;
        $kbli = $this->createKbliItem();

        $this->createTestApplication([
            'user_id'       => $user->id,
            'kbli_item_id'  => $kbli->id,
            'tracking_code' => 'KBLI-20260728-AAA1111',
            'product_name'  => 'Produk A',
        ]);

        $this->createTestApplication([
            'user_id'       => $user->id,
            'kbli_item_id'  => $kbli->id,
            'tracking_code' => 'KBLI-20260728-BBB2222',
            'product_name'  => 'Produk B',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/v1/applications');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_idor_prevention_on_application_detail(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token1 = $user1->createToken('test_token')->plainTextToken;
        $kbli = $this->createKbliItem();

        $applicationUser2 = $this->createTestApplication([
            'user_id'       => $user2->id,
            'kbli_item_id'  => $kbli->id,
            'tracking_code' => 'KBLI-20260728-USER2APP',
            'product_name'  => 'Produk User 2',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token1")
            ->getJson("/api/v1/applications/{$applicationUser2->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pengajuan ini.',
            ]);
    }

    public function test_public_tracking_works_without_auth(): void
    {
        $kbli = $this->createKbliItem();
        $user = User::factory()->create();

        $this->createTestApplication([
            'user_id'       => $user->id,
            'kbli_item_id'  => $kbli->id,
            'tracking_code' => 'KBLI-20260728-TEST1234',
            'product_name'  => 'Pupuk Organik',
        ]);

        $response = $this->getJson('/api/v1/applications/track/KBLI-20260728-TEST1234');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'tracking_code' => 'KBLI-20260728-TEST1234',
                    'product_name'  => 'Pupuk Organik',
                ],
            ]);
    }

    public function test_user_can_upload_media_attachment(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;
        $kbli = $this->createKbliItem();

        $application = $this->createTestApplication([
            'user_id'       => $user->id,
            'kbli_item_id'  => $kbli->id,
            'tracking_code' => 'KBLI-20260728-MEDIA123',
            'product_name'  => 'Produk Media',
        ]);

        $file = UploadedFile::fake()->create('foto_produk.jpg', 300, 'image/jpeg');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/applications/{$application->id}/media", [
                'document' => $file,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Dokumen lampiran berhasil diunggah',
            ]);

        $this->assertDatabaseHas('media', [
            'application_id' => $application->id,
            'original_name'  => 'foto_produk.jpg',
        ]);
    }
}
