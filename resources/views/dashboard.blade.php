<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-3">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

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