<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('bookings');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('user_id', 'desc')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive,banned',
        ]);

        if ($user->user_id === $request->user()->user_id && $validated['role'] !== $user->role) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'Bạn không thể thay đổi vai trò của chính mình.');
        }

        $user->update($validated);
        $user->syncRoles($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->user_id === $request->user()->user_id) {
            return redirect()->back()
                ->with('error', 'Không thể xóa tài khoản của chính mình.');
        }

        if ($user->bookings()->exists()) {
            return redirect()->back()
                ->with('error', 'Không thể xóa người dùng này vì đã có lịch sử đặt tour. Bạn có thể đổi trạng thái sang "Banned" để vô hiệu hóa tài khoản.');
        }

        DB::transaction(function () use ($user) {
            $user->socialAccounts()->delete();
            $user->bankAccounts()->delete();
            $user->comments()->delete();
            $user->reviews()->delete();
            $user->reviewLikes()->detach();
            $user->syncRoles([]);
            $user->delete();
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa người dùng thành công.');
    }
}
