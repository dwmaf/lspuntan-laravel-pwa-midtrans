<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = null;
        DB::transaction(function () use ($request, &$user) {
            $createdUser = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $createdUser->assignRole('asesi');
            Student::create([
                'user_id' => $createdUser->id,
            ]);
            event(new Registered($createdUser));
            $user = $createdUser;
        });
        Auth::login($user);

        return redirect(route('asesi.dashboard', absolute: false));
    }
}
