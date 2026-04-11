<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard — Daimaa' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-surface-container-low font-body antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex">
            {{-- Sidebar --}}
            <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-surface-container-lowest h-screen sticky top-0">
                <div class="p-6">
                    <a href="/" class="flex items-center gap-2">
                        <img src="/images/logo.png" alt="Daimaa" class="h-10 w-auto object-contain">
                        <span class="text-xl font-headline text-primary">Daimaa</span>
                    </a>
                </div>

                <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                    {{ $sidebar }}
                </nav>

                <div class="p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-sm text-on-surface-variant rounded-xl hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined text-xl">logout</span>
                            {{ __('Sign Out') }}
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Mobile sidebar overlay --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-300" class="fixed inset-0 z-40 bg-on-surface/30 lg:hidden" @click="sidebarOpen = false"></div>
            <aside x-show="sidebarOpen" x-transition:enter="transition-transform duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition-transform duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-64 bg-surface-container-lowest lg:hidden flex flex-col">
                <div class="p-6 flex justify-between items-center">
                    <a href="/" class="flex items-center gap-2">
                        <img src="/images/logo.png" alt="Daimaa" class="h-10 w-auto object-contain">
                        <span class="text-xl font-headline text-primary">Daimaa</span>
                    </a>
                    <button @click="sidebarOpen = false" class="text-on-surface-variant">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                    {{ $sidebar }}
                </nav>
                <div class="p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-sm text-error rounded-xl hover:bg-error-container/30 transition-colors">
                            <span class="material-symbols-outlined text-xl">logout</span>
                            {{ __('Sign Out') }}
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Main content --}}
            <div class="flex-1 flex flex-col min-w-0">
                <header class="sticky top-0 z-30 glass-nav px-6 py-4 flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-on-surface-variant">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <div class="flex-1">
                        @isset($heading)
                            <h1 class="text-xl font-headline font-bold text-primary">{{ $heading }}</h1>
                        @endisset
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-on-surface-variant">{{ Auth::user()->name }}</span>
                        <div class="h-9 w-9 rounded-full bg-primary-fixed-dim flex items-center justify-center text-sm font-bold text-on-primary-fixed">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
