<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\UpdateBankAccountRequest;
use App\Http\Resources\BankAccountResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user account via API.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'username'      => $validated['username'],
            'email'         => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'role'          => 'user',
            'status'        => 'active',
        ]);

        $user->assignRole('user');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'message'      => 'Đăng ký tài khoản thành công.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => new UserResource($user),
        ], 201);
    }

    /**
     * Authenticate user & return API token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['login'])
            ->orWhere('username', $validated['login'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'login' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản của bạn đang bị tạm khóa hoặc ngừng hoạt động.',
            ], 403);
        }

        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'message'      => 'Đăng nhập thành công.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => new UserResource($user),
        ]);
    }

    /**
     * Get the authenticated User profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user'   => new UserResource($request->user()),
        ]);
    }

    /**
     * Update the authenticated User profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->user_id, 'user_id')],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
        ]);

        $user->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật thông tin cá nhân thành công.',
            'user'    => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Update the authenticated User password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không chính xác.'],
            ]);
        }

        $user->update([
            'password_hash' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đổi mật khẩu thành công.',
        ]);
    }

    /**
     * Get the authenticated user's bank account.
     */
    public function getBankAccount(Request $request): JsonResponse
    {
        $bankAccount = $request->user()->bankAccounts()->first();

        return response()->json([
            'status'       => 'success',
            'bank_account' => $bankAccount ? new BankAccountResource($bankAccount) : null,
        ]);
    }

    /**
     * Update or create the authenticated user's bank account.
     */
    public function updateBankAccount(UpdateBankAccountRequest $request): JsonResponse
    {
        $user = $request->user();

        $bankAccount = $user->bankAccounts()->updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'bank_name'           => $request->validated('bank_name'),
                'account_number'      => $request->validated('account_number'),
                'account_holder_name' => mb_strtoupper($request->validated('account_holder_name')),
            ]
        );

        return response()->json([
            'status'       => 'success',
            'message'      => 'Cập nhật tài khoản ngân hàng thành công.',
            'bank_account' => new BankAccountResource($bankAccount),
        ]);
    }

    /**
     * Revoke the current authenticated API token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Đăng xuất thành công.',
        ]);
    }
}
