<x-guest-layout>
    <x-slot:title>{{ __('Register') }} — {{ __('Daimaa') }}</x-slot:title>

    <h2 class="text-2xl font-headline font-bold text-primary mb-6">{{ __('Create Account') }}</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <div>
            <label for="name" class="text-sm font-medium text-on-surface mb-1 block">{{ __('Full Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="input-field">
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-error" />
        </div>

        <div>
            <label for="email" class="text-sm font-medium text-on-surface mb-1 block">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="input-field">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-error" />
        </div>

        <div>
            <label for="phone" class="text-sm font-medium text-on-surface mb-1 block">{{ __('Phone (optional)') }}</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="input-field" placeholder="+91 XXXXX XXXXX">
        </div>

        <div>
            <label for="password" class="text-sm font-medium text-on-surface mb-1 block">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="input-field">
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-error" />
        </div>

        <div>
            <label for="password_confirmation" class="text-sm font-medium text-on-surface mb-1 block">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input-field">
        </div>

        <button type="submit" class="btn-primary w-full">{{ __('Create Account') }}</button>

        <p class="text-center text-sm text-on-surface-variant">
            {{ __('Already have an account?') }} <a href="{{ route('login') }}" class="text-primary font-semibold hover:text-primary-container transition-colors">{{ __('Sign In') }}</a>
        </p>
    </form>
</x-guest-layout>
