<x-dashboard-layout>
    <x-slot:title>Book a Session — Daimaa</x-slot:title>
    <x-slot:heading>Book a Session</x-slot:heading>
    <x-slot:sidebar>
        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl text-on-surface-variant hover:bg-surface-container">
            <span class="material-symbols-outlined text-xl">dashboard</span> Dashboard
        </a>
        <a href="{{ route('customer.book') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl bg-primary-fixed/40 text-primary font-semibold">
            <span class="material-symbols-outlined text-xl">add_circle</span> New Booking
        </a>
        <a href="{{ route('customer.bookings') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl text-on-surface-variant hover:bg-surface-container">
            <span class="material-symbols-outlined text-xl">calendar_month</span> My Bookings
        </a>
        <a href="{{ route('customer.addresses') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl text-on-surface-variant hover:bg-surface-container">
            <span class="material-symbols-outlined text-xl">location_on</span> Addresses
        </a>
        <a href="{{ route('customer.reviews') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl text-on-surface-variant hover:bg-surface-container">
            <span class="material-symbols-outlined text-xl">rate_review</span> My Reviews
        </a>
    </x-slot:sidebar>

    <livewire:customer.booking-wizard />
</x-dashboard-layout>
