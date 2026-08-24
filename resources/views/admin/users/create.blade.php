<x-admin-layout title="Thêm người dùng mới">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between pb-4 mb-6 border-b">
            <h2 class="text-lg font-bold text-gray-900">Tạo tài khoản Người dùng / Admin mới</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-800">
                &larr; Quay lại danh sách
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập (Username)</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    value="{{ old('username') }}"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Nhập tên đăng nhập..."
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="email@example.com"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu (Tối thiểu 8 ký tự)</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="••••••••"
                >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Vai trò hệ thống (Role)</label>
                    <select name="role" id="role" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="user" @selected(old('role') === 'user')>Khách hàng (User)</option>
                        <option value="admin" @selected(old('role') === 'admin')>Quản trị viên (Admin)</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái tài khoản</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="active" @selected(old('status') === 'active')>Hoạt động (Active)</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Tạm ngưng (Inactive)</option>
                        <option value="banned" @selected(old('status') === 'banned')>Khóa tài khoản (Banned)</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
                    Tạo tài khoản
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
