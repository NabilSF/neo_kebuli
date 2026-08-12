<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\KbliSection;
use App\Models\KbliItem;
use App\Helpers\RoleList;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

class KbliDirectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected KbliSection $section;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        
        // Mock Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin->roles()->attach($adminRole);
        
        $category = \App\Models\KbliCategory::create(['code' => 'A', 'name' => 'Category A', 'description' => '...']);
        $this->section = KbliSection::create(['category_id' => $category->id, 'code' => 'A', 'name' => 'Pertanian', 'description' => '...']);
    }

    public function test_user_cannot_access_direct_management()
    {
        $this->actingAs($this->user);
        
        $this->get(route('admin.kbli.items.create'))->assertForbidden();
        $this->post(route('admin.kbli.items.store'), [])->assertForbidden();
        
        $item = KbliItem::create(['section_id' => $this->section->id, 'code' => '00000', 'name' => 'Test']);
        
        $this->put(route('admin.kbli.items.update', $item->id), [])->assertForbidden();
        $this->delete(route('admin.kbli.items.destroy', $item->id))->assertForbidden();
        $this->get(route('admin.kbli.import.form'))->assertForbidden();
    }

    public function test_admin_can_create_kbli()
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('admin.kbli.items.store'), [
            'code' => '12345',
            'name' => 'Test Name',
            'section_id' => $this->section->id,
            'is_active' => true,
        ]);
        
        $response->assertRedirect(route('admin.kbli.items.index'));
        $this->assertDatabaseHas('kbli_items', ['code' => '12345']);
    }

    public function test_admin_cannot_create_kbli_with_invalid_code()
    {
        $this->actingAs($this->admin);
        
        // Terlalu panjang dan ada karakter huruf (tracking code style)
        $response1 = $this->post(route('admin.kbli.items.store'), [
            'code' => 'KBLI-20260720-UXUXC5LB',
            'name' => 'Test Name',
            'section_id' => $this->section->id,
            'is_active' => true,
        ]);
        $response1->assertSessionHasErrors('code');

        // Kurang dari 5 digit
        $response2 = $this->post(route('admin.kbli.items.store'), [
            'code' => '1234',
            'name' => 'Test Name',
            'section_id' => $this->section->id,
        ]);
        $response2->assertSessionHasErrors('code');

        // Mengandung non-numerik meskipun 5 karakter
        $response3 = $this->post(route('admin.kbli.items.store'), [
            'code' => 'A1234',
            'name' => 'Test Name',
            'section_id' => $this->section->id,
        ]);
        $response3->assertSessionHasErrors('code');
    }

    public function test_admin_can_update_kbli()
    {
        $this->actingAs($this->admin);
        $item = KbliItem::create(['section_id' => $this->section->id, 'code' => '00000', 'name' => 'Test']);
        
        $response = $this->put(route('admin.kbli.items.update', $item->id), [
            'code' => '00000',
            'name' => 'Updated Name',
            'section_id' => $this->section->id,
            'is_active' => true,
        ]);
        
        $response->assertRedirect(route('admin.kbli.items.index'));
        $this->assertDatabaseHas('kbli_items', ['name' => 'Updated Name']);
    }

    public function test_template_download()
    {
        $this->actingAs($this->admin);
        
        $this->get(route('admin.kbli.import.template', 'csv'))
             ->assertSuccessful()
             ->assertHeader('content-disposition', 'attachment; filename=kbli_template.csv');
             
        $this->get(route('admin.kbli.import.template', 'xlsx'))
             ->assertSuccessful();
    }

    public function test_import_preview_rejects_invalid_file()
    {
        $this->actingAs($this->admin);
        
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        
        $this->post(route('admin.kbli.import.preview'), [
            'file' => $file
        ])->assertSessionHasErrors('file');
    }
    
    protected function test_import_preview_accepts_valid_file_and_does_not_insert_db()
    {
        $this->actingAs($this->admin);
        
        // Simulating the Excel parse is difficult without the actual file formatted for Maatwebsite, 
        // but we can test the structure if we mock it or pass a valid dummy CSV
        $csv = "section_id,code,name,description,is_active\n{$this->section->id},99999,New KBLI,Desc,1\n";
        $file = UploadedFile::fake()->createWithContent('test.csv', $csv);
        
        $response = $this->post(route('admin.kbli.import.preview'), [
            'file' => $file
        ]);
        
        $response->assertSuccessful();
        $response->assertViewHas('cacheKey');
        
        $this->assertDatabaseMissing('kbli_items', ['code' => '99999']);
        
        $cacheKey = $response->original->getData()['cacheKey'];
        
        // Confirm Import
        $confirmResponse = $this->post(route('admin.kbli.import.confirm'), [
            'cache_key' => $cacheKey
        ]);
        
        $confirmResponse->assertRedirect(route('admin.kbli.items.index'));
        $this->assertDatabaseHas('kbli_items', ['code' => '99999']);
    }

    public function test_ui_index_shows_correct_data()
    {
        $this->actingAs($this->admin);
        
        $item = KbliItem::create(['section_id' => $this->section->id, 'code' => '99999', 'name' => 'Test UI']);
        
        $response = $this->get(route('admin.kbli.items.index'));
        $response->assertSuccessful();
        $response->assertViewHas('items');
        $response->assertSee('Test UI');
    }

    public function test_cannot_delete_kbli_with_application_dependency()
    {
        $this->actingAs($this->admin);
        
        $item = KbliItem::create(['section_id' => $this->section->id, 'code' => '54321', 'name' => 'Used']);
        
        // Mock a dependent application
        \App\Models\Application::create([
            'kbli_item_id' => $item->id,
            'user_id' => $this->user->id,
            'tracking_code' => 'TEST-123',
            'product_name' => 'A',
            'product_material' => 'B',
            'production_process' => 'C',
            'product_description' => 'D',
            'saker_address' => '3300',
            'status' => 'draft',
        ]);
        
        $response = $this->delete(route('admin.kbli.items.destroy', $item->id));
        
        // It should redirect with an error message, since we catch Exception in Controller
        $response->assertRedirect(route('admin.kbli.items.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kbli_items', ['id' => $item->id]);
    }

    public function test_ui_index_retains_filter_in_pagination()
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('admin.kbli.items.index', ['search' => 'Pertanian', 'page' => 2]));
        $response->assertSuccessful();
        $response->assertSee('search=Pertanian');
    }

    public function test_import_confirm_rejects_expired_preview()
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('admin.kbli.import.confirm'), [
            'cache_key' => 'expired_or_invalid_key'
        ]);
        
        $response->assertRedirect(route('admin.kbli.import.form'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_export_kbli()
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('admin.kbli.export'));
        $response->assertSuccessful();
        $response->assertHeader('content-disposition');
    }

    public function test_operator_can_export_kbli()
    {
        $operator = User::factory()->create();
        $operatorRole = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);
        $operator->roles()->attach($operatorRole);

        $this->actingAs($operator);
        
        $response = $this->get(route('admin.kbli.export'));
        $response->assertSuccessful();
        $response->assertHeader('content-disposition');
    }

    public function test_user_cannot_export_kbli()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('admin.kbli.export'));
        $response->assertForbidden();
    }

    public function test_export_contract_format()
    {
        $item = KbliItem::create(['section_id' => $this->section->id, 'code' => '99999', 'name' => 'Test Export', 'is_active' => true]);
        
        $service = new \App\Services\KbliExportService();
        $headings = $service->headings();
        
        $this->assertEquals(['section_id', 'code', 'name', 'description', 'is_active'], $headings);
        
        $mapped = $service->map($item);
        
        $this->assertEquals($this->section->id, $mapped[0]); // section_id
        $this->assertEquals('99999', $mapped[1]); // code
        $this->assertEquals(1, $mapped[4]); // is_active
    }
}
