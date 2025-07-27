<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                /** @var \App\Models\User $user */
                $user = $request->user();

                // Cek role user dan arahkan ke dashboard yang sesuai
                if ($user->hasAnyRole(['admin','asesor'])) {
                    return redirect(route('admin.dashboard'));
                }
                
                // Untuk role lain, arahkan ke dashboard default
                return redirect(route('asesi.dashboard'));
            }
        }

        return $next($request);
    }
}
