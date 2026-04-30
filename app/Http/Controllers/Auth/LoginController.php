<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Rate limiting - keamanan brute force
        $key = 'login_attempts_' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Cek apakah user aktif
            if (!Auth::user()->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'username' => 'Akun Anda telah dinonaktifkan. Hubungi admin.',
                ]);
            }

            $user = Auth::user();

            // === SINGLE LOGIN: Force logout session lama ===
            $oldSessionId = $user->current_session_id;
            if ($oldSessionId && $oldSessionId !== $request->session()->getId()) {
                // Hapus session file lama (force logout device sebelumnya)
                $sessionPath = storage_path("framework/sessions/{$oldSessionId}");
                if (file_exists($sessionPath)) {
                    unlink($sessionPath);
                }

                // Catat di audit log: multiple login detected
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'force_logout',
                    'description' => "Multiple login terdeteksi: {$user->name} login dari IP baru ({$request->ip()}). Session lama di-terminate.",
                    'ip_address' => $request->ip(),
                ]);
            }

            RateLimiter::clear($key);
            $request->session()->regenerate();

            // Simpan session ID baru & info login
            $user->update([
                'current_session_id' => $request->session()->getId(),
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            \App\Models\AuditLog::log('login', "User login: {$user->name} (IP: {$request->ip()})");

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($key, 60);

        throw ValidationException::withMessages([
            'username' => 'Username atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        \App\Models\AuditLog::log('logout', "User logout: {$user->name}");

        // Clear session ID di database
        $user->update(['current_session_id' => null]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
