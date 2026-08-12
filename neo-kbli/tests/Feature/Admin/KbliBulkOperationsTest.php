<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\KbliSection;
use App\Models\KbliCategory;
use App\Models\KbliItem;
use App\Models\ActivityLog;

class KbliBulkOperationsTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;
    protected User $admin;
    protected User $superAdmin;
    protected KbliSection $section;
    protected KbliItem $item1;
    protected KbliItem $item2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        
        $this->admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin->roles()->attach($adminRole);
        
        $this->superAdmin = User::factory()->create();
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $this->superAdmin->roles()->attach($superAdminRole);
        
        $category = KbliCategory::create(['code' => 'A', 'name' => 'Cat A', 'description' => '-']);
        $this->section = KbliSection::create(['category_id' => $category->id, 'code' => 'A', 'name' => 'Sec A', 'description' => '-']);
        
        $this->item1 = KbliItem::create(['section_id' => $this->section->id, 'code' => '11111', 'name' => 'Item 1', 'is_active' => false]);
        $this->item2 = KbliItem::create(['section_id' => $this->section->id, 'code' => '22222', 'name' => 'Item 2', 'is_active' => true]);
    }

    public function test_user_cannot_bulk_activate()
    {
        $this->actingAs($this->user);
        
        $response = $this->post(route('admin.kbli.items.bulk-update'), [
            'bulk_ids' => [$this->item1->id],
            'bulk_action' => 'activate'
        ]);
        
        $response->assertForbidden();
    }

    public function test_admin_can_bulk_activate()
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('admin.kbli.items.bulk-update'), [
            'bulk_ids' => [$this->item1->id],
            'bulk_action' => 'activate'
        ]);
        
        $response->assertRedirect(route('admin.kbli.items.index'));
        $this->assertDatabaseHas('kbli_items', ['id' => $this->item1->id, 'is_active' => true]);
        
        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'kbli.bulk_activated',
            'subject_type' => KbliItem::class,
            'subject_id' => $this->item1->id
        ]);
    }

    public function test_super_admin_can_bulk_deactivate()
    {
        $this->actingAs($this->superAdmin);
        
        $response = $this->post(route('admin.kbli.items.bulk-update'), [
            'bulk_ids' => [$this->item2->id],
            'bulk_action' => 'deactivate'
        ]);
        
        $response->assertRedirect(route('admin.kbli.items.index'));
        $this->assertDatabaseHas('kbli_items', ['id' => $this->item2->id, 'is_active' => false]);
    }

    public function test_empty_selection_is_rejected()
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('admin.kbli.items.bulk-update'), [
            'bulk_ids' => [],
            'bulk_action' => 'activate'
        ]);
        
        $response->assertSessionHasErrors('bulk_ids');
    }

    public function test_invalid_action_is_rejected()
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('admin.kbli.items.bulk-update'), [
            'bulk_ids' => [$this->item1->id],
            'bulk_action' => 'delete' // Not allowed
        ]);
        
        $response->assertSessionHasErrors('bulk_action');
    }

    public function test_nonexistent_ids_are_rejected()
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('admin.kbli.items.bulk-update'), [
            'bulk_ids' => [999999],
            'bulk_action' => 'activate'
        ]);
        
        $response->assertSessionHasErrors('bulk_ids.0');
    }
}
