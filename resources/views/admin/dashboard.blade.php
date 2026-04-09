<x-dashboard-layout>
    <x-slot:title>Admin Dashboard — Daimaa</x-slot:title>
    <x-slot:heading>Admin Dashboard</x-slot:heading>
    <x-slot:sidebar>@include('admin._sidebar')</x-slot:sidebar>

    @php
        $totalCustomers = \App\Models\User::where('role', 'customer')->count();
        $totalDaimaas = \App\Models\User::where('role', 'daimaa')->count();
        $pendingKyc = \App\Models\DaimaaProfile::where('status', 'pending')->count();
        $totalBookings = \App\Models\Booking::count();
        $activeBookings = \App\Models\Booking::whereIn('status', ['pending', 'confirmed', 'assigned', 'in_progress'])->count();
        $revenue = \App\Models\Payment::where('status', 'success')->sum('amount');
        $recentBookings = \App\Models\Booking::with(['customer', 'package', 'service'])->latest()->take(5)->get();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-surface-container-lowest rounded-2xl p-5">
            <span class="material-symbols-outlined text-primary text-2xl">group</span>
            <p class="text-2xl font-headline font-bold text-primary mt-2">{{ $totalCustomers }}</p>
            <p class="text-xs text-on-surface-variant">Customers</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-5">
            <span class="material-symbols-outlined text-secondary text-2xl">diversity_2</span>
            <p class="text-2xl font-headline font-bold text-secondary mt-2">{{ $totalDaimaas }}</p>
            <p class="text-xs text-on-surface-variant">Daimaas</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-5">
            <span class="material-symbols-outlined text-tertiary text-2xl">pending_actions</span>
            <p class="text-2xl font-headline font-bold text-tertiary mt-2">{{ $pendingKyc }}</p>
            <p class="text-xs text-on-surface-variant">Pending KYC</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-5">
            <span class="material-symbols-outlined text-primary text-2xl">calendar_month</span>
            <p class="text-2xl font-headline font-bold text-primary mt-2">{{ $totalBookings }}</p>
            <p class="text-xs text-on-surface-variant">Total Bookings</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-5">
            <span class="material-symbols-outlined text-tertiary text-2xl">event_available</span>
            <p class="text-2xl font-headline font-bold text-tertiary mt-2">{{ $activeBookings }}</p>
            <p class="text-xs text-on-surface-variant">Active</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-5">
            <span class="material-symbols-outlined text-primary text-2xl">currency_rupee</span>
            <p class="text-2xl font-headline font-bold text-primary mt-2">₹{{ number_format($revenue) }}</p>
            <p class="text-xs text-on-surface-variant">Revenue</p>
        </div>
    </div>

    <h2 class="text-xl font-headline font-bold text-primary mb-4">Recent Bookings</h2>
    <div class="bg-surface-container-lowest rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-surface-container text-on-surface-variant text-left">
                <th class="px-6 py-3 font-medium">Booking</th>
                <th class="px-6 py-3 font-medium">Customer</th>
                <th class="px-6 py-3 font-medium">Service</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium">Amount</th>
            </tr></thead>
            <tbody>
                @foreach($recentBookings as $b)
                <tr class="hover:bg-surface-container/50 transition-colors">
                    <td class="px-6 py-3 font-medium text-on-surface">{{ $b->booking_number }}</td>
                    <td class="px-6 py-3 text-on-surface-variant">{{ $b->customer?->name }}</td>
                    <td class="px-6 py-3 text-on-surface-variant">{{ $b->package?->name ?? $b->service?->name }}</td>
                    <td class="px-6 py-3"><span class="px-2 py-1 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
                    <td class="px-6 py-3 font-semibold text-primary">₹{{ number_format($b->total_amount) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-dashboard-layout>
