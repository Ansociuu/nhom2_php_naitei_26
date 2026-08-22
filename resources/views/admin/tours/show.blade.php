<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Chi tiết Tour') }}: {{ $tour->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.tours.edit', $tour) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                    Sửa Tour
                </a>
                <a href="{{ route('admin.tours.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg text-sm transition">
                    ← Quay lại danh sách
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/50 dark:border-green-600 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Main Info Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 mb-2">
                            {{ $tour->category->name }}
                        </span>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tour->title }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">📍 Xuất phát: {{ $tour->departure_location ?? 'Chưa cập nhật' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                            {{ number_format($tour->price, 0, ',', '.') }} đ
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Thời gian: <strong>{{ $tour->duration_days }} ngày</strong></p>
                        <div class="mt-2">
                            @if($tour->status === 'active')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Đang hoạt động</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Tạm ẩn</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Mô tả Tour</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $tour->description ?? 'Chưa có mô tả.' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Điểm nổi bật (Highlights)</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $tour->highlights ?? 'Chưa có thông tin.' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 mb-2">✓ Dịch vụ BAO GỒM</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $tour->included_services ?? 'Chưa có thông tin.' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-2">✕ Dịch vụ KHÔNG BAO GỒM</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $tour->excluded_services ?? 'Chưa có thông tin.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tour Image Gallery Section -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Thư viện ảnh Tour ({{ $tour->images->count() }})</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Quản lý hình ảnh hiển thị và chọn ảnh đại diện (Cover) cho Tour.</p>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('admin.tours.images.store', $tour) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                        @csrf
                        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp" required class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-xs whitespace-nowrap transition">
                            + Tải ảnh lên
                        </button>
                    </form>
                </div>

                @error('images')
                    <div class="p-3 mb-4 text-xs bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ $message }}
                    </div>
                @enderror
                @error('images.*')
                    <div class="p-3 mb-4 text-xs bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ $message }}
                    </div>
                @enderror

                @if($tour->images->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($tour->images->sortBy('display_order') as $img)
                            <div class="relative group border rounded-xl overflow-hidden shadow-sm dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-col justify-between">
                                <div class="relative aspect-video w-full overflow-hidden bg-gray-200 dark:bg-gray-800">
                                    <img src="{{ $img->secure_url }}" alt="Tour Image" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @if($img->is_cover)
                                        <span class="absolute top-2 left-2 px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase bg-amber-500 text-white rounded-full shadow">
                                            ★ Cover
                                        </span>
                                    @endif
                                </div>
                                <div class="p-2 flex flex-col gap-1 text-[11px] text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800">
                                    <div class="flex justify-between items-center font-mono">
                                        <span>{{ strtoupper($img->format) }}</span>
                                        <span>{{ number_format($img->bytes / 1024, 1) }} KB</span>
                                    </div>
                                    <div class="flex gap-1 mt-1">
                                        @if(!$img->is_cover)
                                            <form action="{{ route('admin.tours.images.cover', [$tour, $img]) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full py-1 text-[11px] bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/60 dark:text-indigo-300 font-medium rounded transition">
                                                    Đặt Cover
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.tours.images.destroy', [$tour, $img]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ảnh này?');" class="{{ $img->is_cover ? 'w-full' : '' }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-[11px] bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/30 dark:hover:bg-red-900/60 dark:text-red-400 font-medium rounded transition">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400 italic">
                        Chưa có hình ảnh nào cho tour này. Hãy tải lên ảnh để làm nổi bật Tour.
                    </div>
                @endif
            </div>

            <!-- Schedules Section -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Lịch khởi hành ({{ $tour->schedules->count() }})</h3>
                @if($tour->schedules->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-4 py-2">ID</th>
                                    <th scope="col" class="px-4 py-2">Ngày khởi hành</th>
                                    <th scope="col" class="px-4 py-2">Số chỗ còn</th>
                                    <th scope="col" class="px-4 py-2">Giá khởi hành</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tour->schedules as $sch)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-2 font-mono text-xs">{{ $sch->schedule_id }}</td>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($sch->departure_date)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 font-bold">{{ $sch->available_slots }}</td>
                                        <td class="px-4 py-2 font-semibold text-emerald-600 dark:text-emerald-400">
                                            {{ number_format($sch->price_override ?? $tour->price, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Chưa có lịch khởi hành nào được thiết lập.</p>
                @endif
            </div>

            <!-- Itineraries Section -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Lịch trình chi tiết ({{ $tour->itineraries->count() }} ngày)</h3>
                @if($tour->itineraries->count() > 0)
                    <div class="space-y-4">
                        @foreach($tour->itineraries->sortBy('day_number') as $itin)
                            <div class="border-l-4 border-indigo-500 pl-4 py-2 bg-gray-50 dark:bg-gray-900/50 rounded-r-lg">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    Ngày {{ $itin->day_number }}: {{ $itin->title }}
                                </h4>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 whitespace-pre-line">{{ $itin->description }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Chưa có lịch trình chi tiết.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

