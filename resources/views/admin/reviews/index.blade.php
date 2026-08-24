<x-admin-layout title="Quản lý Đánh giá">
    <div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên tour hoặc người dùng..." class="w-full border-gray-300 focus:border-[#2D5A3D]:border-[#2D5A3D] focus:ring-[#2D5A3D]:ring-[#2D5A3D] rounded-md shadow-sm">
                            </div>
                            <div class="w-48">
                                <select name="status" class="w-full border-gray-300 focus:border-[#2D5A3D]:border-[#2D5A3D] focus:ring-[#2D5A3D]:ring-[#2D5A3D] rounded-md shadow-sm">
                                    <option value="">Tất cả</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Lọc
                                </button>
                                <a href="{{ route('admin.reviews.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                    Xóa lọc
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tour</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Điểm</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Likes</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($reviews as $review)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-gray-900">
                                            <a href="{{ route('admin.reviews.show', $review) }}" class="text-[#2D5A3D] hover:underline">
                                                #{{ $review->review_id }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $review->user->username ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($review->tour)
                                                <a href="{{ route('admin.tours.show', $review->tour) }}" class="text-[#2D5A3D] hover:underline">
                                                    {{ \Illuminate\Support\Str::limit($review->tour->title, 40) }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="text-yellow-400">
                                                {!! str_repeat('&#9733;', $review->score) !!}{!! str_repeat('&#9734;', 5 - $review->score) !!}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                            <a href="{{ route('admin.reviews.show', $review) }}" class="block text-gray-800 hover:text-[#2D5A3D]:text-[#2D5A3D] mb-1 transition">
                                                {{ \Illuminate\Support\Str::limit($review->content, 80) }}
                                            </a>
                                            @if($review->images->isNotEmpty())
                                                <div class="flex gap-1.5 mt-1 flex-wrap">
                                                    @foreach($review->images as $img)
                                                        <a href="{{ route('admin.reviews.show', $review) }}" title="Xem ảnh đính kèm">
                                                            <img src="{{ $img->secure_url }}" alt="Review photo" class="w-8 h-8 object-cover rounded border border-gray-200 hover:scale-110 transition" onerror="this.style.display='none'">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($review->status === 'pending')
                                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Chờ duyệt
                                                </span>
                                            @elseif ($review->status === 'approved')
                                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Đã duyệt
                                                </span>
                                            @elseif ($review->status === 'rejected')
                                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Đã từ chối
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="text-red-500">&hearts;</span> {{ $review->likes_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $review->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ route('admin.reviews.show', $review) }}" class="text-[#2D5A3D] hover:text-[#2D5A3D]:text-[#2D5A3D] font-semibold">Chi tiết</a>

                                                @if ($review->status === 'pending' || $review->status === 'rejected')
                                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900">Duyệt</button>
                                                    </form>
                                                @endif
                                                
                                                @if ($review->status === 'pending' || $review->status === 'approved')
                                                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                                        @csrf
                                                        <button type="submit" class="text-orange-600 hover:text-orange-900">Từ chối</button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này không?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                            Chưa có đánh giá nào.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
