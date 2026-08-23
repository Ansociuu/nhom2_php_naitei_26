<x-site-layout title="Hồ sơ cá nhân">
    @php
        $initialTab = 'info';
        if ($errors->updatePassword->isNotEmpty() || session('status') === 'password-updated') {
            $initialTab = 'security';
        } elseif ($errors->userDeletion->isNotEmpty()) {
            $initialTab = 'delete';
        } elseif ($errors->default->has('bank_name') || $errors->default->has('account_number') || $errors->default->has('account_holder_name') || session('status') === 'bank-account-updated') {
            $initialTab = 'bank';
        }
    @endphp

    <div class="container-page py-8" x-data="{ tab: '{{ $initialTab }}' }">
        <h1 class="page-title">Cài đặt tài khoản</h1>
        <p class="page-subtitle">Quản lý thông tin cá nhân, bảo mật và thanh toán</p>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">
            <div class="space-y-4">
                <div class="card-surface p-6 text-center">
                    <span class="mx-auto w-16 h-16 rounded-full bg-[#2D5A3D] text-white text-2xl font-bold flex items-center justify-center">
                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                    </span>
                    <div class="mt-3 card-title">{{ auth()->user()->username }}</div>
                    <div class="text-base text-gray-400 truncate">{{ auth()->user()->email }}</div>
                </div>

                <div class="card-surface overflow-hidden text-base">
                    <button type="button" @click="tab = 'info'"
                            :class="tab === 'info' ? 'bg-emerald-50 text-[#2D5A3D] font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-5 py-3 text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4 shrink-0"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" stroke-linecap="round"/></svg>
                        Thông tin cá nhân
                    </button>
                    <button type="button" @click="tab = 'security'"
                            :class="tab === 'security' ? 'bg-emerald-50 text-[#2D5A3D] font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-5 py-3 text-left border-t">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4 shrink-0"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke-linecap="round"/></svg>
                        Bảo mật &amp; Mật khẩu
                    </button>
                    <button type="button" @click="tab = 'bank'"
                            :class="tab === 'bank' ? 'bg-emerald-50 text-[#2D5A3D] font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-5 py-3 text-left border-t">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4 shrink-0"><path d="M3 10 12 4l9 6" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 10v9M19 10v9M9 10v9M15 10v9M3 19h18" stroke-linecap="round"/></svg>
                        Tài khoản ngân hàng
                    </button>
                    <button type="button" @click="tab = 'social'"
                            :class="tab === 'social' ? 'bg-emerald-50 text-[#2D5A3D] font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-5 py-3 text-left border-t">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4 shrink-0"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 10.5 6.8-3.9M8.6 13.5l6.8 3.9" stroke-linecap="round"/></svg>
                        Mạng xã hội
                    </button>
                    <a href="{{ route('bookings.index') }}"
                       class="w-full flex items-center gap-3 px-5 py-3 text-left border-t text-gray-700 hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4 shrink-0"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/></svg>
                        Lịch sử đặt tour
                    </a>
                    <a href="{{ route('reviews.index') }}"
                       class="w-full flex items-center gap-3 px-5 py-3 text-left border-t text-gray-700 hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4 shrink-0"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/></svg>
                        Đánh giá chuyến đi
                    </a>
                    <button type="button" @click="tab = 'delete'"
                            :class="tab === 'delete' ? 'bg-red-50 text-red-600 font-semibold' : 'text-red-500 hover:bg-red-50'"
                            class="w-full flex items-center gap-3 px-5 py-3 text-left border-t">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4 shrink-0"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Xoá tài khoản
                    </button>
                </div>
            </div>

            <div>
                <div x-show="tab === 'info'" class="space-y-6">
                    <div class="card-surface p-6 sm:p-8">
                        @include('profile.partials.profile-overview')
                    </div>
                    <div class="card-surface p-6 sm:p-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div x-show="tab === 'security'" class="card-surface p-6 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>

                <div x-show="tab === 'bank'" class="card-surface p-6 sm:p-8">
                    @include('profile.partials.update-bank-account-form')
                </div>

                <div x-show="tab === 'social'" class="card-surface p-6 sm:p-8">
                    @include('profile.partials.social-accounts-status')
                </div>

                <div x-show="tab === 'delete'" class="card-surface p-6 sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-site-layout>
