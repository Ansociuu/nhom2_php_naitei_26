<x-admin-layout title="Quản lý người dùng">
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Tìm theo tên đăng nhập, email..."
                    class="flex-1 min-w-[220px] rounded-md border-gray-300 shadow-sm text-sm"
                >

                <select name="role" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="user" @selected(request('role') === 'user')>User</option>
                </select>

                <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Ngừng hoạt động</option>
                    <option value="banned" @selected(request('status') === 'banned')>Đã khoá</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm rounded-md hover:bg-gray-700">
                    Lọc
                </button>

                @if (request()->anyFilled(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800">
                        Xoá lọc
                    </a>
                @endif
            </form>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Mã</th>
                    <th class="px-4 py-3">Tên đăng nhập</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Vai trò &amp; trạng thái</th>
                    <th class="px-4 py-3">Đăng nhập cuối</th>
                    <th class="px-4 py-3 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 text-gray-500">#{{ $user->user_id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $user->username }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>

                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')

                                <select name="role" class="rounded-md border-gray-300 shadow-sm text-xs">
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    <option value="user" @selected($user->role === 'user')>User</option>
                                </select>

                                <select name="status" class="rounded-md border-gray-300 shadow-sm text-xs">
                                    <option value="active" @selected($user->status === 'active')>Hoạt động</option>
                                    <option value="inactive" @selected($user->status === 'inactive')>Ngừng hoạt động</option>
                                    <option value="banned" @selected($user->status === 'banned')>Đã khoá</option>
                                </select>

                                <button type="submit" class="text-xs text-blue-600 hover:underline">Lưu</button>
                            </form>
                        </td>

                        <td class="px-4 py-3 text-gray-500">
                            {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Chưa đăng nhập' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Xoá tài khoản này? Hành động không thể hoàn tác.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline">Xoá</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            Chưa có người dùng nào đăng ký
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $users->links() }}
        </div>
    </div>
</x-admin-layout>
