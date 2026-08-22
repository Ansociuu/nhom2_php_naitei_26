<section>
    <header>
        <h2 class="card-title">
            Thông tin tài khoản
        </h2>
    </header>

    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="text-gray-400 text-sm">Tên đăng nhập</span>
            <p class="mt-1 font-medium text-gray-900">{{ $user->username }}</p>
        </div>

        <div>
            <span class="text-gray-400 text-sm">Email</span>
            <p class="mt-1 font-medium text-gray-900">{{ $user->email }}</p>
        </div>

        <div>
            <span class="text-gray-400 text-sm">Vai trò</span>
            <p class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $user->role === 'admin' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-[#2D5A3D]' }}">
                    {{ $user->role === 'admin' ? 'Admin' : 'User' }}
                </span>
            </p>
        </div>

        <div>
            <span class="text-gray-400 text-sm">Trạng thái</span>
            <p class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $user->status === 'active' ? 'bg-emerald-50 text-[#2D5A3D]' : 'bg-amber-50 text-amber-700' }}">
                    {{ match ($user->status) {
                        'active' => 'Hoạt động',
                        'inactive' => 'Ngừng hoạt động',
                        'banned' => 'Đã khoá',
                        default => $user->status,
                    } }}
                </span>
            </p>
        </div>

        <div>
            <span class="text-gray-400 text-sm">Tham gia từ</span>
            <p class="mt-1 font-medium text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
        </div>

        <div>
            <span class="text-gray-400 text-sm">Đăng nhập gần nhất</span>
            <p class="mt-1 font-medium text-gray-900">{{ $user->last_login_at?->diffForHumans() ?? 'Chưa có' }}</p>
        </div>
    </div>
</section>
