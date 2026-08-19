<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Account Overview') }}
        </h2>
    </header>

    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="text-gray-500 dark:text-gray-400">{{ __('Username') }}</span>
            <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user->username }}</p>
        </div>

        <div>
            <span class="text-gray-500 dark:text-gray-400">{{ __('Email') }}</span>
            <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
        </div>

        <div>
            <span class="text-gray-500 dark:text-gray-400">{{ __('Role') }}</span>
            <p class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </p>
        </div>

        <div>
            <span class="text-gray-500 dark:text-gray-400">{{ __('Status') }}</span>
            <p class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $user->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                    {{ ucfirst($user->status) }}
                </span>
            </p>
        </div>

        <div>
            <span class="text-gray-500 dark:text-gray-400">{{ __('Member Since') }}</span>
            <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d/m/Y') }}</p>
        </div>

        <div>
            <span class="text-gray-500 dark:text-gray-400">{{ __('Last Login') }}</span>
            <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $user->last_login_at?->diffForHumans() ?? __('N/A') }}</p>
        </div>
    </div>
</section>
