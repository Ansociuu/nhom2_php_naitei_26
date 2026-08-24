<x-admin-layout title="Sửa thông tin đơn #BK-{{ $booking->booking_id }}">
    <div class="mb-5 flex justify-end gap-3">
        <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md font-semibold text-xs uppercase tracking-widest transition">
                &larr; Quay lại
            </a>
    </div>

    <div>

            <!-- Booking Summary Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                <h3 class="text-base font-semibold mb-4 border-b pb-2">Thông tin chung</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 block">Khách hàng:</span>
                        <span class="font-medium">{{ $booking->user->username ?? 'N/A' }} ({{ $booking->user->email ?? 'N/A' }})</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Tour:</span>
                        <span class="font-medium">{{ $booking->schedule->tour->title ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Ngày khởi hành:</span>
                        <span class="font-medium">{{ $booking->schedule && $booking->schedule->departure_date ? \Carbon\Carbon::parse($booking->schedule->departure_date)->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Edit Passenger Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h3 class="text-base font-semibold mb-4 border-b pb-2 text-gray-900 flex justify-between items-center">
                        <span>Danh sách hành khách ({{ $booking->details->count() }} người)</span>
                        <span class="text-xs font-normal text-gray-500">* Tuổi ≥ 12 tính vé Người lớn, &lt; 12 tính vé Trẻ em</span>
                    </h3>

                    <div class="space-y-4">
                        @foreach($booking->details as $index => $detail)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <input type="hidden" name="passengers[{{ $index }}][booking_detail_id]" value="{{ $detail->booking_detail_id }}">

                                <div class="flex items-center gap-2 mb-3">
                                    <span class="font-bold text-sm text-[#2D5A3D]">#{{ $index + 1 }}</span>
                                    <span class="text-xs text-gray-500">Hành khách {{ $index + 1 }}</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Họ và tên <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="passengers[{{ $index }}][name]" 
                                               value="{{ old("passengers.{$index}.name", $detail->name) }}" 
                                               required
                                               class="w-full border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] rounded-md shadow-sm text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Tuổi <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" 
                                               name="passengers[{{ $index }}][age]" 
                                               value="{{ old("passengers.{$index}.age", $detail->age) }}" 
                                               min="0" 
                                               max="120" 
                                               required
                                               class="w-full border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] rounded-md shadow-sm text-sm">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#2D6A2D] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#245524] active:bg-[#1c421c] focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2 transition">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-admin-layout>
