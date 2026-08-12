<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class PasswordResetWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_password_reset_workflow()
    {
        Notification::fake();
        $this->withSession(['_token' => 'testing-csrf-token']);

        // 1. Setup a test user
        $email = 'testreset@neokbli.local';
        $user = User::factory()->create([
            'name' => 'Test Reset',
            'email' => $email,
            'password' => Hash::make('OldPassword123!'),
        ]);

        // 2. Visit Forgot Password Page
        $response = $this->get('/password/reset');
        $response->assertStatus(200);

        // 3. Submit Email for Reset Link
        $response = $this->post('/password/email', [
            '_token' => 'testing-csrf-token',
            'email' => $email,
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // Retrieve the token from the notification
        $plainToken = null;
        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function ($notification) use (&$plainToken) {
                $plainToken = $notification->token;
                return true;
            }
        );
        $this->assertNotNull($plainToken);

        // 4. Visit Reset Password Page via Link
        $response = $this->get('/password/reset/' . $plainToken . '?email=' . urlencode($email));
        $response->assertStatus(200);

        // 5. Submit New Password
        $response = $this->post('/password/reset', [
            '_token' => 'testing-csrf-token',
            'token' => $plainToken,
            'email' => $email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/login'); // Should redirect to login as per controller

        // 6. Login with New Password
        $response = $this->post('/login', [
            '_token' => 'testing-csrf-token',
            'login' => $email,
            'password' => 'NewPassword123!',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/dashboard'); 
        
        // Actually, LoginController normally redirects to intended or '/'. Let's just assert redirect.
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
}
