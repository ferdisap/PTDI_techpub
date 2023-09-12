<x-app-layout>
  <x-slot name="header">
      <h2 class="fw-semibold fs-3">
          {{ __('Profile') }}
      </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto px-sm-3 px-lg-3" style="max-width:80rem">
        <div class="bg-white overflow-hidden shadow-sm rounded">
            <div style="max-width:36rem">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded">
            <div style="max-width:36rem">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded">
            <div style="max-width:36rem">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
  </div>

  <div class="py-5">
      <div class="mx-auto px-sm-3 px-lg-3" style="max-width:80rem">
          <div class="bg-white overflow-hidden shadow-sm rounded">
              <div class="p-4">
                  {{ __("You're logged in!") }}
              </div>
          </div>
      </div>
  </div>
</x-app-layout>
{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}
