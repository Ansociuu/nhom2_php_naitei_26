<x-admin-layout title="Thêm Danh mục Mới">
    <div class="mb-5 flex justify-end gap-3">
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg text-sm transition">
                ← Quay lại danh sách
            </a>
    </div>

    <div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf

                    <!-- Tên danh mục -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên danh mục <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ví dụ: Du lịch Miền Bắc, Tour Biển Đảo..." class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Danh mục cha -->
                    <div class="mb-6">
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Danh mục cha
                        </label>
                        <select name="parent_id" id="parent_id" class="w-full rounded-lg border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] text-sm">
                            <option value="">-- Không có (Danh mục gốc) --</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->category_id }}" {{ old('parent_id') == $parent->category_id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg text-sm transition">
                            Hủy
                        </a>
                        <button type="submit" class="px-5 py-2 bg-[#2D6A2D] hover:bg-[#245524] text-white font-medium rounded-lg text-sm transition">
                            Lưu danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
