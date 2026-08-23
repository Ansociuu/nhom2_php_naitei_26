<x-site-layout title="Viết đánh giá">
    <div class="max-w-3xl mx-auto px-6 sm:px-8 lg:px-12 py-8">
        <a href="{{ route('reviews.index') }}" class="text-base text-gray-500 hover:text-[#2D5A3D]">← Quay lại</a>

        <div class="mt-4 card-surface overflow-hidden">
            <div class="flex items-center gap-4 p-5 bg-gray-50 border-b">
                <div class="w-20 h-16 rounded-lg overflow-hidden bg-gray-200 shrink-0">
                    @if ($tour->coverImageUrl())
                        <img src="{{ $tour->coverImageUrl() }}" alt="{{ $tour->title }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="min-w-0">
                    <h1 class="card-title uppercase truncate">TRAIL BUS – {{ $tour->title }}</h1>
                    <p class="muted-text">
                        Khởi hành {{ $booking->schedule->departure_date->format('d/m/Y') }}
                        &middot; Vé "{{ $booking->ticketType?->name }}"
                    </p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mx-5 mt-5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('reviews.store', $booking) }}" enctype="multipart/form-data"
                  class="p-5 sm:p-7 space-y-7"
                  x-data="{ score: {{ old('score', $review->score ?? 0) }}, hover: 0, files: [] }">
                @csrf

                <div>
                    <label class="block text-lg font-bold text-gray-900">Bạn thấy chuyến đi thế nào?</label>
                    <input type="hidden" name="score" :value="score">

                    <div class="mt-3 flex items-center gap-2">
                        <template x-for="i in 5" :key="i">
                            <button type="button" @click="score = i" @mouseenter="hover = i" @mouseleave="hover = 0"
                                    class="text-amber-400 transition-transform hover:scale-110">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="w-10 h-10"
                                     :fill="i <= (hover || score) ? 'currentColor' : 'none'"
                                     :class="i <= (hover || score) ? '' : 'text-gray-300'">
                                    <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </template>

                        <span class="ml-2 text-sm font-medium text-gray-500" x-text="[
                            '', 'Rất tệ', 'Không hài lòng', 'Bình thường', 'Hài lòng', 'Tuyệt vời'
                        ][hover || score] || 'Chọn số sao'"></span>
                    </div>
                </div>

                <div>
                    <label for="content" class="block text-lg font-bold text-gray-900">Chia sẻ trải nghiệm của bạn</label>
                    <p class="mt-1 muted-text">Cung đường thế nào? Leader hỗ trợ ra sao? Có lời khuyên gì cho người đi sau?</p>
                    <textarea id="content" name="content" rows="6" required minlength="10"
                              placeholder="Viết cảm nhận của bạn về chuyến đi..."
                              class="mt-3 form-control">{{ old('content', $review->content ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-lg font-bold text-gray-900">Thêm ảnh (tối đa 5 ảnh)</label>
                    <p class="mt-1 muted-text">Định dạng JPG, PNG hoặc WEBP, mỗi ảnh tối đa 4MB.</p>

                    <label class="mt-3 flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 rounded-xl py-8 cursor-pointer hover:border-[#2D5A3D] hover:bg-emerald-50/40 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-8 h-8 text-gray-400">
                            <rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m4 18 5-5 4 4 3-3 4 4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="text-sm text-gray-600">Bấm để chọn ảnh từ máy</span>
                        <input type="file" name="images[]" accept="image/*" multiple class="hidden"
                               @change="files = Array.from($event.target.files).map(f => ({ name: f.name, url: URL.createObjectURL(f) }))">
                    </label>

                    <div x-show="files.length" x-cloak class="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-3">
                        <template x-for="file in files" :key="file.name">
                            <img :src="file.url" :alt="file.name" class="w-full aspect-square rounded-lg object-cover border">
                        </template>
                    </div>

                    @if ($review && $review->images->isNotEmpty())
                        <div class="mt-4">
                            <p class="muted-text">Ảnh đã tải lên trước đó:</p>
                            <div class="mt-2 grid grid-cols-3 sm:grid-cols-5 gap-3">
                                @foreach ($review->images as $image)
                                    <img src="{{ $image->url() }}" alt="" class="w-full aspect-square rounded-lg object-cover border">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" :disabled="!score"
                            :class="score ? 'bg-[#2D6A2D] hover:bg-[#245524]' : 'bg-gray-300 cursor-not-allowed'"
                            class="btn px-7 py-3 text-white">
                        {{ $review ? 'Cập nhật đánh giá' : 'Gửi đánh giá' }}
                    </button>
                    <a href="{{ route('reviews.index') }}" class="text-gray-500 hover:text-gray-800">Huỷ</a>
                </div>
            </form>
        </div>
    </div>
</x-site-layout>
