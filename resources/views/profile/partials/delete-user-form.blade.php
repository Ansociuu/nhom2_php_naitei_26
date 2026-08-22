<section class="space-y-6">
    <header>
        <h2 class="card-title">
            Xoá tài khoản
        </h2>

        <p class="mt-1 muted-text">
            Sau khi xoá, toàn bộ dữ liệu tài khoản sẽ bị xoá vĩnh viễn. Vui lòng tải về những thông tin bạn muốn giữ lại trước khi xoá.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Xoá tài khoản</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="card-title">
                Bạn có chắc muốn xoá tài khoản?
            </h2>

            <p class="mt-1 muted-text">
                Sau khi xoá, toàn bộ dữ liệu sẽ bị xoá vĩnh viễn. Vui lòng nhập mật khẩu để xác nhận xoá tài khoản.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Mật khẩu" class="sr-only" />

                <x-password-input
                    id="password"
                    name="password"
                    class="mt-1.5 form-control"
                    placeholder="Mật khẩu"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Huỷ
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Xoá tài khoản
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
