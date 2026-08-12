<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsernameUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_rejects_duplicate_name()
    {
        $existingUser = User::factory()->create(['name' => 'ExistingUser']);

        $response = $this->post('/register', [
            'name' => 'ExistingUser',
            'email' => 'newuser@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'terms' => 'on',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_registration_accepts_unique_name()
    {
        $existingUser = User::factory()->create(['name' => 'ExistingUser']);

        $response = $this->post('/register', [
            'name' => 'UniqueUser',
            'email' => 'uniqueuser@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'name' => 'UniqueUser',
            'email' => 'uniqueuser@example.com',
        ]);
    }

    public function test_profile_update_rejects_another_users_name()
    {
        $existingUser = User::factory()->create(['name' => 'TakenName']);
        $currentUser = User::factory()->create(['name' => 'MyName']);

        $response = $this->actingAs($currentUser)->patch('/profile', [
            'name' => 'TakenName',
            'email' => $currentUser->email,
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('users', [
            'id' => $currentUser->id,
            'name' => 'MyName',
        ]);
    }

    public function test_profile_update_accepts_current_users_existing_name()
    {
        $currentUser = User::factory()->create(['name' => 'MyName']);

        $response = $this->actingAs($currentUser)->patch('/profile', [
            'name' => 'MyName',
            'email' => 'newemail@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $currentUser->id,
            'name' => 'MyName',
            'email' => 'newemail@example.com',
        ]);
    }

    public function test_login_via_unique_name_remains_functional()
    {
        $user = User::factory()->create([
            'name' => 'LoginUser',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'LoginUser',
            'password' => 'Password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
