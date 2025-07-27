<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            if ($user->hasRole('asesi')) {
                return redirect()->intended(route('asesi.dashboard'));
            } elseif ($user->hasAnyRole(['admin', 'asesor'])) {
                return redirect()->intended(route('admin.dashboard'));
            }
        }

        return view('auth.verify-email');
    }
}
