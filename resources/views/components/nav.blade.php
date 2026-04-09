<nav class="fixed top-0 w-full z-50 glass-nav" x-data="{ mobileOpen: false, langOpen: false }">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2">
                <img src="/images/logo.png" alt="Daimaa" class="h-12 w-auto object-contain">
                <span class="text-2xl font-headline tracking-tight text-primary">Daimaa</span>
            </a>

            {{-- Desktop navigation --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors {{ request()->routeIs('home') ? 'text-primary' : '' }}">{{ __('Home') }}</a>
                <a href="{{ route('services') }}" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors {{ request()->routeIs('services*') ? 'text-primary' : '' }}">{{ __('Services') }}</a>
                <a href="{{ route('how-it-works') }}" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors {{ request()->routeIs('how-it-works') ? 'text-primary' : '' }}">{{ __('How It Works') }}</a>
                <a href="{{ route('about') }}" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors {{ request()->routeIs('about') ? 'text-primary' : '' }}">{{ __('About') }}</a>
                <a href="{{ route('contact') }}" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors {{ request()->routeIs('contact') ? 'text-primary' : '' }}">{{ __('Contact') }}</a>
            </div>

            {{-- Language switcher + Auth buttons --}}
            <div class="hidden md:flex items-center gap-3">
                {{-- Language dropdown --}}
                <div class="relative" @click.outside="langOpen = false">
                    <button @click="langOpen = !langOpen" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-base">translate</span>
                        <span class="uppercase">{{ app()->getLocale() }}</span>
                        <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="langOpen ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="langOpen" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-40 bg-surface-container-lowest rounded-xl ambient-shadow overflow-hidden py-1 z-50">
                        <a href="{{ route('lang.switch', 'en') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-surface-container transition-colors {{ app()->getLocale() === 'en' ? 'text-primary font-semibold' : 'text-on-surface-variant' }}">
                            <span class="text-base">EN</span>
                            <span>{{ __('English') }}</span>
                            @if(app()->getLocale() === 'en')<span class="material-symbols-outlined text-primary text-sm ml-auto">check</span>@endif
                        </a>
                        <a href="{{ route('lang.switch', 'hi') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-surface-container transition-colors {{ app()->getLocale() === 'hi' ? 'text-primary font-semibold' : 'text-on-surface-variant' }}">
                            <span class="text-base">HI</span>
                            <span>{{ __('Hindi') }}</span>
                            @if(app()->getLocale() === 'hi')<span class="material-symbols-outlined text-primary text-sm ml-auto">check</span>@endif
                        </a>
                        <a href="{{ route('lang.switch', 'mr') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-surface-container transition-colors {{ app()->getLocale() === 'mr' ? 'text-primary font-semibold' : 'text-on-surface-variant' }}">
                            <span class="text-base">MR</span>
                            <span>{{ __('Marathi') }}</span>
                            @if(app()->getLocale() === 'mr')<span class="material-symbols-outlined text-primary text-sm ml-auto">check</span>@endif
                        </a>
                    </div>
                </div>

                @auth
                    @php $dashRoute = match(Auth::user()->role) {
                        'admin', 'super_admin' => 'admin.dashboard',
                        'daimaa' => 'daimaa.dashboard',
                        default => 'customer.dashboard',
                    }; @endphp
                    <a href="{{ route($dashRoute) }}" class="btn-primary text-sm">{{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-primary hover:text-primary-container transition-colors">{{ __('Sign In') }}</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm">{{ __('Book Now') }}</a>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-on-surface-variant">
                <span x-show="!mobileOpen" class="material-symbols-outlined">menu</span>
                <span x-show="mobileOpen" x-cloak class="material-symbols-outlined">close</span>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-cloak x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="md:hidden bg-surface-container-lowest rounded-b-2xl mx-4 mb-2 p-4 space-y-2 ambient-shadow">
        <a href="{{ route('home') }}" class="block px-4 py-3 text-sm font-medium rounded-xl hover:bg-surface-container transition-colors {{ request()->routeIs('home') ? 'text-primary bg-surface-container' : 'text-on-surface-variant' }}">{{ __('Home') }}</a>
        <a href="{{ route('services') }}" class="block px-4 py-3 text-sm font-medium rounded-xl hover:bg-surface-container transition-colors {{ request()->routeIs('services*') ? 'text-primary bg-surface-container' : 'text-on-surface-variant' }}">{{ __('Services') }}</a>
        <a href="{{ route('how-it-works') }}" class="block px-4 py-3 text-sm font-medium rounded-xl hover:bg-surface-container transition-colors {{ request()->routeIs('how-it-works') ? 'text-primary bg-surface-container' : 'text-on-surface-variant' }}">{{ __('How It Works') }}</a>
        <a href="{{ route('about') }}" class="block px-4 py-3 text-sm font-medium rounded-xl hover:bg-surface-container transition-colors {{ request()->routeIs('about') ? 'text-primary bg-surface-container' : 'text-on-surface-variant' }}">{{ __('About') }}</a>
        <a href="{{ route('contact') }}" class="block px-4 py-3 text-sm font-medium rounded-xl hover:bg-surface-container transition-colors {{ request()->routeIs('contact') ? 'text-primary bg-surface-container' : 'text-on-surface-variant' }}">{{ __('Contact') }}</a>
        {{-- Mobile language switcher --}}
        <div class="pt-2 flex items-center gap-2 px-4">
            <span class="material-symbols-outlined text-on-surface-variant text-base">translate</span>
            <div class="flex gap-1.5">
                <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ app()->getLocale() === 'en' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">EN</a>
                <a href="{{ route('lang.switch', 'hi') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ app()->getLocale() === 'hi' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">HI</a>
                <a href="{{ route('lang.switch', 'mr') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ app()->getLocale() === 'mr' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">MR</a>
            </div>
        </div>

        <div class="pt-2 space-y-2">
            @auth
                <a href="{{ route($dashRoute ?? 'customer.dashboard') }}" class="block btn-primary text-sm text-center">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="block text-center px-4 py-3 text-sm font-medium text-primary rounded-xl hover:bg-surface-container transition-colors">{{ __('Sign In') }}</a>
                <a href="{{ route('register') }}" class="block btn-primary text-sm text-center">{{ __('Book Now') }}</a>
            @endauth
        </div>
    </div>
</nav>
