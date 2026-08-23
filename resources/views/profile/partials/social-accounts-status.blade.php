<section>
    <header>
        <h2 class="card-title">
            Tài khoản mạng xã hội
        </h2>

        <p class="mt-1 muted-text">
            Liên kết tài khoản mạng xã hội để đăng nhập nhanh hơn.
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
            <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b' : '' }}">
                <div class="flex items-center gap-3">
                    <span class="font-medium {{ $info['color'] }}">{{ $info['label'] }}</span>
                </div>

                <div>
                    @if ($linked->has($providerKey))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-[#2D5A3D]">
                            ✓ Đã liên kết
                        </span>
                        <span class="ml-2 text-xs text-gray-400">
                            {{ $linked[$providerKey]->linked_at?->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                            Chưa liên kết
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
