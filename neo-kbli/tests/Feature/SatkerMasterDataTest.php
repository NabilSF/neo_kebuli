<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\KbliCategory;
use App\Models\KbliSection;
use App\Models\KbliItem;

class SatkerMasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_satker_is_available_and_has_36_items()
    {
        $satkers = config('satker');
        
        $this->assertIsArray($satkers);
        $this->assertCount(36, $satkers);
        
        // Cek beberapa sampel untuk memastikan kebenaran data
        $this->assertEquals('BPS Provinsi Jawa Tengah', $satkers['3300']);
        $this->assertEquals('BPS Kabupaten Klaten', $satkers['3310']);
        $this->assertEquals('BPS Kota Surakarta', $satkers['3372']);
    }

    public function test_application_form_accepts_valid_satker_code()
    {
        Storage::fake('private');
        
        $user = User::factory()->create();
        
        $category = KbliCategory::create(['code' => 'A', 'name' => 'Cat A']);
        $section = KbliSection::create(['category_id' => $category->id, 'code' => '01', 'name' => 'Sec 01']);
        $kbli = KbliItem::create(['section_id' => $section->id, 'code' => '01111', 'name' => 'KBLI A', 'is_active' => true]);

        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user)->post(route('application.store'), [
            'kbli_item_id' => $kbli->id,
            'product_name' => 'Produk A',
            'product_material' => 'Material A',
            'production_process' => 'Proses A',
            'saker_address' => '3310', // Valid Satker code
            'document' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'saker_address' => '3310',
        ]);
    }

    public function test_application_form_rejects_invalid_satker_code()
    {
        Storage::fake('private');
        
        $user = User::factory()->create();
        
        $category = KbliCategory::create(['code' => 'A', 'name' => 'Cat A']);
        $section = KbliSection::create(['category_id' => $category->id, 'code' => '01', 'name' => 'Sec 01']);
        $kbli = KbliItem::create(['section_id' => $section->id, 'code' => '01111', 'name' => 'KBLI A', 'is_active' => true]);

        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user)->post(route('application.store'), [
            'kbli_item_id' => $kbli->id,
            'product_name' => 'Produk A',
            'product_material' => 'Material A',
            'production_process' => 'Proses A',
            'saker_address' => '9999', // Invalid Satker code
            'document' => $file,
        ]);

        $response->assertSessionHasErrors(['saker_address']);
        $this->assertDatabaseMissing('applications', [
            'user_id' => $user->id,
            'saker_address' => '9999',
        ]);
    }

    public function test_api_application_submission_accepts_valid_satker_code()
    {
        Storage::fake('private');
        
        $user = User::factory()->create();
        
        $category = KbliCategory::create(['code' => 'B', 'name' => 'Cat B']);
        $section = KbliSection::create(['category_id' => $category->id, 'code' => '02', 'name' => 'Sec 02']);
        $kbli = KbliItem::create(['section_id' => $section->id, 'code' => '02111', 'name' => 'KBLI B', 'is_active' => true]);

        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user, 'sanctum')->postJson(route('api.v1.applications.store'), [
            'kbli_item_id' => $kbli->id,
            'product_name' => 'Produk B',
            'product_material' => 'Material B',
            'production_process' => 'Proses B',
            'saker_address' => '3310', // Valid Satker code
            'document' => $file,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'saker_address' => '3310',
        ]);
    }

    public function test_api_application_submission_rejects_invalid_satker_code()
    {
        Storage::fake('private');
        
        $user = User::factory()->create();
        
        $category = KbliCategory::create(['code' => 'C', 'name' => 'Cat C']);
        $section = KbliSection::create(['category_id' => $category->id, 'code' => '03', 'name' => 'Sec 03']);
        $kbli = KbliItem::create(['section_id' => $section->id, 'code' => '03111', 'name' => 'KBLI C', 'is_active' => true]);

        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user, 'sanctum')->postJson(route('api.v1.applications.store'), [
            'kbli_item_id' => $kbli->id,
            'product_name' => 'Produk C',
            'product_material' => 'Material C',
            'production_process' => 'Proses C',
            'saker_address' => '9999', // Invalid Satker code
            'document' => $file,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['saker_address']);
                 
        $this->assertDatabaseMissing('applications', [
            'user_id' => $user->id,
            'saker_address' => '9999',
        ]);
    }
}
