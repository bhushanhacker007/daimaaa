<x-dashboard-layout>
    <x-slot:title>My Dashboard — Daimaa</x-slot:title>
    <x-slot:heading>Dashboard</x-slot:heading>
    <x-slot:sidebar>@include('customer._sidebar')</x-slot:sidebar>

    @php
        $user = Auth::user();
        $totalBookings = $user->bookings()->count();
        $activeBookings = $user->bookings()->whereIn('status', ['pending', 'confirmed', 'assigned', 'in_progress'])->count();
        $completedBookings = $user->bookings()->where('status', 'completed')->count();
        $reviewCount = $user->reviews()->count();
        $pendingReviews = $user->bookings()->where('status', 'completed')->whereDoesntHave('review')->count();

        $nextBooking = $user->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'assigned'])
            ->where('scheduled_date', '>=', now()->toDateString())
            ->with(['package', 'service', 'address', 'assignments.daimaa'])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->first();

        $recentBookings = $user->bookings()
            ->with(['package', 'service'])
            ->latest()
            ->take(5)
            ->get();

        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
    @endphp

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl cta-gradient p-6 sm:p-8 mb-6">
        <div class="relative z-10">
            <p class="text-on-primary/70 text-sm font-medium mb-1">{{ $greeting }}</p>
            <h2 class="text-2xl sm:text-3xl font-headline font-bold text-on-primary mb-2">{{ $user->name }} 🙏</h2>
            <p class="text-on-primary/80 text-sm max-w-md">
                @if($activeBookings > 0)
                    You have {{ $activeBookings }} active {{ Str::plural('booking', $activeBookings) }}.
                @elseif($completedBookings > 0)
                    All your bookings are complete. Ready for another session?
                @else
                    Welcome to Daimaa! Book your first care session today.
                @endif
            </p>
        </div>
        {{-- Decorative circles --}}
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-on-primary/5"></div>
        <div class="absolute -bottom-6 -right-2 w-24 h-24 rounded-full bg-on-primary/5"></div>
    </div>

    {{-- Unscheduled Sessions Banner --}}
    @php
        $bookingsWithUnscheduled = $user->bookings()
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->whereHas('sessions', fn ($q) => $q->where('status', 'upcoming'))
            ->with(['package', 'service', 'sessions' => fn ($q) => $q->where('status', 'upcoming')])
            ->get();
        $totalUnscheduled = $bookingsWithUnscheduled->sum(fn ($b) => $b->sessions->count());
    @endphp

    @if($totalUnscheduled > 0)
        <div class="mb-6 bg-tertiary-fixed/20 border border-tertiary/15 rounded-2xl overflow-hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-5">
                <div class="w-12 h-12 rounded-2xl bg-tertiary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-on-tertiary text-xl">edit_calendar</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-on-surface">You have {{ $totalUnscheduled }} {{ Str::plural('session', $totalUnscheduled) }} to schedule</p>
                    <p class="text-xs text-on-surface-variant mt-0.5">Pick your preferred dates and times for upcoming sessions.</p>
                </div>
            </div>
            <div class="border-t border-tertiary/10 divide-y divide-tertiary/10">
                @foreach($bookingsWithUnscheduled->take(3) as $ub)
                    <a href="{{ route('customer.bookings.show', $ub->id) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-tertiary-fixed/10 transition-colors group">
                        <span class="material-symbols-outlined text-tertiary text-base">inventory_2</span>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-medium text-on-surface truncate block">{{ $ub->package?->name ?? $ub->service?->name ?? 'Booking' }}</span>
                        </div>
                        <span class="text-xs font-bold text-tertiary bg-tertiary-fixed/30 px-2 py-0.5 rounded-full">{{ $ub->sessions->count() }} pending</span>
                        <span class="material-symbols-outlined text-on-surface-variant/30 text-sm group-hover:text-tertiary group-hover:translate-x-0.5 transition-all">arrow_forward_ios</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-4 sm:p-5 group hover:shadow-ambient transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
                </div>
                @if($totalBookings > 0)
                    <a href="{{ route('customer.bookings') }}" class="text-xs text-primary font-medium opacity-0 group-hover:opacity-100 transition-opacity">View</a>
                @endif
            </div>
            <p class="text-2xl sm:text-3xl font-headline font-bold text-primary">{{ $totalBookings }}</p>
            <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">Total Bookings</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-4 sm:p-5 group hover:shadow-ambient transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-tertiary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-tertiary text-xl">pending_actions</span>
                </div>
                @if($activeBookings > 0)
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-tertiary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-tertiary"></span>
                    </span>
                @endif
            </div>
            <p class="text-2xl sm:text-3xl font-headline font-bold text-tertiary">{{ $activeBookings }}</p>
            <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">Active</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-4 sm:p-5 group hover:shadow-ambient transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-secondary-container text-xl">task_alt</span>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-headline font-bold text-on-surface">{{ $completedBookings }}</p>
            <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">Completed</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-4 sm:p-5 group hover:shadow-ambient transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-xl">star</span>
                </div>
                @if($pendingReviews > 0)
                    <span class="text-[10px] font-bold bg-tertiary text-on-tertiary px-2 py-0.5 rounded-full">{{ $pendingReviews }} new</span>
                @endif
            </div>
            <p class="text-2xl sm:text-3xl font-headline font-bold text-on-surface">{{ $reviewCount }}</p>
            <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">Reviews</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <a href="{{ route('customer.book') }}" class="flex flex-col items-center gap-2 bg-surface-container-lowest rounded-2xl ghost-border p-4 sm:p-5 hover:shadow-ambient hover:-translate-y-0.5 transition-all group">
            <div class="w-12 h-12 rounded-2xl cta-gradient flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                <span class="material-symbols-outlined text-on-primary text-xl">add_circle</span>
            </div>
            <span class="text-xs sm:text-sm font-semibold text-on-surface text-center">New Booking</span>
        </a>

        <a href="{{ route('customer.bookings') }}" class="flex flex-col items-center gap-2 bg-surface-container-lowest rounded-2xl ghost-border p-4 sm:p-5 hover:shadow-ambient hover:-translate-y-0.5 transition-all group">
            <div class="w-12 h-12 rounded-2xl bg-primary-fixed flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-primary group-hover:text-on-primary text-xl">list_alt</span>
            </div>
            <span class="text-xs sm:text-sm font-semibold text-on-surface text-center">My Bookings</span>
        </a>

        <a href="{{ route('customer.reviews') }}" class="flex flex-col items-center gap-2 bg-surface-container-lowest rounded-2xl ghost-border p-4 sm:p-5 hover:shadow-ambient hover:-translate-y-0.5 transition-all group relative">
            @if($pendingReviews > 0)
                <span class="absolute top-2 right-2 flex h-5 w-5 items-center justify-center rounded-full bg-tertiary text-on-tertiary text-[10px] font-bold">{{ $pendingReviews }}</span>
            @endif
            <div class="w-12 h-12 rounded-2xl bg-primary-fixed flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-primary group-hover:text-on-primary text-xl">rate_review</span>
            </div>
            <span class="text-xs sm:text-sm font-semibold text-on-surface text-center">Reviews</span>
        </a>

        <a href="{{ route('customer.addresses') }}" class="flex flex-col items-center gap-2 bg-surface-container-lowest rounded-2xl ghost-border p-4 sm:p-5 hover:shadow-ambient hover:-translate-y-0.5 transition-all group">
            <div class="w-12 h-12 rounded-2xl bg-primary-fixed flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-primary group-hover:text-on-primary text-xl">location_on</span>
            </div>
            <span class="text-xs sm:text-sm font-semibold text-on-surface text-center">Addresses</span>
        </a>
    </div>

    {{-- Next Upcoming Booking --}}
    @if($nextBooking)
        <div class="mb-6">
            <h2 class="text-lg font-headline font-bold text-on-surface mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">upcoming</span>
                Upcoming Booking
            </h2>
            <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                <div class="flex flex-col sm:flex-row">
                    {{-- Date highlight --}}
                    <div class="sm:w-28 shrink-0 cta-gradient flex sm:flex-col items-center sm:justify-center gap-2 sm:gap-0 px-5 py-4 sm:py-6 text-center">
                        <span class="text-on-primary/70 text-xs font-semibold uppercase">{{ $nextBooking->scheduled_date->format('M') }}</span>
                        <span class="text-3xl sm:text-4xl font-headline font-bold text-on-primary leading-none">{{ $nextBooking->scheduled_date->format('d') }}</span>
                        <span class="text-on-primary/70 text-xs font-medium">{{ $nextBooking->scheduled_date->format('D') }}</span>
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <h3 class="font-semibold text-on-surface text-base">
                                    {{ $nextBooking->package?->name ?? $nextBooking->service?->name ?? 'Booking' }}
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-0.5">{{ $nextBooking->booking_number }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold shrink-0
                                {{ match($nextBooking->status) {
                                    'pending' => 'bg-tertiary-fixed/30 text-tertiary',
                                    'confirmed', 'assigned' => 'bg-primary-fixed/40 text-primary',
                                    default => 'bg-surface-container text-on-surface-variant',
                                } }}">
                                {{ ucfirst($nextBooking->status) }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-on-surface-variant mb-4">
                            @if($nextBooking->scheduled_time)
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base text-primary">schedule</span>
                                    {{ \Carbon\Carbon::parse($nextBooking->scheduled_time)->format('g:i A') }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-primary">payments</span>
                                ₹{{ number_format($nextBooking->total_amount) }}
                            </span>
                            @php $assignedDaimaa = $nextBooking->assignments->firstWhere('accepted_at', '!=', null)?->daimaa; @endphp
                            @if($assignedDaimaa)
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base text-primary">person</span>
                                    {{ $assignedDaimaa->name }}
                                </span>
                            @endif
                        </div>

                        @if($nextBooking->address)
                            <div class="flex items-start gap-2 text-xs text-on-surface-variant/70 bg-surface-container rounded-xl px-3 py-2.5">
                                <span class="material-symbols-outlined text-sm shrink-0 mt-0.5">location_on</span>
                                <span>{{ $nextBooking->address->address_line_1 }}, {{ $nextBooking->address->pincode }}</span>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('customer.bookings.show', $nextBooking->id) }}" class="btn-outline text-xs px-4 py-2">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Bookings --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-headline font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">history</span>
                Recent Activity
            </h2>
            @if($totalBookings > 0)
                <a href="{{ route('customer.bookings') }}" class="text-xs text-primary font-semibold hover:underline flex items-center gap-1">
                    View all <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            @endif
        </div>

        @if($recentBookings->count())
            <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden divide-y divide-[rgba(218,193,186,0.15)]">
                @foreach($recentBookings as $booking)
                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-surface-container/50 transition-colors group">
                        {{-- Icon --}}
                        <div @class([
                            'w-10 h-10 rounded-xl flex items-center justify-center shrink-0',
                            'bg-primary-fixed text-primary' => in_array($booking->status, ['pending', 'confirmed', 'assigned']),
                            'bg-secondary-container text-on-secondary-container' => $booking->status === 'completed',
                            'bg-tertiary-fixed text-tertiary' => $booking->status === 'in_progress',
                            'bg-error-container text-on-error-container' => in_array($booking->status, ['cancelled', 'refunded']),
                        ])>
                            <span class="material-symbols-outlined text-lg">
                                {{ match($booking->status) {
                                    'completed' => 'check_circle',
                                    'cancelled', 'refunded' => 'cancel',
                                    'in_progress' => 'autorenew',
                                    default => 'event',
                                } }}
                            </span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-on-surface truncate">{{ $booking->package?->name ?? $booking->service?->name ?? 'Booking' }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-on-surface-variant/60">{{ $booking->scheduled_date->format('M j, Y') }}</span>
                                <span class="text-xs font-medium
                                    {{ match($booking->status) {
                                        'completed' => 'text-secondary',
                                        'cancelled', 'refunded' => 'text-error',
                                        'in_progress' => 'text-tertiary',
                                        default => 'text-primary',
                                    } }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </div>
                        </div>

                        {{-- Price + arrow --}}
                        <div class="text-right shrink-0 flex items-center gap-2">
                            <span class="text-sm font-bold text-on-surface">₹{{ number_format($booking->total_amount) }}</span>
                            <span class="material-symbols-outlined text-on-surface-variant/30 text-sm group-hover:text-primary group-hover:translate-x-0.5 transition-all">arrow_forward_ios</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-14 bg-surface-container-lowest rounded-2xl ghost-border">
                <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-primary text-3xl">spa</span>
                </div>
                <p class="text-on-surface font-semibold mb-1">No bookings yet</p>
                <p class="text-sm text-on-surface-variant mb-5">Start your care journey with Daimaa</p>
                <a href="{{ route('customer.book') }}" class="btn-primary text-sm px-6 py-3 inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    Book Your First Session
                </a>
            </div>
        @endif
    </div>

    {{-- Pending Reviews Prompt --}}
    @if($pendingReviews > 0)
        <div class="mt-6 bg-tertiary-fixed/30 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-tertiary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-on-tertiary">rate_review</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-on-surface">You have {{ $pendingReviews }} {{ Str::plural('booking', $pendingReviews) }} to review</p>
                <p class="text-xs text-on-surface-variant mt-0.5">Your feedback helps other mothers find the best care.</p>
            </div>
            <a href="{{ route('customer.reviews') }}" class="btn-primary text-xs px-5 py-2.5 shrink-0 whitespace-nowrap">Write Reviews</a>
        </div>
    @endif
</x-dashboard-layout>
