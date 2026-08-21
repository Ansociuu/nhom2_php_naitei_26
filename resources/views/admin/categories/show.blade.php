<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Chi tiết Danh mục') }}: {{ $category->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.categories.edit', $category) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                    Sửa danh mục
                </a>
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg text-sm transition">
                    ← Quay lại danh sách
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Thông tin tổng quan</h3>
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">ID Danh mục</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->category_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Tên danh mục</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Danh mục cha</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $category->parent ? $category->parent->name : '-- Danh mục gốc --' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Children Categories Card -->
            @if($category->children->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Danh mục con thuộc {{ $category->name }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($category->children as $child)
                            <a href="{{ route('admin.categories.show', $child) }}" class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg text-sm hover:bg-blue-100 transition">
                                📁 {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tours in Category Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Các Tour du lịch thuộc danh mục này</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Tên Tour</th>
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
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('admin.tours.show', $tour) }}" class="hover:underline text-indigo-600 dark:text-indigo-400">
                                            {{ $tour->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">{{ $tour->duration_days }} ngày</td>
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
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.tours.show', $tour) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 font-medium text-xs">Chi tiết</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        Chưa có Tour nào thuộc danh mục này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $tours->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
