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

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['admin', 'user'])],
            'status'   => ['required', Rule::in(['active', 'inactive', 'banned'])],
        ]);

        User::create([
            'username'      => $validated['username'],
            'email'         => $validated['email'],
            'password_hash' => bcrypt($validated['password']),
            'role'          => $validated['role'],
            'status'        => $validated['status'],
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Đã tạo tài khoản người dùng mới thành công.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        // Không ai có thể hạ cấp hoặc khóa tài khoản Super Admin tối cao
        if ($user->isSuperAdmin() && ($request->input('role') !== 'admin' || $request->input('status') !== 'active')) {
            return back()->with('error', 'Không thể hạ cấp hoặc khóa tài khoản Super Admin tối cao.');
        }

        // Chỉ Super Admin mới có quyền chỉnh sửa vai trò hoặc trạng thái của tài khoản Admin khác
        if ($user->role === 'admin' && $user->user_id !== $currentUser->user_id && ! $currentUser->isSuperAdmin()) {
            return back()->with('error', 'Chỉ có Super Admin mới có quyền chỉnh sửa tài khoản Quản trị viên khác.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'user'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
        ]);

        $user->update($validated);

        return back()->with('status', 'Đã cập nhật thông tin tài khoản thành công.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Không thể xóa tài khoản Super Admin tối cao.');
        }

        if ($user->role === 'admin' && ! $currentUser->isSuperAdmin()) {
            return back()->with('error', 'Chỉ có Super Admin mới có quyền xóa tài khoản Quản trị viên.');
        }

        if ($user->bookings()->exists()) {
            return back()->with('error', 'Không thể xoá người dùng đã có lịch đặt tour.');
        }

        $user->delete();

        return back()->with('status', 'Đã xoá người dùng thành công.');
    }
}
