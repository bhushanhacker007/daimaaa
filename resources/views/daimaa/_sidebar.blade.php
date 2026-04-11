@php
    $upcomingCount = \App\Models\BookingSession::where('daimaa_id', auth()->id())
        ->whereIn('status', ['upcoming', 'scheduled'])
        ->count();
    $inProgressCount = \App\Models\BookingSession::where('daimaa_id', auth()->id())
        ->where('status', 'started')
        ->count();
    $_daimaaOnline = \App\Models\DaimaaProfile::where('user_id', auth()->id())->value('is_online') ?? true;
@endphp

{{-- Online / Offline status badge --}}
<div class="mx-3 mb-3 flex items-center gap-2.5 px-4 py-3 rounded-2xl {{ $_daimaaOnline ? 'bg-primary-fixed/30' : 'bg-error-container/40' }}">
    <span class="relative flex h-3.5 w-3.5 shrink-0">
        @if($_daimaaOnline)
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-60"></span>
        @endif
        <span class="relative inline-flex rounded-full h-3.5 w-3.5 {{ $_daimaaOnline ? 'bg-primary' : 'bg-error' }}"></span>
    </span>
    <span class="text-sm font-bold {{ $_daimaaOnline ? 'text-primary' : 'text-error' }}">
        {{ $_daimaaOnline ? 'Online' : 'Offline' }}
    </span>
</div>

<a href="{{ route('daimaa.dashboard') }}" class="flex items-center gap-3.5 px-4 py-4 text-base rounded-2xl transition-colors {{ request()->routeIs('daimaa.dashboard') ? 'bg-primary-fixed/40 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-2xl" @if(request()->routeIs('daimaa.dashboard')) style="font-variation-settings: 'FILL' 1" @endif>dashboard</span>
    Dashboard
</a>

<a href="{{ route('daimaa.bookings') }}" class="flex items-center gap-3.5 px-4 py-4 text-base rounded-2xl transition-colors relative {{ request()->routeIs('daimaa.bookings') ? 'bg-primary-fixed/40 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-2xl" @if(request()->routeIs('daimaa.bookings')) style="font-variation-settings: 'FILL' 1" @endif>calendar_month</span>
    My Visits
    @if($upcomingCount + $inProgressCount > 0)
        <span class="ml-auto flex h-6 min-w-[24px] items-center justify-center rounded-full bg-tertiary text-on-tertiary text-xs font-bold px-1.5">{{ $upcomingCount + $inProgressCount }}</span>
    @endif
</a>

<a href="{{ route('daimaa.schedule') }}" class="flex items-center gap-3.5 px-4 py-4 text-base rounded-2xl transition-colors {{ request()->routeIs('daimaa.schedule') ? 'bg-primary-fixed/40 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-2xl" @if(request()->routeIs('daimaa.schedule')) style="font-variation-settings: 'FILL' 1" @endif>event_available</span>
    Schedule
</a>

<a href="{{ route('daimaa.payouts') }}" class="flex items-center gap-3.5 px-4 py-4 text-base rounded-2xl transition-colors {{ request()->routeIs('daimaa.payouts') ? 'bg-primary-fixed/40 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-2xl" @if(request()->routeIs('daimaa.payouts')) style="font-variation-settings: 'FILL' 1" @endif>payments</span>
    Payouts
</a>

<a href="{{ route('daimaa.profile') }}" class="flex items-center gap-3.5 px-4 py-4 text-base rounded-2xl transition-colors {{ request()->routeIs('daimaa.profile') ? 'bg-primary-fixed/40 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-2xl" @if(request()->routeIs('daimaa.profile')) style="font-variation-settings: 'FILL' 1" @endif>person</span>
    My Profile
</a>
