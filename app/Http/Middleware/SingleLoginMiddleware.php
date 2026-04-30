<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SingleLoginMiddleware
{
    /**
     * Cek apakah session user masih valid (belum di-force logout oleh login baru)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $currentSessionId = $request->session()->getId();

            // Jika session ID tidak cocok dengan yang tersimpan di DB,
            // berarti ada login baru dari device lain → force logout
            if ($user->current_session_id && $user->current_session_id !== $currentSessionId) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir karena akun ini login dari perangkat lain.');
            }
        }

        return $next($request);
    }
}
