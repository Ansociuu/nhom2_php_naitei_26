<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * Get redirect URL for OAuth provider.
     */
    public function redirect(string $provider): JsonResponse
    {
        if (! in_array($provider, ['facebook', 'twitter', 'google'], true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nhà cung cấp đăng nhập mạng xã hội không hỗ trợ.',
            ], 422);
        }

        $driver = ($provider === 'twitter') ? 'twitter-oauth-2' : $provider;
        $targetUrl = Socialite::driver($driver)->stateless()->redirect()->getTargetUrl();

        return response()->json([
            'status'       => 'success',
            'redirect_url' => $targetUrl,
        ]);
    }

    /**
     * Handle OAuth callback from Client / SPA.
     */
    public function callback(Request $request, string $provider): JsonResponse
    {
        if (! in_array($provider, ['facebook', 'twitter', 'google'], true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nhà cung cấp đăng nhập mạng xã hội không hỗ trợ.',
            ], 422);
        }

        $driver = ($provider === 'twitter') ? 'twitter-oauth-2' : $provider;

        try {
            if ($request->has('access_token')) {
                $socialUser = Socialite::driver($driver)->stateless()->userFromToken($request->input('access_token'));
            } else {
                $socialUser = Socialite::driver($driver)->stateless()->user();
            }
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Xác thực tài khoản mạng xã hội thất bại hoặc đã hủy.',
            ], 400);
        }

        $dbProvider = ($provider === 'twitter') ? 'twitter' : ($provider === 'google' ? 'google' : 'fb');

        $socialAccount = SocialAccount::where('provider', $dbProvider)
            ->where('provider_user_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;
        } else {
            $email = $socialUser->getEmail();
            $user = $email ? User::where('email', $email)->first() : null;

            if (! $user) {
                $user = User::create([
                    'username'          => $this->generateUniqueUsername($socialUser->getName() ?: 'user_' . $socialUser->getId()),
                    'email'             => $email ?? $dbProvider . '_' . $socialUser->getId() . '@placeholder.local',
                    'email_verified_at' => now(),
                    'password_hash'     => '',
                    'role'              => 'user',
                    'status'            => 'active',
                ]);

                \Spatie\Permission\Models\Role::findOrCreate('user', 'web');
                $user->assignRole('user');
            }

            SocialAccount::create([
                'user_id'          => $user->user_id,
                'provider'         => $dbProvider,
                'provider_user_id' => $socialUser->getId(),
                'linked_at'        => now(),
            ]);
        }

        if ($user->status === 'banned') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản của bạn đã bị khóa.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'message'      => 'Đăng nhập thành công.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'user_id'  => $user->user_id,
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => $user->role,
            ],
        ]);
    }

    /**
     * Generate a unique username from display name.
     */
    private function generateUniqueUsername(string $name): string
    {
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
