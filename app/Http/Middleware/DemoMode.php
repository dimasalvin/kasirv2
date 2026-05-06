<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoMode
{
    /**
     * Routes yang di-block di demo mode (destructive actions).
     * User demo tetap bisa melakukan transaksi POS, tapi tidak bisa:
     * - Hapus data master (products, suppliers, categories, users)
     * - Ubah password / settings kritis
     */
    protected array $blockedRoutes = [
        'users.destroy',
        'products.destroy',
        'suppliers.destroy',
        'categories.destroy',
    ];

    /**
     * Blocked URI patterns (partial match).
     */
    protected array $blockedPatterns = [
        // Tidak ada yang di-block saat ini — demo full access
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Skip jika bukan demo mode
        if (!config('app.demo_mode')) {
            return $next($request);
        }

        // Block delete pada data master
        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, $this->blockedRoutes)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => '🔒 Demo Mode: Hapus data master dinonaktifkan.'
                ], 403);
            }

            return redirect()->back()->with('error', '🔒 Demo Mode: Hapus data master dinonaktifkan untuk menjaga data demo.');
        }

        return $next($request);
    }
}
