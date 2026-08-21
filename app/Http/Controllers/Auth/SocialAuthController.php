<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the OAuth provider's login page.
     * Supported providers: facebook, twitter, google
     */
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the OAuth provider.
     * Finds an existing user by email or creates a new one,
     * then links the provider account in social_accounts.
     */
    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            // User denied access or provider returned an error
            error_log('Social login error for provider ' . $provider . ': ' . $e->getMessage());
            return redirect()->route('login')
                ->withErrors(['email' => __('Social login failed or was cancelled. Please try again.')]);
        }

        // 1. Try to find an existing social_accounts record for this provider
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            // Already linked — just log in
            Auth::login($socialAccount->user);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        // 2. No social_accounts record yet — check if the email exists in users
        $email = $socialUser->getEmail();
        $user  = $email ? User::where('email', $email)->first() : null;

        if (! $user) {
            // 3. Brand-new user — create one
            // Twitter may not provide an email; generate a placeholder if needed
            $user = User::create([
                'username'  => $this->generateUniqueUsername($socialUser->getName()),
                'email'     => $email ?? $provider . '_' . $socialUser->getId() . '@placeholder.local',
                'email_verified_at' => now(),
                'password_hash' => '', // Social-only accounts have no local password
                'status'    => 'active',
            ]);

            \Spatie\Permission\Models\Role::findOrCreate('user', 'web');
            $user->assignRole('user');
        }

        // 4. Link this provider to the user
        SocialAccount::create([
            'user_id'          => $user->user_id,
            'provider'         => ($provider === 'twitter-oauth-2') ? 'twitter' : ($provider === 'google' ? 'google' : 'fb')   ,
            'provider_user_id' => $socialUser->getId(),
            'linked_at'        => now(),
        ]);

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Generate a unique username from the provider's display name.
     * Strips non-alphanumeric characters and appends a random suffix if taken.
     */
    private function generateUniqueUsername(string $name): string
    {
        // Transliterate to ASCII, lowercase, strip special chars, truncate to 90 chars
        $base = substr(preg_replace('/[^a-z0-9_]/i', '', str_replace(' ', '_', $name)), 0, 90);
        $base = $base ?: 'user';

        $username = $base;
        $i = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . '_' . $i++;
        }

        return $username;
    }
}
