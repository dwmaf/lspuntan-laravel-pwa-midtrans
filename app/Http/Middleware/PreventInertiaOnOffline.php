<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventInertiaOnOffline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika request adalah untuk halaman offline, hapus header X-Inertia
        // agar middleware HandleInertiaRequests menganggapnya sebagai request biasa.
        if ($request->routeIs('laravelpwa.offline')) {
            $request->headers->remove('X-Inertia');
        }

        return $next($request);
    }
}
