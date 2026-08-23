<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Trang sửa một người dùng. `isEditingSelf` để view chặn admin tự hạ quyền
     * hoặc tự khoá tài khoản của chính mình.
     */
    public function edit(Request $request, User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'isEditingSelf' => $user->user_id === $request->user()->user_id,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'user'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
        ]);

        $user->update($validated);

        return back()->with('status', 'Đã cập nhật người dùng.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->bookings()->exists()) {
            return back()->with('error', 'Không thể xoá người dùng đã có lịch đặt tour.');
        }

        $user->delete();

        return back()->with('status', 'Đã xoá người dùng.');
    }
}
