<x-guest-layout>
    <div class="px-7 pt-6 pb-8">
        <div class="grid grid-cols-2 gap-1 p-1 bg-gray-100 rounded-full">
            <a href="{{ route('login') }}" class="text-center py-2.5 text-sm font-bold rounded-full text-gray-500 hover:text-gray-800">
                Đăng nhập
            </a>
            <span class="text-center py-2.5 text-sm font-bold rounded-full bg-white text-[#2D6A2D] shadow-sm">
                Đăng ký
            </span>
        </div>

        <div class="mt-6 text-center">
            <h1 class="text-xl font-extrabold text-gray-900">Tạo tài khoản mới</h1>
            <p class="mt-1 text-sm text-gray-500">Tham gia cùng chúng tôi cho hành trình cuối tuần</p>
        </div>

        <div class="mt-6 grid grid-cols-3 gap-2">
            <a href="{{ route('social.redirect', 'google') }}"
               class="inline-flex items-center justify-center gap-2 px-3 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.2-2.27H12v4.51h6.47a5.53 5.53 0 0 1-2.4 3.63v3h3.88c2.27-2.09 3.54-5.17 3.54-8.87Z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.07 7.93-2.9l-3.87-3a7.4 7.4 0 0 1-11-3.9H1.06v3.09A12 12 0 0 0 12 24Z"/><path fill="#FBBC05" d="M5.04 14.2A7.2 7.2 0 0 1 4.66 12c0-.77.13-1.51.38-2.2V6.71H1.06A12 12 0 0 0 0 12c0 1.93.46 3.76 1.06 4.29l3.98-2.09Z"/><path fill="#EA4335" d="M12 4.75c1.76 0 3.34.6 4.59 1.8l3.44-3.44C17.94 1.19 15.24 0 12 0A12 12 0 0 0 1.06 6.71l3.98 3.09A7.16 7.16 0 0 1 12 4.75Z"/></svg>
                <span class="hidden sm:inline">Google</span>
            </a>
            <a href="{{ route('social.redirect', 'facebook') }}"
               class="inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-md text-sm font-medium text-white bg-[#1877F2] hover:bg-[#1465d1]">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.5 1.5-3.89 3.79-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.9h-2.34V22c4.78-.79 8.44-4.93 8.44-9.94Z"/></svg>
                <span class="hidden sm:inline">Facebook</span>
            </a>
            <a href="{{ route('social.redirect', 'twitter-oauth-2') }}"
               class="inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-md text-sm font-medium text-white bg-gray-900 hover:bg-black">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.9 2H22l-7.6 8.7L23 22h-6.9l-5.4-6.7L4.5 22H1.4l8.2-9.3L1 2h7.1l4.9 6.1L18.9 2Zm-1.2 18h1.9L7.4 4h-2l12.3 16Z"/></svg>
            </a>
        </div>

        <div class="relative flex items-center py-5">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="mx-4 text-gray-400 text-xs uppercase">hoặc</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="username" value="Tên đăng nhập" />
                <x-text-input id="username" class="block mt-1.5 w-full rounded-xl py-3" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1.5 w-full rounded-xl py-3" type="email" name="email" :value="old('email')"
                              placeholder="your@email.com" required autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" value="Mật khẩu" />
                <x-password-input id="password" class="block mt-1.5 w-full rounded-xl py-3" name="password"
                              required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" />
                <x-password-input id="password_confirmation" class="block mt-1.5 w-full rounded-xl py-3"
                              name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <x-primary-button class="w-full !py-3.5 !rounded-xl text-base">
                Tạo Tài Khoản
            </x-primary-button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="font-semibold text-[#2D6A2D] hover:underline">Đăng nhập</a>
        </p>
    </div>
</x-guest-layout>
