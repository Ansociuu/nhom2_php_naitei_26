<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Connected Social Accounts') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Social accounts linked to your profile for quick sign-in.') }}
        </p>
    </header>

    <div class="mt-6 space-y-4">
        @php
            $providers = [
                'google'          => ['label' => 'Google',   'color' => 'text-red-500'],
                'facebook'        => ['label' => 'Facebook', 'color' => 'text-blue-600'],
                'twitter-oauth-2' => ['label' => 'Twitter',  'color' => 'text-sky-500'],
            ];

            $linked = $socialAccounts->keyBy('provider');
        @endphp

        @foreach ($providers as $providerKey => $info)
            <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-200 dark:border-gray-700' : '' }}">
                <div class="flex items-center gap-3">
                    <span class="font-medium {{ $info['color'] }}">{{ $info['label'] }}</span>
                </div>

                <div>
                    @if ($linked->has($providerKey))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            ✓ {{ __('Connected') }}
                        </span>
                        <span class="ml-2 text-xs text-gray-400">
                            {{ $linked[$providerKey]->linked_at?->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                            {{ __('Not connected') }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
