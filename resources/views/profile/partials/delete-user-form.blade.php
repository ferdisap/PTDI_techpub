<section>
  <header>
    <h2 class="fs-4 fw-semibold">
          {{ __('Delete Account') }}
      </h2>

      <p class="mt-1 fs-6">
        Once your account is deleted, all of its resources and data will be <span class="text-warning">permanently deleted</span>. Before deleting your account, please download any data or information that you wish to retain.
      </p>
  </header>

  {{-- <x-danger-button
      x-data=""
      x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
  >{{ __('Delete Account') }}</x-danger-button> --}}
  {{-- <x-danger-button>{{ __('Delete Account') }}</x-danger-button> --}}
  <button type="submit" data-bs-toggle="modal" data-bs-target="#delete_account" class="d-inline-flex align-items-center px-3 py-1 border bg-danger rounded fw-semibold fs-6 text-white text-uppercase">Delete Account</button>

  <div class="modal fade" id="delete_account" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
      @csrf
      @method('delete')
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete Account</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="mt-1 fs-4">Are you sure you want to delete your account?</p>
            <p class="mt-1 fs-6">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>
            <div class="mt-4">
              <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
  
              <x-text-input
                  id="password"
                  name="password"
                  type="password"
                  class="mt-1"
                  placeholder="{{ __('Password') }}"
              />
          </div>


            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger">Understood</button>
          </div>
        </div>
      </div>
    </form>
  </div>
  

  <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
      <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
          @csrf
          @method('delete')

          <h2 class="text-lg font-medium text-gray-900">
              {{ __('Are you sure you want to delete your account?') }}
          </h2>

          <p class="mt-1 text-sm text-gray-600">
              {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
          </p>

          <div class="mt-6">
              <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

              <x-text-input
                  id="password"
                  name="password"
                  type="password"
                  class="mt-1 block w-3/4"
                  placeholder="{{ __('Password') }}"
              />

              <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
          </div>

          <div class="mt-6 flex justify-end">
              <x-secondary-button x-on:click="$dispatch('close')">
                  {{ __('Cancel') }}
              </x-secondary-button>

              <x-danger-button class="ml-3">
                  {{ __('Delete Account') }}
              </x-danger-button>
          </div>
      </form>
  </x-modal>
</section>

{{-- <section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section> --}}
