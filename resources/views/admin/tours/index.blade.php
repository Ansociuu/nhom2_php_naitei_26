<x-admin-layout title="Quản lý Tour Du lịch">
    <div class="mb-5 flex justify-end gap-3">
        <a href="{{ route('admin.tours.create') }}" class="px-4 py-2 bg-[#2D6A2D] hover:bg-[#245524] text-white font-medium rounded-lg text-sm transition">
                + Thêm Tour mới
            </a>
    </div>

    <div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Filters -->
                <form method="GET" action="{{ route('admin.tours.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên tour hoặc điểm khởi hành..." class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                    </div>

                    <div>
                        <select name="category_id" class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
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
                            <a href="{{ route('admin.tours.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition flex items-center">
                                Xóa
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
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
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-mono text-xs">{{ $tour->tour_id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 max-w-xs truncate">
                                        <a href="{{ route('admin.tours.show', $tour) }}" class="hover:underline text-[#2D5A3D]">
                                            {{ $tour->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $tour->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $tour->departure_location ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 font-medium">{{ $tour->duration_days }} ngày</td>
                                    <td class="px-6 py-4 font-semibold text-emerald-600">
                                        {{ number_format($tour->price, 0, ',', '.') }} đ
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tour->status === 'active')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hoạt động</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tạm ẩn</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.tours.show', $tour) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">Chi tiết</a>
                                        <a href="{{ route('admin.tours.edit', $tour) }}" class="text-[#2D5A3D] hover:text-[#2D5A3D] font-medium text-xs">Sửa</a>
                                        <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Tour này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
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
</x-admin-layout>
