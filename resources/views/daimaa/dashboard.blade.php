<x-dashboard-layout>
    <x-slot:title>Daimaa Dashboard</x-slot:title>
    <x-slot:heading>Welcome, {{ Auth::user()->name }}</x-slot:heading>
    <x-slot:sidebar>@include('daimaa._sidebar')</x-slot:sidebar>

    @php
        $profile = Auth::user()->daimaaProfile;
        $upcomingSessions = \App\Models\BookingSession::where('daimaa_id', auth()->id())->where('status', 'upcoming')->count();
        $completedSessions = \App\Models\BookingSession::where('daimaa_id', auth()->id())->where('status', 'completed')->count();
        $totalEarnings = \App\Models\Payout::where('daimaa_id', auth()->id())->where('status', 'processed')->sum('amount');
    @endphp

    {{-- KYC Status Banner --}}
    @if($profile && $profile->status !== 'verified')
    <div class="mb-6 p-4 rounded-2xl {{ $profile->status === 'pending' ? 'bg-tertiary-fixed/20' : 'bg-error-container' }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined {{ $profile->status === 'pending' ? 'text-tertiary' : 'text-on-error-container' }}">{{ $profile->status === 'pending' ? 'hourglass_top' : 'warning' }}</span>
            <div>
                <p class="font-semibold {{ $profile->status === 'pending' ? 'text-tertiary' : 'text-on-error-container' }}">Profile {{ ucfirst($profile->status) }}</p>
                <p class="text-sm text-on-surface-variant">{{ $profile->status === 'pending' ? 'Your profile is under review. You will be notified once verified.' : 'Your verification was rejected. Please update your documents.' }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface-container-lowest rounded-2xl p-6">
            <span class="material-symbols-outlined text-primary text-3xl mb-2">event</span>
            <p class="text-3xl font-headline font-bold text-primary">{{ $upcomingSessions }}</p>
            <p class="text-sm text-on-surface-variant">Upcoming Sessions</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-6">
            <span class="material-symbols-outlined text-secondary text-3xl mb-2">task_alt</span>
            <p class="text-3xl font-headline font-bold text-secondary">{{ $completedSessions }}</p>
            <p class="text-sm text-on-surface-variant">Completed Sessions</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-6">
            <span class="material-symbols-outlined text-tertiary text-3xl mb-2">currency_rupee</span>
            <p class="text-3xl font-headline font-bold text-tertiary">₹{{ number_format($totalEarnings) }}</p>
            <p class="text-sm text-on-surface-variant">Total Earnings</p>
        </div>
    </div>

    <livewire:daimaa.assigned-bookings />
</x-dashboard-layout>
