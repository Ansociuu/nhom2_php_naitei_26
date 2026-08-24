<x-admin-layout title="Thêm Tour Du lịch Mới">
    <div class="mb-5 flex justify-end gap-3">
        <a href="{{ route('admin.tours.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg text-sm transition">
                ← Quay lại danh sách
            </a>
    </div>

    <div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.tours.store') }}" class="space-y-6">
                    @csrf

                    <!-- Tên Tour & Danh mục -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Tên Tour <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Ví dụ: Tour Hà Nội - Sapa 3N2Đ..." class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                            @error('title')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Danh mục Tour <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" required class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}" {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Giá, Số ngày & Trạng thái -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Giá Tour (VNĐ) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" step="1000" min="0" required placeholder="Ví dụ: 3500000" class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                            @error('price')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">
                                Số ngày <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', 1) }}" min="1" required class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                            @error('duration_days')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Trạng thái <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Điểm khởi hành -->
                    <div>
                        <label for="departure_location" class="block text-sm font-medium text-gray-700 mb-2">
                            Điểm xuất phát
                        </label>
                        <input type="text" name="departure_location" id="departure_location" value="{{ old('departure_location') }}" placeholder="Ví dụ: Hà Nội, TP. Hồ Chí Minh..." class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                        @error('departure_location')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mô tả ngắn & Điểm nổi bật -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả Tour</label>
                        <textarea name="description" id="description" rows="4" placeholder="Mô tả chi tiết về Tour..." class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="highlights" class="block text-sm font-medium text-gray-700 mb-2">Điểm nổi bật (Highlights)</label>
                        <textarea name="highlights" id="highlights" rows="3" placeholder="Những trải nghiệm đặc sắc nhất..." class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">{{ old('highlights') }}</textarea>
                        @error('highlights')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dịch vụ bao gồm & không bao gồm -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="included_services" class="block text-sm font-medium text-gray-700 mb-2">Dịch vụ BAO GỒM</label>
                            <textarea name="included_services" id="included_services" rows="3" placeholder="Ví dụ: Xe đưa đón, Khách sạn 4 sao, Ăn sáng..." class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">{{ old('included_services') }}</textarea>
                        </div>
                        <div>
                            <label for="excluded_services" class="block text-sm font-medium text-gray-700 mb-2">Dịch vụ KHÔNG BAO GỒM</label>
                            <textarea name="excluded_services" id="excluded_services" rows="3" placeholder="Ví dụ: Vé máy bay, Chi phí cá nhân, Tiền tip..." class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">{{ old('excluded_services') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('admin.tours.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg text-sm transition">
                            Hủy
                        </a>
                        <button type="submit" class="px-5 py-2 bg-[#2D6A2D] hover:bg-[#245524] text-white font-medium rounded-lg text-sm transition">
                            Lưu Tour mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
