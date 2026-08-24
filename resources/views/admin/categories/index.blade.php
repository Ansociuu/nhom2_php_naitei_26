<x-admin-layout title="Quản lý Danh mục Tour">
    <div class="mb-5 flex justify-end gap-3">
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-[#2D6A2D] hover:bg-[#245524] text-white font-medium rounded-lg text-sm transition">
                + Thêm danh mục mới
            </a>
    </div>

    <div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Search Filter -->
                <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-6 flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tên danh mục..." class="w-full sm:w-80 rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-sm transition">
                        Tìm kiếm
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition flex items-center">
                            Xóa lọc
                        </a>
                    @endif
                </form>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Tên danh mục</th>
                                <th scope="col" class="px-6 py-3">Danh mục cha</th>
                                <th scope="col" class="px-6 py-3 text-center">Số danh mục con</th>
                                <th scope="col" class="px-6 py-3 text-center">Số lượng Tour</th>
                                <th scope="col" class="px-6 py-3 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-mono text-xs">{{ $category->category_id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <a href="{{ route('admin.categories.show', $category) }}" class="hover:underline text-[#2D5A3D]">
                                            {{ $category->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($category->parent)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $category->parent->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic">-- Danh mục gốc --</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 font-semibold">
                                            {{ $category->children_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-800 font-semibold">
                                            {{ $category->tours_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.categories.show', $category) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">Chi tiết</a>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-[#2D5A3D] hover:text-[#2D5A3D] font-medium text-xs">Sửa</a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Chưa có danh mục nào được tìm thấy.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
