<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\KbliCategory;
use App\Models\KbliSection;
use App\Models\KbliItem;
use App\Models\Application;

class WebFileUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $applicant;
    private KbliItem $kbliItem;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->applicant = User::factory()->create();

        $category = KbliCategory::create(['code' => 'A', 'name' => 'Cat A']);
        $section = KbliSection::create(['category_id' => $category->id, 'code' => '01', 'name' => 'Sec 01']);
        $this->kbliItem = KbliItem::create(['section_id' => $section->id, 'code' => '01111', 'name' => 'KBLI A']);
        
        Storage::fake('local');
    }

    private function makeTestApplication(User $user): Application
    {
        return Application::create([
            'user_id' => $user->id,
            'kbli_item_id' => $this->kbliItem->id,
            'tracking_code' => 'TRK-' . time(),
            'product_name' => 'Produk Tes',
            'product_material' => 'Material Tes',
            'production_process' => 'Proses Tes',
            'product_description' => 'Desc Tes',
            'saker_address' => '3300',
            'status' => \App\Enums\WorkflowState::SUBMITTED->value,
            'submitted_at' => now(),
        ]);
    }

    public function test_user_can_upload_valid_media_to_their_application()
    {
        $application = $this->makeTestApplication($this->applicant);

        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->applicant)->post(route('user.applications.upload-media', $application), [
            'document' => $file,
        ]);

        $response->assertRedirect(route('user.applications.show', $application));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('media', [
            'application_id' => $application->id,
            'original_name' => 'document.pdf',
        ]);
    }

    public function test_user_cannot_upload_invalid_mime_type()
    {
        $application = $this->makeTestApplication($this->applicant);

        // Upload executable file (invalid mime)
        $file = UploadedFile::fake()->create('malicious.exe', 1024, 'application/x-msdownload');

        $response = $this->actingAs($this->applicant)->post(route('user.applications.upload-media', $application), [
            'document' => $file,
        ]);

        $response->assertSessionHasErrors(['document']);
        $this->assertDatabaseMissing('media', [
            'application_id' => $application->id,
            'original_name' => 'malicious.exe',
        ]);
    }

    public function test_user_cannot_upload_to_others_application()
    {
        $otherUser = User::factory()->create();
        $otherApplication = $this->makeTestApplication($otherUser);

        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->applicant)->post(route('user.applications.upload-media', $otherApplication), [
            'document' => $file,
        ]);

        $response->assertStatus(403);
    }
}
