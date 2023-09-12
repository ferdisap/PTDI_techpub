<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name_register" :value="__('Name')" />
            <x-text-input id="name_register" class="block mt-1 w-full" type="text" name="name_register" :value="old('name_register')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name_register')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email_register" :value="__('Email')" />
            <x-text-input id="email_register" class="block mt-1 w-full" type="email" name="email_register" :value="old('email_register')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email_register')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password_register" :value="__('Password')" />

            <x-text-input id="password_register" class="block mt-1 w-full"
                            type="password"
                            name="password_register"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_register')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_register_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_register_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_register_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_register_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
