<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username'      => $request->username,
            'email'         => $request->email,
            'password_hash' => $request->password, // cast to 'hashed' in User model
        ]);

        \Spatie\Permission\Models\Role::findOrCreate('user', 'web');
        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('home', absolute: false));
    }
}

