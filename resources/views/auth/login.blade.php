<x-guest-layout>
    <x-slot:title>{{ __('Sign In') }} — {{ __('Daimaa') }}</x-slot:title>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-2xl font-headline font-bold text-primary mb-6">{{ __('Sign In') }}</h2>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="text-sm font-medium text-on-surface mb-1 block">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input-field">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-error" />
        </div>

        <div>
            <label for="password" class="text-sm font-medium text-on-surface mb-1 block">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="input-field">
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-error" />
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded text-primary focus:ring-primary">
                <span class="text-sm text-on-surface-variant">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-primary hover:text-primary-container transition-colors">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full">{{ __('Sign In') }}</button>

        <p class="text-center text-sm text-on-surface-variant">
            {{ __("Don't have an account?") }} <a href="{{ route('register') }}" class="text-primary font-semibold hover:text-primary-container transition-colors">{{ __('Register') }}</a>
        </p>
    </form>
</x-guest-layout>
