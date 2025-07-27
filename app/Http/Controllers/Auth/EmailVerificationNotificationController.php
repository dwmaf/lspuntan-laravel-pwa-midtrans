<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            if ($user->hasRole('asesi')) {
                return redirect()->intended(route('asesi.dashboard'));
            } elseif ($user->hasAnyRole(['admin', 'asesor'])) {
                return redirect()->intended(route('admin.dashboard'));
            }
            // Fallback jika tidak ada role yang cocok
            return redirect()->intended('/');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
