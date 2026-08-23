<section>
    <header>
        <h2 class="card-title">
            Tài khoản ngân hàng
        </h2>

        <p class="mt-1 muted-text">
            Thêm hoặc cập nhật tài khoản ngân hàng để nhận thanh toán/hoàn tiền.
        </p>
    </header>

    <form method="post" action="{{ route('profile.bank.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Bank Name Dropdown --}}
        <div>
            <x-input-label for="bank_name" value="Ngân hàng" />
            <select
                id="bank_name"
                name="bank_name"
                class="mt-1.5 form-control"
                required
            >
                <option value="">Chọn ngân hàng</option>
                @foreach ($banks as $code => $name)
                    <option value="{{ $code }}" @selected(old('bank_name', $bankAccount?->bank_name) === $code)>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
        </div>

        {{-- Account Number --}}
        <div>
            <x-input-label for="account_number" value="Số tài khoản" />
            <x-text-input
                id="account_number"
                name="account_number"
                type="text"
                class="mt-1.5 form-control"
                :value="old('account_number', $bankAccount?->account_number)"
                required
                placeholder="VD: 0123456789"
                inputmode="numeric"
                pattern="[0-9]*"
            />
            <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
        </div>

        {{-- Account Holder Name --}}
        <div>
            <x-input-label for="account_holder_name" value="Tên chủ tài khoản" />
            <x-text-input
                id="account_holder_name"
                name="account_holder_name"
                type="text"
                class="mt-1.5 form-control uppercase"
                :value="old('account_holder_name', $bankAccount?->account_holder_name)"
                required
                placeholder="VD: NGUYEN VAN A"
            />
            <p class="mt-1 text-xs text-gray-500">
                Nhập đúng tên như trên tài khoản ngân hàng (viết hoa).
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('account_holder_name')" />
        </div>

        {{-- Verification Status (Read-only) --}}
        @if ($bankAccount)
            <div class="flex items-center gap-2 text-sm">
                @if ($bankAccount->is_verified)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-[#2D5A3D]">
                        ✓ Đã xác thực
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                        Đang chờ xác thực
                    </span>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>Lưu tài khoản ngân hàng</x-primary-button>

            @if (session('status') === 'bank-account-updated')
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
