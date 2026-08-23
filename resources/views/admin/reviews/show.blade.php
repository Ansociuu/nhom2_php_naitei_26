<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reviews.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm transition">
                    &larr; Quay lại
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Chi tiết Đánh giá') }} #{{ $review->review_id }}
                </h2>
            </div>
            
            <div>
                @if ($review->status === 'pending')
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300">
                        ⏳ Chờ duyệt
                    </span>
                @elseif ($review->status === 'approved')
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                        ✓ Đã duyệt
                    </span>
                @elseif ($review->status === 'rejected')
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                        ✕ Đã từ chối
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/50 dark:border-green-600 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-900/50 dark:border-red-600 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Main Column (Content & Images) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Review Content Box -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <span class="text-2xl text-yellow-400">
                                    {!! str_repeat('&#9733;', $review->score) !!}{!! str_repeat('&#9734;', 5 - $review->score) !!}
                                </span>
                                <span class="ms-2 text-sm font-semibold text-gray-700 dark:text-gray-300">({{ $review->score }}/5 sao)</span>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <span class="text-red-500 font-bold">&hearts;</span>
                                <span>{{ $review->likes_count ?? 0 }} lượt thích</span>
                            </div>
                        </div>

                        <div class="prose dark:prose-invert max-w-none">
                            <h4 class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-2">Nội dung đánh giá</h4>
                            <p class="text-gray-800 dark:text-gray-200 text-base leading-relaxed whitespace-pre-line">
                                {{ $review->content }}
                            </p>
                        </div>
                    </div>

                    <!-- Attached Images Gallery -->
                    @if($review->images && $review->images->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-semibold text-base text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <span>📷 Ảnh đính kèm</span>
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $review->images->count() }} ảnh)</span>
                            </h3>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($review->images as $img)
                                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 aspect-square">
                                        <img src="{{ $img->secure_url }}" 
                                             alt="Ảnh đánh giá" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-xs text-gray-400 p-2 text-center\'>Ảnh không khả dụng</div>';">
                                        <a href="{{ $img->secure_url }}" 
                                           target="_blank" 
                                           class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-medium transition">
                                            Xem ảnh lớn &nearr;
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Comments Thread (if any) -->
                    @if($review->comments && $review->comments->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-semibold text-base text-gray-900 dark:text-white mb-4">
                                💬 Bình luận ({{ $review->comments->count() }})
                            </h3>
                            <div class="space-y-4">
                                @foreach($review->comments as $comment)
                                    <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg text-sm">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->username ?? 'Khách' }}</span>
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>

                                        <!-- Replies -->
                                        @if($comment->replies && $comment->replies->isNotEmpty())
                                            <div class="mt-2 ms-4 pl-3 border-l-2 border-indigo-200 dark:border-indigo-800 space-y-2">
                                                @foreach($comment->replies as $reply)
                                                    <div class="text-xs">
                                                        <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $reply->user->username ?? 'Khách' }}:</span>
                                                        <span class="text-gray-600 dark:text-gray-300">{{ $reply->content }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Sidebar (Meta & Moderation Actions) -->
                <div class="space-y-6">
                    
                    <!-- Moderation Actions Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-2 border-indigo-100 dark:border-indigo-900/50">
                        <h3 class="font-semibold text-base text-gray-900 dark:text-white mb-4">
                            ⚙️ Kiểm duyệt đánh giá
                        </h3>
                        
                        <div class="space-y-3">
                            @if ($review->status === 'pending' || $review->status === 'rejected')
                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-sm transition flex items-center justify-center gap-2 shadow-sm">
                                        <span>✓</span> Duyệt đánh giá
                                    </button>
                                </form>
                            @endif

                            @if ($review->status === 'pending' || $review->status === 'approved')
                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg text-sm transition flex items-center justify-center gap-2 shadow-sm">
                                        <span>✕</span> Từ chối đánh giá
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition flex items-center justify-center gap-2 shadow-sm">
                                    <span>🗑️</span> Xóa đánh giá
                                </button>
                            </form>
                        </div>

                        @if($review->approved_at)
                            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
                                <span>Thời điểm duyệt:</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $review->approved_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Reviewer Info Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-semibold text-base text-gray-900 dark:text-white mb-4">
                            👤 Người đánh giá
                        </h3>
                        
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Tên người dùng</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $review->user->username ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Email</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $review->user->email ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Ngày gửi đánh giá</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $review->created_at->format('d/m/Y H:i:s') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tour Info Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-semibold text-base text-gray-900 dark:text-white mb-4">
                            🗺️ Thông tin Tour
                        </h3>
                        
                        @if($review->tour)
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Tên Tour</span>
                                    <a href="{{ route('admin.tours.show', $review->tour) }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $review->tour->title }} &rarr;
                                    </a>
                                </div>
                                @if($review->tour->category)
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block">Danh mục</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                            {{ $review->tour->category->name }}
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Điểm khởi hành</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $review->tour->departure_location ?? 'N/A' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Giá Tour</span>
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($review->tour->price, 0, ',', '.') }} đ
                                    </span>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Tour không còn tồn tại trong hệ thống.</p>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
