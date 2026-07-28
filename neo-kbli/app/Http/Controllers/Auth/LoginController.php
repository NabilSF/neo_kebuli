<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Check if user is active
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator untuk informasi lebih lanjut.',
                ]);
            }

            // Update last login
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->last_login_at = \Carbon\Carbon::now();
            $user->save();

            // Log activity
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'properties' => json_encode([
                    'module' => 'auth',
                    'description' => 'Pengguna berhasil masuk ke sistem.',
                    'user_agent' => $request->userAgent(),
                ]),
                'ip_address' => $request->ip(),
            ]);

            if ($user->hasAnyRole(['super-admin', 'superadmin', 'admin', 'operator'])) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'properties' => json_encode([
                    'module' => 'auth',
                    'description' => 'Pengguna berhasil keluar dari sistem.',
                    'user_agent' => $request->userAgent(),
                ]),
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
