<x-admin-layout title="Chỉnh sửa người dùng:  {{ $user->username }}">
    <div>
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @php
                        $isEditingSelf = $user->user_id === auth()->user()->user_id;
                    @endphp

                    @if($isEditingSelf)
                        <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                            Bạn đang chỉnh sửa tài khoản của chính mình. Không thể thay đổi vai trò.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6 max-w-2xl">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username</label>
                            <p class="mt-1 text-gray-900">{{ $user->username }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vai trò</label>
                            <select name="role" {{ $isEditingSelf ? 'disabled' : '' }} class="mt-1 block w-full border-gray-300 focus:border-[#2D5A3D]:border-[#2D5A3D] focus:ring-[#2D5A3D]:ring-[#2D5A3D] rounded-md shadow-sm">
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @if($isEditingSelf)
                                <input type="hidden" name="role" value="{{ $user->role }}">
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select name="status" class="mt-1 block w-full border-gray-300 focus:border-[#2D5A3D]:border-[#2D5A3D] focus:ring-[#2D5A3D]:ring-[#2D5A3D] rounded-md shadow-sm">
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="banned" {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Banned</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2:ring-offset-gray-800 transition ease-in-out duration-150">
                                Cập nhật
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                Trở về
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
