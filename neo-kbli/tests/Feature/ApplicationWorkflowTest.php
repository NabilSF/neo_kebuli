<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\KbliCategory;
use App\Models\KbliSection;
use App\Models\KbliItem;
use App\Models\Application;
use App\Models\Media;
use App\Enums\WorkflowState;
use App\Events\ApplicationApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $applicant;
    private User $reviewer;
    private User $admin;
    private KbliItem $kbliItem;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->applicant = User::factory()->create();
        
        $this->reviewer = User::factory()->create();
        $this->reviewer->assignRole('Admin');
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        $category = KbliCategory::create(['code' => 'A', 'name' => 'Cat A']);
        $section = KbliSection::create(['category_id' => $category->id, 'code' => '01', 'name' => 'Sec 01']);
        $this->kbliItem = KbliItem::create(['section_id' => $section->id, 'code' => '01111', 'name' => 'KBLI A']);
    }

    private function makeTestApplication(User $user, $status = WorkflowState::SUBMITTED): Application
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
            'status' => $status->value,
            'submitted_at' => now(),
            'reviewed_by' => $status !== WorkflowState::SUBMITTED ? $this->reviewer->id : null,
        ]);
    }

    public function test_ui_user_cannot_view_other_applications()
    {
        $otherUser = User::factory()->create();
        $application = $this->makeTestApplication($otherUser);

        $response = $this->actingAs($this->applicant)->get(route('user.applications.show', $application));
        $response->assertStatus(403);
    }

    public function test_unauthorized_reviewer_cannot_approve()
    {
        $application = $this->makeTestApplication($this->applicant, WorkflowState::UNDER_REVIEW);
        
        $unauthorizedReviewer = User::factory()->create();
        $unauthorizedReviewer->assignRole('Operator');

        $response = $this->actingAs($unauthorizedReviewer)->post(route('admin.applications.approve', $application), [
            'notes' => 'Coba approve'
        ]);

        $response->assertStatus(403);
    }

    public function test_authorized_reviewer_can_approve_and_creates_history_and_invalidates_cache()
    {
        $application = $this->makeTestApplication($this->applicant, WorkflowState::UNDER_REVIEW);
        
        // Explicitly set version to 1 to test increment
        Cache::put("user.{$this->applicant->id}.dashboard.version", 1);
        $cacheKey = \App\Helpers\CacheKey::userDashboard($this->applicant->id);
        Cache::put($cacheKey, 'dummy', now()->addMinutes(5));

        $this->reviewer->load('roles', 'permissions');
        if (!\Illuminate\Support\Facades\Gate::forUser($this->reviewer)->allows('approve', $application)) {
            dd('Gate allows failed', $this->reviewer->roles->pluck('name'), $this->reviewer->getAllPermissions()->pluck('name'));
        }

        $response = $this->actingAs($this->reviewer)->post(route('admin.applications.approve', $application), [
            'decision' => WorkflowState::APPROVED->value,
            'notes' => 'Terlihat bagus',
            'code' => '11111',
            'section_id' => $this->kbliItem->section_id,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => WorkflowState::APPROVED->value
        ]);

        // History
        $this->assertDatabaseHas('application_histories', [
            'application_id' => $application->id,
            'status' => WorkflowState::APPROVED->value,
            'created_by' => $this->reviewer->id
        ]);

        // Cache must be invalidated (version bumped)
        $newCacheKey = \App\Helpers\CacheKey::userDashboard($this->applicant->id);
        $this->assertNotEquals($cacheKey, $newCacheKey);
        $this->assertNull(Cache::get($newCacheKey));
    }

    public function test_invalid_workflow_transition_is_prevented()
    {
        // Already approved
        $application = $this->makeTestApplication($this->applicant, WorkflowState::APPROVED);

        $response = $this->actingAs($this->admin)->post(route('admin.applications.approve', $application), [
            'decision' => WorkflowState::REJECTED->value,
            'notes' => 'Tolak'
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => WorkflowState::APPROVED->value
        ]);
    }

    public function test_user_cannot_download_foreign_media()
    {
        $otherUser = User::factory()->create();
        $application = $this->makeTestApplication($otherUser);
        
        $media = Media::create([
            'application_id' => $application->id,
            'path' => 'dummy.pdf',
            'stored_name' => 'dummy.pdf',
            'original_name' => 'dummy.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'checksum' => '123'
        ]);

        $response = $this->actingAs($this->applicant)->get(route('user.applications.download-media', [$application, $media]));
        
        $response->assertStatus(403);
    }
}
