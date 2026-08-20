<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Bank Account Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add or update your Vietnamese bank account for tour payment and refunds.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.bank.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Bank Name Dropdown --}}
        <div>
            <x-input-label for="bank_name" :value="__('Bank')" />
            <select
                id="bank_name"
                name="bank_name"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                required
            >
                <option value="">{{ __('Choose Bank') }}</option>
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
            <x-input-label for="account_number" :value="__('Account Number')" />
            <x-text-input
                id="account_number"
                name="account_number"
                type="text"
                class="mt-1 block w-full"
                :value="old('account_number', $bankAccount?->account_number)"
                required
                placeholder="e.g. 0123456789"
                inputmode="numeric"
                pattern="[0-9]*"
            />
            <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
        </div>

        {{-- Account Holder Name --}}
        <div>
            <x-input-label for="account_holder_name" :value="__('Account Holder Name')" />
            <x-text-input
                id="account_holder_name"
                name="account_holder_name"
                type="text"
                class="mt-1 block w-full uppercase"
                :value="old('account_holder_name', $bankAccount?->account_holder_name)"
                required
                placeholder="e.g. NGUYEN VAN A"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Enter name exactly as it appears on your bank account (uppercase).') }}
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('account_holder_name')" />
        </div>

        {{-- Verification Status (Read-only) --}}
        @if ($bankAccount)
            <div class="flex items-center gap-2 text-sm">
                @if ($bankAccount->is_verified)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        ✓ {{ __('Verified') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                        {{ __('Pending verification') }}
                    </span>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Bank Account') }}</x-primary-button>

            @if (session('status') === 'bank-account-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
