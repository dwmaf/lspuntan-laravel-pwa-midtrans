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
                $user = Auth::user();

                // Cek role user dan arahkan ke dashboard yang sesuai
                if (in_array($user->role, ['admin', 'asesor'])) {
                    return redirect(route('dashboardadmin'));
                }
                
                // Untuk role lain, arahkan ke dashboard default
                return redirect(route('dashboard'));
            }
        }

        return $next($request);
    }
}
