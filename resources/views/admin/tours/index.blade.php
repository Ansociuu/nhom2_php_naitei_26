<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Quản lý Tour Du lịch') }}
            </h2>
            <a href="{{ route('admin.tours.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                + Thêm Tour mới
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/50 dark:border-green-600 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-900/50 dark:border-red-600 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filters -->
                <form method="GET" action="{{ route('admin.tours.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên tour hoặc điểm khởi hành..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <div>
                        <select name="category_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-sm transition">
                            Lọc
                        </button>
                        @if(request('search') || request('category_id') || request('status'))
                            <a href="{{ route('admin.tours.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm transition flex items-center">
                                Xóa
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Tên Tour</th>
                                <th scope="col" class="px-6 py-3">Danh mục</th>
                                <th scope="col" class="px-6 py-3">Điểm xuất phát</th>
                                <th scope="col" class="px-6 py-3">Thời gian</th>
                                <th scope="col" class="px-6 py-3">Giá (VNĐ)</th>
                                <th scope="col" class="px-6 py-3">Trạng thái</th>
                                <th scope="col" class="px-6 py-3 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tours as $tour)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 font-mono text-xs">{{ $tour->tour_id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white max-w-xs truncate">
                                        <a href="{{ route('admin.tours.show', $tour) }}" class="hover:underline text-indigo-600 dark:text-indigo-400">
                                            {{ $tour->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                            {{ $tour->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $tour->departure_location ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 font-medium">{{ $tour->duration_days }} ngày</td>
                                    <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($tour->price, 0, ',', '.') }} đ
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tour->status === 'active')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Hoạt động</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Tạm ẩn</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.tours.show', $tour) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 font-medium text-xs">Chi tiết</a>
                                        <a href="{{ route('admin.tours.edit', $tour) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium text-xs">Sửa</a>
                                        <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Tour này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 font-medium text-xs">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Chưa có Tour nào được tìm thấy.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $tours->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
