<?php

namespace Tests\Feature;

use App\Enums\WorkflowState;
use App\Models\Application;
use App\Models\KbliItem;
use App\Models\KbliSection;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class ApplicationApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_admin_can_approve_application_and_create_kbli_item()
    {
        Event::fake([
            \App\Events\ApplicationApproved::class,
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $user = User::factory()->create();

        $category = \App\Models\KbliCategory::create([
            'code' => 'A',
            'name' => 'Pertanian',
            'description' => 'Test Category'
        ]);

        $section = KbliSection::create([
            'category_id' => $category->id,
            'code' => 'A',
            'name' => 'Pertanian',
            'description' => 'Test Section'
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'status' => WorkflowState::UNDER_REVIEW->value,
            'reviewed_by' => $admin->id,
            'tracking_code' => 'TRK-12345',
            'product_name' => 'Kopi Robusta',
            'product_description' => 'Bubuk kopi robusta murni',
            'product_material' => 'Test',
            'production_process' => 'Test',
            'saker_address' => '3300',
            'submitted_at' => now(),
        ]);

        $initialKbliCount = KbliItem::count();

        $response = $this->actingAs($admin)->post(route('admin.applications.approve', $application), [
            'decision' => 'approved',
            'code' => '11111',
            'section_id' => $section->id,
            'notes' => 'Disetujui',
        ]);
        
        $response->assertSessionHasNoErrors();

        
        if ($response->status() !== 302 || !session()->has('success')) {
            dd($response->status(), session()->all());
        }

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => WorkflowState::APPROVED->value,
        ]);

        $this->assertEquals($initialKbliCount + 1, KbliItem::count());

        $kbli = KbliItem::latest('id')->first();
        
        // G. Assert: kbli_items.code === submitted code
        $this->assertEquals('11111', $kbli->code);
        
        // H. Assert: is_active === true
        $this->assertTrue((bool)$kbli->is_active);
        
        // N. Assert tracking_code tidak masuk kbli_items.code
        $this->assertNotEquals('TRK-12345', $kbli->code);
        
        // Verify Application belongs to KbliItem
        $application->refresh();
        $this->assertEquals($kbli->id, $application->kbli_item_id);

        // I. Assert search publik menemukan item tersebut
        $searchResponse = $this->get('/search?query=11111');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('11111');
        $searchResponse->assertSee('Kopi Robusta');

        // K. Approve ulang application yang sama (simulate multiple requests)
        // Set application back to under review just to bypass ensureCanApprove for this test
        $application->update(['status' => WorkflowState::UNDER_REVIEW->value]);
        
        $response2 = $this->actingAs($admin)->post(route('admin.applications.approve', $application), [
            'decision' => 'approved',
            'code' => '11112', // even if code changes
            'section_id' => $section->id,
            'notes' => 'Approved Again',
        ]);

        // L. Assert jumlah kbli_items tidak bertambah
        $this->assertEquals($initialKbliCount + 1, KbliItem::count());
    }

    public function test_inactive_kbli_items_do_not_appear_in_public_search()
    {
        $category = \App\Models\KbliCategory::create([
            'code' => 'A',
            'name' => 'Pertanian',
            'description' => 'Test Category'
        ]);

        $section = KbliSection::create([
            'category_id' => $category->id,
            'code' => 'A',
            'name' => 'Pertanian',
            'description' => 'Test Section'
        ]);

        $inactiveKbli = KbliItem::create([
            'code' => '99999',
            'name' => 'Rahasia Negara',
            'description' => 'Tidak boleh tampil',
            'section_id' => $section->id,
            'is_active' => false,
        ]);

        // J. Assert inactive item tidak muncul
        $searchResponse = $this->get('/search?query=99999');
        $searchResponse->assertStatus(200);
        $searchResponse->assertDontSee('Rahasia Negara');
    }

    public function test_user_cannot_approve_application()
    {
        $user = User::factory()->create();

        $application = Application::create([
            'user_id' => $user->id,
            'status' => WorkflowState::UNDER_REVIEW->value,
            'tracking_code' => 'TRK-12345',
            'product_name' => 'Test Product',
            'product_description' => 'Test Description',
            'product_material' => 'Test Material',
            'production_process' => 'Test Process',
            'saker_address' => '3300',
            'submitted_at' => now(),
        ]);

        $category = \App\Models\KbliCategory::create([
            'code' => 'A',
            'name' => 'Pertanian',
            'description' => 'Test Category'
        ]);

        $section = KbliSection::create([
            'category_id' => $category->id,
            'code' => 'A',
            'name' => 'Pertanian',
            'description' => 'Test Section'
        ]);

        // M. Assert user biasa tidak dapat approve
        $response = $this->actingAs($user)->post(route('admin.applications.approve', $application), [
            'decision' => 'approved',
            'code' => '22222',
            'section_id' => $section->id,
            'notes' => 'User Approve',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_approve_with_invalid_code()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $user = User::factory()->create();

        $category = \App\Models\KbliCategory::create([
            'code' => 'B',
            'name' => 'Test Invalid Code',
            'description' => 'Test Category'
        ]);

        $section = KbliSection::create([
            'category_id' => $category->id,
            'code' => 'B',
            'name' => 'Test',
            'description' => 'Test Section'
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'status' => WorkflowState::UNDER_REVIEW->value,
            'reviewed_by' => $admin->id,
            'tracking_code' => 'TRK-99999',
            'product_name' => 'Test Product',
            'product_description' => 'Test',
            'product_material' => 'Test',
            'production_process' => 'Test',
            'saker_address' => '3300',
            'submitted_at' => now(),
        ]);

        // Attempt to approve with alphanumeric code
        $response = $this->actingAs($admin)->post(route('admin.applications.approve', $application), [
            'decision' => 'approved',
            'code' => 'KBLI1',
            'section_id' => $section->id,
            'notes' => 'Disetujui',
        ]);

        $response->assertSessionHasErrors('code');

        // Attempt to approve with wrong length code
        $response2 = $this->actingAs($admin)->post(route('admin.applications.approve', $application), [
            'decision' => 'approved',
            'code' => '1234',
            'section_id' => $section->id,
            'notes' => 'Disetujui',
        ]);

        $response2->assertSessionHasErrors('code');
    }
}
