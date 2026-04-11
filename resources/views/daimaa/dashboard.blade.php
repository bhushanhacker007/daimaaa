<x-dashboard-layout>
    <x-slot:title>Daimaa Dashboard</x-slot:title>
    <x-slot:heading>Dashboard</x-slot:heading>
    <x-slot:sidebar>@include('daimaa._sidebar')</x-slot:sidebar>

    @php
        $user = Auth::user();
        $profile = $user->daimaaProfile;

        $todaySessions = \App\Models\BookingSession::where('daimaa_id', $user->id)
            ->whereIn('status', ['upcoming', 'scheduled', 'started'])
            ->whereDate('scheduled_at', today())
            ->with(['booking.customer', 'booking.package', 'booking.service', 'booking.address', 'service'])
            ->orderBy('scheduled_at')
            ->get();

        $upcomingSessions = \App\Models\BookingSession::where('daimaa_id', $user->id)
            ->whereIn('status', ['upcoming', 'scheduled'])
            ->count();
        $inProgressSessions = \App\Models\BookingSession::where('daimaa_id', $user->id)
            ->where('status', 'started')
            ->count();
        $completedSessions = \App\Models\BookingSession::where('daimaa_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $totalEarnings = \App\Models\Payout::where('daimaa_id', $user->id)
            ->where('status', 'processed')
            ->sum('amount');

        $nextSession = \App\Models\BookingSession::where('daimaa_id', $user->id)
            ->whereIn('status', ['upcoming', 'scheduled'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now())
            ->with(['booking.customer', 'booking.package', 'booking.service', 'booking.address', 'service'])
            ->orderBy('scheduled_at')
            ->first();

        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
        $greetingIcon = $hour < 12 ? '☀️' : ($hour < 17 ? '🌤️' : '🌙');
    @endphp

    {{-- KYC Status Banner --}}
    @if($profile && $profile->status !== 'verified')
        <div class="mb-6 p-5 sm:p-6 rounded-2xl {{ $profile->status === 'pending' ? 'bg-tertiary-fixed/20 border border-tertiary/15' : 'bg-error-container border border-error/15' }}">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl {{ $profile->status === 'pending' ? 'bg-tertiary' : 'bg-error' }} flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-2xl">{{ $profile->status === 'pending' ? 'hourglass_top' : 'warning' }}</span>
                </div>
                <div>
                    <p class="text-lg font-bold {{ $profile->status === 'pending' ? 'text-tertiary' : 'text-on-error-container' }}">
                        Profile {{ ucfirst($profile->status) }}
                    </p>
                    <p class="text-base text-on-surface-variant mt-1 leading-relaxed">
                        {{ $profile->status === 'pending'
                            ? 'Your profile is under review. We will let you know once it is verified. Thank you for your patience!'
                            : 'Your verification was not approved. Please update your documents and try again.' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Pending Booking Requests (auto-dispatch) --}}
    <livewire:daimaa.booking-request />

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl cta-gradient p-6 sm:p-8 mb-6">
        <div class="relative z-10">
            <p class="text-on-primary/70 text-base font-medium mb-1">{{ $greetingIcon }} {{ $greeting }}</p>
            <h2 class="text-2xl sm:text-3xl font-headline font-bold text-on-primary mb-2">{{ $user->name }}</h2>
            <p class="text-on-primary/80 text-base max-w-lg leading-relaxed">
                @if($todaySessions->count() > 0)
                    You have <strong>{{ $todaySessions->count() }} {{ Str::plural('visit', $todaySessions->count()) }}</strong> today. Have a wonderful day!
                @elseif($upcomingSessions > 0)
                    No visits today. Your next visit is coming up soon.
                @else
                    No visits scheduled yet. Enjoy your rest!
                @endif
            </p>
        </div>
        <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-on-primary/5"></div>
        <div class="absolute -bottom-6 -right-2 w-28 h-28 rounded-full bg-on-primary/5"></div>
    </div>

    {{-- Stat Cards — Large, high-contrast, easy to read --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <a href="{{ route('daimaa.bookings') }}" class="bg-surface-container-lowest rounded-2xl ghost-border p-5 sm:p-6 hover:shadow-ambient transition-shadow group">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-tertiary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-tertiary text-2xl sm:text-3xl">event</span>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-headline font-bold text-tertiary">{{ $upcomingSessions }}</p>
            <p class="text-sm sm:text-base text-on-surface-variant mt-1 font-medium">Upcoming Visits</p>
        </a>

        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-2xl sm:text-3xl">autorenew</span>
                </div>
                @if($inProgressSessions > 0)
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                    </span>
                @endif
            </div>
            <p class="text-3xl sm:text-4xl font-headline font-bold text-primary">{{ $inProgressSessions }}</p>
            <p class="text-sm sm:text-base text-on-surface-variant mt-1 font-medium">In Progress</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-secondary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-secondary-container text-2xl sm:text-3xl">task_alt</span>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-headline font-bold text-on-surface">{{ $completedSessions }}</p>
            <p class="text-sm sm:text-base text-on-surface-variant mt-1 font-medium">Completed</p>
        </div>

        <a href="{{ route('daimaa.payouts') }}" class="bg-surface-container-lowest rounded-2xl ghost-border p-5 sm:p-6 hover:shadow-ambient transition-shadow group">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-tertiary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-tertiary text-2xl sm:text-3xl">currency_rupee</span>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-headline font-bold text-tertiary">₹{{ number_format($totalEarnings) }}</p>
            <p class="text-sm sm:text-base text-on-surface-variant mt-1 font-medium">Earnings</p>
        </a>
    </div>

    {{-- Quick Actions — Big, tap-friendly buttons --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <a href="{{ route('daimaa.bookings') }}" class="flex flex-col items-center gap-3 bg-surface-container-lowest rounded-2xl ghost-border p-5 hover:shadow-ambient hover:-translate-y-0.5 transition-all group">
            <div class="w-14 h-14 rounded-2xl cta-gradient flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                <span class="material-symbols-outlined text-on-primary text-2xl">calendar_month</span>
            </div>
            <span class="text-sm sm:text-base font-semibold text-on-surface text-center">My Visits</span>
        </a>

        <a href="{{ route('daimaa.schedule') }}" class="flex flex-col items-center gap-3 bg-surface-container-lowest rounded-2xl ghost-border p-5 hover:shadow-ambient hover:-translate-y-0.5 transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-primary-fixed flex items-center justify-center group-hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-primary group-hover:text-on-primary text-2xl transition-colors">event_available</span>
            </div>
            <span class="text-sm sm:text-base font-semibold text-on-surface text-center">Schedule</span>
        </a>

        <a href="{{ route('daimaa.payouts') }}" class="flex flex-col items-center gap-3 bg-surface-container-lowest rounded-2xl ghost-border p-5 hover:shadow-ambient hover:-translate-y-0.5 transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-primary-fixed flex items-center justify-center group-hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-primary group-hover:text-on-primary text-2xl transition-colors">payments</span>
            </div>
            <span class="text-sm sm:text-base font-semibold text-on-surface text-center">Payouts</span>
        </a>

        <a href="{{ route('daimaa.profile') }}" class="flex flex-col items-center gap-3 bg-surface-container-lowest rounded-2xl ghost-border p-5 hover:shadow-ambient hover:-translate-y-0.5 transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-primary-fixed flex items-center justify-center group-hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-primary group-hover:text-on-primary text-2xl transition-colors">person</span>
            </div>
            <span class="text-sm sm:text-base font-semibold text-on-surface text-center">Profile</span>
        </a>
    </div>

    {{-- Today's Schedule --}}
    @if($todaySessions->count() > 0)
        <div class="mb-6">
            <h2 class="text-xl font-headline font-bold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">today</span>
                Today's Visits
            </h2>
            <div class="space-y-4">
                @foreach($todaySessions as $ts)
                    @php
                        $tsStatusConfig = match($ts->status) {
                            'started' => ['bg' => 'bg-primary-fixed/40', 'text' => 'text-primary', 'icon' => 'autorenew', 'label' => 'In Progress', 'border' => 'border-primary/20'],
                            'scheduled' => ['bg' => 'bg-tertiary-fixed/30', 'text' => 'text-tertiary', 'icon' => 'event_available', 'label' => 'Scheduled', 'border' => 'border-tertiary/15'],
                            default => ['bg' => 'bg-tertiary-fixed/20', 'text' => 'text-tertiary', 'icon' => 'schedule', 'label' => 'Upcoming', 'border' => 'border-tertiary/10'],
                        };
                    @endphp
                    <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden border {{ $tsStatusConfig['border'] }}">
                        {{-- Session header with time --}}
                        <div class="flex items-center gap-4 p-5 sm:p-6">
                            {{-- Time block --}}
                            <div class="shrink-0 text-center">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl {{ $tsStatusConfig['bg'] }} flex flex-col items-center justify-center">
                                    @if($ts->scheduled_at)
                                        <span class="text-lg sm:text-xl font-headline font-bold {{ $tsStatusConfig['text'] }}">{{ $ts->scheduled_at->format('g:i') }}</span>
                                        <span class="text-xs font-semibold {{ $tsStatusConfig['text'] }}/70">{{ $ts->scheduled_at->format('A') }}</span>
                                    @else
                                        <span class="material-symbols-outlined text-2xl {{ $tsStatusConfig['text'] }}">schedule</span>
                                        <span class="text-[10px] font-medium {{ $tsStatusConfig['text'] }}/70">TBD</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <div>
                                        <h3 class="text-base sm:text-lg font-bold text-on-surface leading-tight">
                                            {{ $ts->service?->name ?? $ts->booking?->package?->name ?? $ts->booking?->service?->name ?? 'Visit' }}
                                        </h3>
                                        <p class="text-sm sm:text-base text-on-surface-variant mt-0.5">
                                            Session #{{ $ts->session_number }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold {{ $tsStatusConfig['bg'] }} {{ $tsStatusConfig['text'] }}">
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1">{{ $tsStatusConfig['icon'] }}</span>
                                        {{ $tsStatusConfig['label'] }}
                                    </span>
                                </div>

                                {{-- Customer & Address --}}
                                <div class="flex flex-col gap-1.5 mt-2">
                                    <div class="flex items-center gap-2 text-sm sm:text-base text-on-surface-variant">
                                        <span class="material-symbols-outlined text-primary text-lg">person</span>
                                        <span class="font-medium">{{ $ts->booking?->customer?->name ?? 'Customer' }}</span>
                                        @if($ts->booking?->customer?->phone)
                                            <a href="tel:{{ $ts->booking->customer->phone }}" class="inline-flex items-center gap-1 text-primary font-semibold ml-auto">
                                                <span class="material-symbols-outlined text-lg">call</span>
                                                <span class="hidden sm:inline">Call</span>
                                            </a>
                                        @endif
                                    </div>
                                    @if($ts->booking?->address)
                                        <div class="flex items-start gap-2 text-sm text-on-surface-variant/70">
                                            <span class="material-symbols-outlined text-lg mt-0.5 shrink-0">location_on</span>
                                            <span>{{ $ts->booking->address->address_line_1 }}, {{ $ts->booking->address->pincode }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Customer & Address --}}
                        <div class="bg-surface-container rounded-2xl p-4 mx-5 sm:mx-6 mb-5 sm:mb-6">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-primary text-xl">person</span>
                                    </div>
                                    <p class="text-base font-semibold text-on-surface">{{ $ts->booking?->customer?->name ?? 'Customer' }}</p>
                                </div>
                                @if($ts->booking?->customer?->phone)
                                    <a href="tel:{{ $ts->booking->customer->phone }}"
                                        class="shrink-0 w-12 h-12 rounded-2xl bg-primary flex items-center justify-center shadow-md">
                                        <span class="material-symbols-outlined text-on-primary text-xl">call</span>
                                    </a>
                                @endif
                            </div>
                            @if($ts->booking?->address)
                                <div class="flex items-start gap-2 mt-3 pt-3 border-t border-outline-variant/10">
                                    <span class="material-symbols-outlined text-primary text-lg mt-0.5 shrink-0">location_on</span>
                                    <span class="text-sm text-on-surface-variant">{{ $ts->booking->address->address_line_1 }}, {{ $ts->booking->address->pincode }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Optimized Route (if 2+ sessions today with geo data) --}}
    @if($todaySessions->count() >= 2)
        @php
            $optimizedRoute = \App\Services\RouteOptimizerService::optimizeRoute($user->id);
            $homeLat = $profile?->home_latitude;
            $homeLng = $profile?->home_longitude;
            $mapsUrl = \App\Services\RouteOptimizerService::getGoogleMapsUrl($optimizedRoute, $homeLat ? (float) $homeLat : null, $homeLng ? (float) $homeLng : null);
        @endphp
        <div class="mb-6">
            <h2 class="text-xl font-headline font-bold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">route</span>
                Best Route for Today
            </h2>

            <div class="bg-surface-container-lowest rounded-2xl ghost-border p-5 space-y-3">
                @foreach($optimizedRoute as $rs)
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full cta-gradient text-on-primary flex items-center justify-center text-lg font-bold shrink-0">
                            {{ $rs->route_order }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-base font-bold text-on-surface truncate">
                                {{ $rs->service?->name ?? $rs->booking?->service?->name ?? 'Visit' }}
                            </p>
                            <p class="text-sm text-on-surface-variant">
                                {{ $rs->scheduled_at?->format('g:i A') ?? 'TBD' }}
                                &middot; {{ $rs->booking?->address?->address_line_1 ?? '' }}
                            </p>
                        </div>
                    </div>
                @endforeach

                @if($mapsUrl !== '#')
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                        class="flex items-center justify-center gap-3 w-full px-6 py-4 cta-gradient text-on-primary rounded-2xl text-base font-bold shadow-md hover:shadow-lg transition-shadow mt-4">
                        <span class="material-symbols-outlined text-2xl">directions</span>
                        Open in Google Maps
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- Next Upcoming Visit (if not today) --}}
    @if($nextSession && !$todaySessions->contains('id', $nextSession->id))
        <div class="mb-6">
            <h2 class="text-xl font-headline font-bold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">upcoming</span>
                Next Visit
            </h2>
            <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                <div class="flex flex-col sm:flex-row">
                    {{-- Date highlight --}}
                    <div class="sm:w-32 shrink-0 cta-gradient flex sm:flex-col items-center sm:justify-center gap-2 sm:gap-1 px-5 py-4 sm:py-6 text-center">
                        <span class="text-on-primary/70 text-sm font-semibold uppercase">{{ $nextSession->scheduled_at->format('M') }}</span>
                        <span class="text-3xl sm:text-4xl font-headline font-bold text-on-primary leading-none">{{ $nextSession->scheduled_at->format('d') }}</span>
                        <span class="text-on-primary/70 text-sm font-medium">{{ $nextSession->scheduled_at->format('D') }}</span>
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 p-5 sm:p-6">
                        <h3 class="text-lg font-bold text-on-surface mb-1">
                            {{ $nextSession->service?->name ?? $nextSession->booking?->package?->name ?? $nextSession->booking?->service?->name ?? 'Visit' }}
                        </h3>
                        <p class="text-sm text-on-surface-variant mb-3">Session #{{ $nextSession->session_number }}</p>

                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-base text-on-surface-variant mb-4">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-xl">schedule</span>
                                {{ $nextSession->scheduled_at->format('g:i A') }}
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-xl">person</span>
                                {{ $nextSession->booking?->customer?->name ?? 'Customer' }}
                            </span>
                        </div>

                        @if($nextSession->booking?->address)
                            <div class="flex items-start gap-2 text-sm text-on-surface-variant/70 bg-surface-container rounded-xl px-4 py-3">
                                <span class="material-symbols-outlined text-base shrink-0 mt-0.5">location_on</span>
                                <span>{{ $nextSession->booking->address->address_line_1 }}, {{ $nextSession->booking->address->pincode }}</span>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('daimaa.bookings') }}" class="btn-outline text-sm px-5 py-2.5 inline-flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                View All Visits
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Empty state when no sessions at all --}}
    @if($upcomingSessions === 0 && $inProgressSessions === 0 && $completedSessions === 0)
        <div class="text-center py-16 bg-surface-container-lowest rounded-2xl ghost-border">
            <div class="w-20 h-20 rounded-full bg-primary-fixed flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-primary text-4xl">spa</span>
            </div>
            <p class="text-xl text-on-surface font-semibold mb-2">Welcome to Daimaa!</p>
            <p class="text-base text-on-surface-variant max-w-md mx-auto leading-relaxed">
                Once you are verified and assigned visits, they will appear here. Thank you for being part of our family.
            </p>
        </div>
    @endif
</x-dashboard-layout>
