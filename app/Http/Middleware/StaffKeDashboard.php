<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin & owner tidak boleh memakai halaman pelanggan (beranda, katalog, keranjang, akun, ...).
 * Kalau staff yang masih login membuka halaman pelanggan, langsung dibawa ke dashboard admin.
 */
class StaffKeDashboard
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && in_array($user->role, ['admin', 'owner'], true)
            && ! $request->routeIs('admin.*', 'logout')) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
