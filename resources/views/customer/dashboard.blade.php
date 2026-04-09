<x-dashboard-layout>
    <x-slot:title>My Dashboard — Daimaa</x-slot:title>
    <x-slot:heading>Welcome, {{ Auth::user()->name }}</x-slot:heading>
    <x-slot:sidebar>
        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.dashboard') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
            <span class="material-symbols-outlined text-xl">dashboard</span> Dashboard
        </a>
        <a href="{{ route('customer.book') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.book') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
            <span class="material-symbols-outlined text-xl">add_circle</span> New Booking
        </a>
        <a href="{{ route('customer.bookings') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.bookings*') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
            <span class="material-symbols-outlined text-xl">calendar_month</span> My Bookings
        </a>
        <a href="{{ route('customer.addresses') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.addresses') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
            <span class="material-symbols-outlined text-xl">location_on</span> Addresses
        </a>
        <a href="{{ route('customer.reviews') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.reviews') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
            <span class="material-symbols-outlined text-xl">rate_review</span> My Reviews
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors text-on-surface-variant hover:bg-surface-container">
            <span class="material-symbols-outlined text-xl">person</span> Profile
        </a>
    </x-slot:sidebar>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @php
            $totalBookings = Auth::user()->bookings()->count();
            $activeBookings = Auth::user()->bookings()->whereIn('status', ['pending', 'confirmed', 'assigned', 'in_progress'])->count();
            $completedBookings = Auth::user()->bookings()->where('status', 'completed')->count();
        @endphp
        <div class="bg-surface-container-lowest rounded-2xl p-6">
            <span class="material-symbols-outlined text-primary text-3xl mb-2">calendar_month</span>
            <p class="text-3xl font-headline font-bold text-primary">{{ $totalBookings }}</p>
            <p class="text-sm text-on-surface-variant">Total Bookings</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-6">
            <span class="material-symbols-outlined text-tertiary text-3xl mb-2">pending_actions</span>
            <p class="text-3xl font-headline font-bold text-tertiary">{{ $activeBookings }}</p>
            <p class="text-sm text-on-surface-variant">Active Bookings</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-6">
            <span class="material-symbols-outlined text-secondary text-3xl mb-2">task_alt</span>
            <p class="text-3xl font-headline font-bold text-secondary">{{ $completedBookings }}</p>
            <p class="text-sm text-on-surface-variant">Completed</p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-headline font-bold text-primary">Recent Bookings</h2>
        <a href="{{ route('customer.book') }}" class="btn-primary text-sm">+ New Booking</a>
    </div>
    <livewire:customer.my-bookings />
</x-dashboard-layout>
