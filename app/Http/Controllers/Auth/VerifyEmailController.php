<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Jika sudah terverifikasi, langsung arahkan ke dashboard yang benar
            if ($request->user()->hasRole('asesi')) {
                return redirect()->intended(route('asesi.dashboard').'?verified=1');
            } elseif ($request->user()->hasAnyRole(['admin', 'asesor'])) {
                return redirect()->intended(route('admin.dashboard').'?verified=1');
            }
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Setelah berhasil verifikasi, arahkan ke dashboard yang benar
        if ($request->user()->hasRole('asesi')) {
            return redirect()->intended(route('asesi.dashboard').'?verified=1');
        } elseif ($request->user()->hasAnyRole(['admin', 'asesor'])) {
            return redirect()->intended(route('admin.dashboard').'?verified=1');
        }

        return redirect()->intended('/?verified=1');
    }
}
