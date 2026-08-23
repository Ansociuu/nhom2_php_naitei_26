<section>
    <header>
        <h2 class="card-title">
            Thông tin hồ sơ
        </h2>

        <p class="mt-1 muted-text">
            Cập nhật tên đăng nhập và email của tài khoản.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="username" value="Tên đăng nhập" />
            <x-text-input id="username" name="username" type="text" class="mt-1.5 form-control" :value="old('username', $user->username)" required autofocus autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1.5 form-control" :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-700">
                        Email của bạn chưa được xác thực.

                        <button form="send-verification" class="underline text-sm text-gray-500 hover:text-gray-800">
                            Gửi lại email xác thực
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-600">
                            Đã gửi liên kết xác thực mới tới email của bạn.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Lưu</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-500"
                >Đã lưu.</p>
            @endif
        </div>
    </form>
</section>
