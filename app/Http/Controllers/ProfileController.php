<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankAccountUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'user'           => $user,
            'bankAccount'    => $user->bankAccounts()->first(),
            'banks'          => config('banks.list'),
            'socialAccounts' => $user->socialAccounts()->get(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update or create the user's bank account.
     */
    public function updateBankAccount(BankAccountUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->bankAccounts()->updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'bank_name'           => $request->validated('bank_name'),
                'account_number'      => $request->validated('account_number'),
                'account_holder_name' => mb_strtoupper($request->validated('account_holder_name')),
            ]
        );

        return Redirect::route('profile.edit')->with('status', 'bank-account-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
