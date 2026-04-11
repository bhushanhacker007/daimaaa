<div x-data="{ view: 'list' }">

    {{-- Top Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <button wire:click="$set('statusFilter', '')"
            class="relative overflow-hidden rounded-2xl p-4 text-left transition-all {{ $statusFilter === '' ? 'ring-2 ring-primary shadow-ambient' : 'ghost-border hover:shadow-ambient' }} bg-surface-container-lowest">
            <div class="flex items-center gap-2.5 mb-2">
                <div class="w-9 h-9 rounded-xl cta-gradient flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary text-lg">calendar_month</span>
                </div>
                <span class="text-2xl font-headline font-bold text-primary">{{ $counts->total ?? 0 }}</span>
            </div>
            <p class="text-xs font-medium text-on-surface-variant">All Bookings</p>
        </button>

        <button wire:click="$set('statusFilter', 'active')"
            class="relative overflow-hidden rounded-2xl p-4 text-left transition-all {{ $statusFilter === 'active' ? 'ring-2 ring-tertiary shadow-ambient' : 'ghost-border hover:shadow-ambient' }} bg-surface-container-lowest">
            <div class="flex items-center gap-2.5 mb-2">
                <div class="w-9 h-9 rounded-xl bg-tertiary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-tertiary text-lg">pending_actions</span>
                </div>
                <span class="text-2xl font-headline font-bold text-tertiary">{{ $counts->active ?? 0 }}</span>
            </div>
            <p class="text-xs font-medium text-on-surface-variant">Active</p>
            @if(($counts->active ?? 0) > 0)
                <span class="absolute top-3 right-3 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-tertiary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-tertiary"></span>
                </span>
            @endif
        </button>

        <button wire:click="$set('statusFilter', 'completed')"
            class="relative overflow-hidden rounded-2xl p-4 text-left transition-all {{ $statusFilter === 'completed' ? 'ring-2 ring-secondary shadow-ambient' : 'ghost-border hover:shadow-ambient' }} bg-surface-container-lowest">
            <div class="flex items-center gap-2.5 mb-2">
                <div class="w-9 h-9 rounded-xl bg-secondary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-secondary-container text-lg">task_alt</span>
                </div>
                <span class="text-2xl font-headline font-bold text-on-surface">{{ $counts->completed ?? 0 }}</span>
            </div>
            <p class="text-xs font-medium text-on-surface-variant">Completed</p>
        </button>

        <button wire:click="$set('statusFilter', 'cancelled')"
            class="relative overflow-hidden rounded-2xl p-4 text-left transition-all {{ $statusFilter === 'cancelled' ? 'ring-2 ring-error shadow-ambient' : 'ghost-border hover:shadow-ambient' }} bg-surface-container-lowest">
            <div class="flex items-center gap-2.5 mb-2">
                <div class="w-9 h-9 rounded-xl bg-error-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-error-container text-lg">cancel</span>
                </div>
                <span class="text-2xl font-headline font-bold text-on-surface">{{ $counts->cancelled ?? 0 }}</span>
            </div>
            <p class="text-xs font-medium text-on-surface-variant">Cancelled</p>
        </button>
    </div>

    {{-- Search + Sub-Filters Bar --}}
    <div class="bg-surface-container-lowest rounded-2xl ghost-border p-3 sm:p-4 mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <span class="material-symbols-outlined text-on-surface-variant/50 absolute left-3 top-1/2 -translate-y-1/2 text-lg">search</span>
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Search by booking number or service..."
                    class="w-full bg-surface-container border-0 rounded-xl pl-10 pr-4 py-2.5 text-sm text-on-surface placeholder-on-surface-variant/50 focus:ring-2 focus:ring-primary/20 transition-all">
            </div>

            {{-- Status pills (granular, scrollable on mobile) --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 sm:pb-0 -mx-1 px-1 no-scrollbar">
                @php
                    $filters = [
                        '' => ['label' => 'All', 'icon' => 'list'],
                        'pending' => ['label' => 'Pending', 'icon' => 'hourglass_empty', 'count' => $counts->pending ?? 0],
                        'confirmed' => ['label' => 'Confirmed', 'icon' => 'verified', 'count' => $counts->confirmed ?? 0],
                        'assigned' => ['label' => 'Assigned', 'icon' => 'person_check', 'count' => $counts->assigned ?? 0],
                        'in_progress' => ['label' => 'Active', 'icon' => 'autorenew', 'count' => $counts->in_progress ?? 0],
                        'completed' => ['label' => 'Done', 'icon' => 'check_circle', 'count' => $counts->completed ?? 0],
                        'cancelled' => ['label' => 'Cancelled', 'icon' => 'cancel', 'count' => $counts->cancelled ?? 0],
                    ];
                @endphp

                @foreach($filters as $key => $filter)
                    <button wire:click="$set('statusFilter', '{{ $key }}')"
                        class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium transition-all whitespace-nowrap
                        {{ $statusFilter === $key
                            ? 'cta-gradient text-on-primary shadow-sm'
                            : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                        <span class="material-symbols-outlined text-sm">{{ $filter['icon'] }}</span>
                        {{ $filter['label'] }}
                        @if(isset($filter['count']) && $filter['count'] > 0)
                            <span class="ml-0.5 text-[10px] font-bold {{ $statusFilter === $key ? 'text-on-primary/80' : 'text-on-surface-variant/60' }}">{{ $filter['count'] }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bookings List --}}
    @if($bookings->count())
        <div class="space-y-3 sm:space-y-4">
            @foreach($bookings as $booking)
                @php
                    $serviceName = $booking->package?->name ?? $booking->service?->name ?? 'Custom Booking';
                    $isPackage = (bool) $booking->package_id;
                    $assignedDaimaa = $booking->assignments->firstWhere('accepted_at', '!=', null)?->daimaa;
                    $isUpcoming = in_array($booking->status, ['pending', 'confirmed', 'assigned']) && $booking->scheduled_date->isFuture();
                    $canCancel = in_array($booking->status, ['pending', 'confirmed']);
                    $hasReview = (bool) $booking->review;

                    $statusConfig = match($booking->status) {
                        'pending' => ['bg' => 'bg-tertiary-fixed/30', 'text' => 'text-tertiary', 'icon' => 'hourglass_empty', 'label' => 'Pending'],
                        'confirmed' => ['bg' => 'bg-primary-fixed/40', 'text' => 'text-primary', 'icon' => 'verified', 'label' => 'Confirmed'],
                        'assigned' => ['bg' => 'bg-primary-fixed/40', 'text' => 'text-primary', 'icon' => 'person_check', 'label' => 'Assigned'],
                        'in_progress' => ['bg' => 'bg-tertiary-fixed/30', 'text' => 'text-tertiary', 'icon' => 'autorenew', 'label' => 'In Progress'],
                        'completed' => ['bg' => 'bg-secondary-container/60', 'text' => 'text-secondary', 'icon' => 'check_circle', 'label' => 'Completed'],
                        'cancelled' => ['bg' => 'bg-error-container/50', 'text' => 'text-error', 'icon' => 'cancel', 'label' => 'Cancelled'],
                        'refunded' => ['bg' => 'bg-error-container/50', 'text' => 'text-error', 'icon' => 'currency_exchange', 'label' => 'Refunded'],
                        default => ['bg' => 'bg-surface-container', 'text' => 'text-on-surface-variant', 'icon' => 'help', 'label' => ucfirst($booking->status)],
                    };
                @endphp

                <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden hover:shadow-ambient transition-shadow group">
                    {{-- Mobile: compact card layout --}}
                    <div class="sm:hidden">
                        <a href="{{ route('customer.bookings.show', $booking->id) }}" class="block p-4">
                            {{-- Header row --}}
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl {{ $statusConfig['bg'] }} flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined {{ $statusConfig['text'] }} text-lg">{{ $statusConfig['icon'] }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-semibold text-on-surface truncate">{{ $serviceName }}</h3>
                                        <p class="text-[11px] text-on-surface-variant/60 mt-0.5">{{ $booking->booking_number }}</p>
                                    </div>
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </div>

                            {{-- Info chips --}}
                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-on-surface-variant mb-3">
                                <span class="inline-flex items-center gap-1 bg-surface-container rounded-lg px-2 py-1">
                                    <span class="material-symbols-outlined text-xs">calendar_today</span>
                                    {{ $booking->scheduled_date->format('M d, Y') }}
                                </span>
                                @if($booking->scheduled_time)
                                    <span class="inline-flex items-center gap-1 bg-surface-container rounded-lg px-2 py-1">
                                        <span class="material-symbols-outlined text-xs">schedule</span>
                                        {{ \Carbon\Carbon::parse($booking->scheduled_time)->format('g:i A') }}
                                    </span>
                                @endif
                                @if($booking->is_instant)
                                    <span class="inline-flex items-center gap-1 bg-tertiary/20 text-tertiary rounded-lg px-2 py-1 font-medium">
                                        <span class="material-symbols-outlined text-xs">bolt</span>
                                        Instant
                                    </span>
                                    @endif
                                    @if($isPackage)
                                    <span class="inline-flex items-center gap-1 bg-primary-fixed/20 text-primary rounded-lg px-2 py-1 font-medium">
                                        <span class="material-symbols-outlined text-xs">inventory_2</span>
                                        Package
                                    </span>
                                @elseif($booking->booked_hours)
                                    <span class="inline-flex items-center gap-1 bg-tertiary-fixed/20 text-tertiary rounded-lg px-2 py-1 font-medium">
                                        <span class="material-symbols-outlined text-xs">timer</span>
                                        {{ $booking->booked_hours == floor($booking->booked_hours) ? number_format($booking->booked_hours, 0) : number_format($booking->booked_hours, 1) }}h
                                    </span>
                                @endif
                            </div>

                            {{-- Bottom row: price + daimaa --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($assignedDaimaa)
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-6 h-6 rounded-full bg-primary-fixed-dim flex items-center justify-center">
                                                <span class="text-[10px] font-bold text-on-primary-fixed">{{ strtoupper(substr($assignedDaimaa->name, 0, 1)) }}</span>
                                            </div>
                                            <span class="text-xs text-on-surface-variant">{{ $assignedDaimaa->name }}</span>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-base font-bold text-primary">₹{{ number_format($booking->total_amount) }}</span>
                            </div>
                        </a>

                        {{-- Mobile action bar --}}
                        @if($canCancel)
                            <div class="border-t border-[rgba(218,193,186,0.15)] px-4 py-2.5 flex gap-2">
                                <a href="{{ route('customer.bookings.show', $booking->id) }}" class="flex-1 text-center text-xs font-semibold text-primary py-1.5 rounded-lg bg-primary-fixed/20 transition-colors">
                                    View Details
                                </a>
                                <button wire:click="cancelBooking({{ $booking->id }})" wire:confirm="Are you sure you want to cancel this booking?"
                                    class="text-xs font-medium text-error py-1.5 px-3 rounded-lg bg-error-container/20 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Desktop: rich card layout --}}
                    <div class="hidden sm:block">
                        <div class="flex">
                            {{-- Left date accent --}}
                            <div class="w-20 shrink-0 flex flex-col items-center justify-center py-5 {{ $isUpcoming ? 'cta-gradient' : 'bg-surface-container' }}">
                                <span class="text-[10px] font-semibold uppercase {{ $isUpcoming ? 'text-on-primary/70' : 'text-on-surface-variant/60' }}">
                                    {{ $booking->scheduled_date->format('M') }}
                                </span>
                                <span class="text-2xl font-headline font-bold {{ $isUpcoming ? 'text-on-primary' : 'text-on-surface' }} leading-none">
                                    {{ $booking->scheduled_date->format('d') }}
                                </span>
                                <span class="text-[10px] font-medium {{ $isUpcoming ? 'text-on-primary/70' : 'text-on-surface-variant/60' }}">
                                    {{ $booking->scheduled_date->format('D') }}
                                </span>
                            </div>

                            {{-- Main content --}}
                            <div class="flex-1 p-5">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-base font-semibold text-on-surface">{{ $serviceName }}</h3>
                                            @if($booking->is_instant)
                                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/20 text-tertiary">
                                                    <span class="material-symbols-outlined text-xs">bolt</span> Instant
                                                </span>
                                            @endif
                                            @if($isPackage)
                                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-fixed/30 text-primary">
                                                    <span class="material-symbols-outlined text-xs">inventory_2</span> Package
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-on-surface-variant/60">{{ $booking->booking_number }}</p>
                                    </div>

                                    <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1">{{ $statusConfig['icon'] }}</span>
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>

                                {{-- Info row --}}
                                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-on-surface-variant mb-4">
                                    @if($booking->scheduled_time)
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-base text-primary/70">schedule</span>
                                            {{ \Carbon\Carbon::parse($booking->scheduled_time)->format('g:i A') }}
                                        </span>
                                    @endif
                                    @if($booking->booked_hours)
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-base text-primary/70">timer</span>
                                            {{ $booking->booked_hours == floor($booking->booked_hours) ? number_format($booking->booked_hours, 0) : number_format($booking->booked_hours, 1) }} hours
                                        </span>
                                    @endif
                                    @if($assignedDaimaa)
                                        <span class="flex items-center gap-1.5">
                                            <div class="w-5 h-5 rounded-full bg-primary-fixed-dim flex items-center justify-center">
                                                <span class="text-[9px] font-bold text-on-primary-fixed">{{ strtoupper(substr($assignedDaimaa->name, 0, 1)) }}</span>
                                            </div>
                                            {{ $assignedDaimaa->name }}
                                        </span>
                                    @endif
                                    @if($booking->address)
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-base text-primary/70">location_on</span>
                                            <span class="truncate max-w-[180px]">{{ $booking->address->address_line_1 }}</span>
                                        </span>
                                    @endif
                                    @if($hasReview)
                                        <span class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="material-symbols-outlined text-xs {{ $i <= $booking->review->rating ? 'text-tertiary' : 'text-surface-dim' }}" style="font-variation-settings: 'FILL' 1">star</span>
                                            @endfor
                                        </span>
                                    @endif
                                </div>

                                {{-- Bottom row: price + actions --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-primary">₹{{ number_format($booking->total_amount) }}</span>

                                    <div class="flex items-center gap-2">
                                        @if($booking->status === 'completed' && !$hasReview)
                                            <a href="{{ route('customer.reviews') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-tertiary bg-tertiary-fixed/20 rounded-full hover:bg-tertiary-fixed/30 transition-colors">
                                                <span class="material-symbols-outlined text-sm">rate_review</span> Review
                                            </a>
                                        @endif
                                        @if($canCancel)
                                            <button wire:click="cancelBooking({{ $booking->id }})" wire:confirm="Are you sure you want to cancel this booking?"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-error ghost-border rounded-full hover:bg-error-container/20 transition-colors">
                                                <span class="material-symbols-outlined text-sm">close</span> Cancel
                                            </button>
                                        @endif
                                        <a href="{{ route('customer.bookings.show', $booking->id) }}"
                                            class="inline-flex items-center gap-1 px-4 py-1.5 text-xs font-semibold text-primary bg-primary-fixed/30 rounded-full hover:bg-primary-fixed/50 transition-colors group-hover:bg-primary group-hover:text-on-primary">
                                            View Details
                                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $bookings->links() }}</div>

    @else
        {{-- Empty state --}}
        <div class="text-center py-16 sm:py-20 bg-surface-container-lowest rounded-2xl ghost-border">
            <div class="w-20 h-20 rounded-full bg-primary-fixed/30 flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-primary text-4xl">
                    {{ $search || $statusFilter ? 'search_off' : 'spa' }}
                </span>
            </div>
            @if($search || $statusFilter)
                <h3 class="text-lg font-headline font-semibold text-on-surface mb-1">No bookings found</h3>
                <p class="text-sm text-on-surface-variant mb-5 max-w-xs mx-auto">
                    Try adjusting your search or filter to find what you're looking for.
                </p>
                <button wire:click="$set('statusFilter', ''); $set('search', '')"
                    class="btn-outline text-sm px-6 py-2.5 inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">filter_list_off</span>
                    Clear Filters
                </button>
            @else
                <h3 class="text-lg font-headline font-semibold text-on-surface mb-1">No bookings yet</h3>
                <p class="text-sm text-on-surface-variant mb-5 max-w-xs mx-auto">
                    Start your care journey with Daimaa. Book your first session today.
                </p>
                <a href="{{ route('customer.book') }}" class="btn-primary text-sm px-6 py-3 inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    Book Your First Session
                </a>
            @endif
        </div>
    @endif

    {{-- Mobile FAB for new booking --}}
    @if($bookings->count())
        <a href="{{ route('customer.book') }}"
            class="sm:hidden fixed bottom-6 right-6 z-40 w-14 h-14 cta-gradient rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow">
            <span class="material-symbols-outlined text-on-primary text-2xl">add</span>
        </a>
    @endif
</div>
