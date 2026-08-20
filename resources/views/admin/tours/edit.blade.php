<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Chỉnh sửa Tour') }}: {{ $tour->title }}
            </h2>
            <a href="{{ route('admin.tours.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg text-sm transition">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.tours.update', $tour) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Tên Tour & Danh mục -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tên Tour <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $tour->title) }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('title')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Danh mục Tour <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}" {{ old('category_id', $tour->category_id) == $cat->category_id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Giá, Số ngày & Trạng thái -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Giá Tour (VNĐ) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price" id="price" value="{{ old('price', $tour->price) }}" step="1000" min="0" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('price')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Số ngày <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $tour->duration_days) }}" min="1" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('duration_days')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Trạng thái <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="active" {{ old('status', $tour->status) === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status', $tour->status) === 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Điểm khởi hành -->
                    <div>
                        <label for="departure_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Điểm xuất phát
                        </label>
                        <input type="text" name="departure_location" id="departure_location" value="{{ old('departure_location', $tour->departure_location) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('departure_location')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mô tả & Điểm nổi bật -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mô tả Tour</label>
                        <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('description', $tour->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="highlights" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Điểm nổi bật (Highlights)</label>
                        <textarea name="highlights" id="highlights" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('highlights', $tour->highlights) }}</textarea>
                        @error('highlights')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dịch vụ bao gồm & không bao gồm -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="included_services" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dịch vụ BAO GỒM</label>
                            <textarea name="included_services" id="included_services" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('included_services', $tour->included_services) }}</textarea>
                        </div>
                        <div>
                            <label for="excluded_services" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dịch vụ KHÔNG BAO GỒM</label>
                            <textarea name="excluded_services" id="excluded_services" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('excluded_services', $tour->excluded_services) }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                        <a href="{{ route('admin.tours.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                            Hủy
                        </a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                            Cập nhật Tour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
