<div>
    {{-- Back link --}}
    <a href="{{ route('customer.bookings') }}" class="inline-flex items-center gap-1.5 text-sm text-on-surface-variant hover:text-primary transition-colors mb-5 group">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
        <span>Back to Bookings</span>
    </a>

    @if($booking)
    @php
        $serviceName = $booking->package?->name ?? $booking->service?->name ?? 'Custom Booking';
        $isPackage = (bool) $booking->package_id;
        $assignedDaimaa = $booking->assignments->firstWhere('accepted_at', '!=', null)?->daimaa;
        $canCancel = in_array($booking->status, ['pending', 'confirmed']);
        $isCompleted = $booking->status === 'completed';
        $isCancelled = in_array($booking->status, ['cancelled', 'refunded']);

        $statusConfig = match($booking->status) {
            'pending' => ['bg' => 'bg-tertiary-fixed/30', 'text' => 'text-tertiary', 'icon' => 'hourglass_empty', 'label' => 'Pending', 'gradient' => 'from-amber-500/10 to-amber-600/5'],
            'confirmed' => ['bg' => 'bg-primary-fixed/40', 'text' => 'text-primary', 'icon' => 'verified', 'label' => 'Confirmed', 'gradient' => 'from-primary/10 to-primary/5'],
            'assigned' => ['bg' => 'bg-primary-fixed/40', 'text' => 'text-primary', 'icon' => 'person_check', 'label' => 'Assigned', 'gradient' => 'from-primary/10 to-primary/5'],
            'in_progress' => ['bg' => 'bg-tertiary-fixed/30', 'text' => 'text-tertiary', 'icon' => 'autorenew', 'label' => 'In Progress', 'gradient' => 'from-tertiary/10 to-tertiary/5'],
            'completed' => ['bg' => 'bg-secondary-container/60', 'text' => 'text-secondary', 'icon' => 'check_circle', 'label' => 'Completed', 'gradient' => 'from-green-500/10 to-green-600/5'],
            'cancelled' => ['bg' => 'bg-error-container/50', 'text' => 'text-error', 'icon' => 'cancel', 'label' => 'Cancelled', 'gradient' => 'from-error/10 to-error/5'],
            'refunded' => ['bg' => 'bg-error-container/50', 'text' => 'text-error', 'icon' => 'currency_exchange', 'label' => 'Refunded', 'gradient' => 'from-error/10 to-error/5'],
            default => ['bg' => 'bg-surface-container', 'text' => 'text-on-surface-variant', 'icon' => 'help', 'label' => ucfirst($booking->status), 'gradient' => 'from-surface/10 to-surface/5'],
        };

        $statusSteps = ['pending', 'confirmed', 'assigned', 'in_progress', 'completed'];
        $currentStepIndex = $isCancelled ? -1 : array_search($booking->status, $statusSteps);
        if ($currentStepIndex === false) $currentStepIndex = 0;
    @endphp

    {{-- Hero Header Card --}}
    <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden mb-5">
        <div class="relative bg-gradient-to-r {{ $statusConfig['gradient'] }} px-5 sm:px-7 pt-5 sm:pt-6 pb-4">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-on-surface-variant/60 font-mono mb-1">{{ $booking->booking_number }}</p>
                    <h2 class="text-xl sm:text-2xl font-headline font-bold text-on-surface mb-1">{{ $serviceName }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @if($booking->is_instant)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/20 text-tertiary">
                                <span class="material-symbols-outlined text-xs">bolt</span> Instant Booking
                            </span>
                        @endif
                        @if($isPackage)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-fixed/30 text-primary">
                                <span class="material-symbols-outlined text-xs">inventory_2</span> Package
                            </span>
                        @endif
                        @if($booking->booked_hours)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary-fixed/20 text-tertiary">
                                <span class="material-symbols-outlined text-xs">timer</span>
                                {{ $booking->booked_hours == floor($booking->booked_hours) ? number_format($booking->booked_hours, 0) : number_format($booking->booked_hours, 1) }} hours
                            </span>
                        @endif
                    </div>
                </div>

                <span class="shrink-0 self-start inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1">{{ $statusConfig['icon'] }}</span>
                    {{ $statusConfig['label'] }}
                </span>
            </div>
        </div>

        {{-- Progress Stepper (only for non-cancelled bookings) --}}
        @if(!$isCancelled)
            <div class="px-5 sm:px-7 py-4 border-t border-[rgba(218,193,186,0.12)]">
                {{-- Desktop stepper --}}
                <div class="hidden sm:flex items-center justify-between">
                    @foreach($statusSteps as $i => $step)
                        @php
                            $isPast = $i < $currentStepIndex;
                            $isCurrent = $i === $currentStepIndex;
                            $isFuture = $i > $currentStepIndex;
                            $stepLabel = match($step) {
                                'pending' => 'Booked',
                                'confirmed' => 'Confirmed',
                                'assigned' => 'Assigned',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                default => ucfirst($step),
                            };
                            $stepIcon = match($step) {
                                'pending' => 'edit_calendar',
                                'confirmed' => 'verified',
                                'assigned' => 'person_check',
                                'in_progress' => 'autorenew',
                                'completed' => 'check_circle',
                                default => 'circle',
                            };
                        @endphp

                        @if($i > 0)
                            <div class="flex-1 h-0.5 mx-1 rounded-full {{ $isPast || $isCurrent ? 'bg-primary' : 'bg-surface-container-high' }}"></div>
                        @endif

                        <div class="flex flex-col items-center gap-1.5">
                            <div @class([
                                'w-9 h-9 rounded-full flex items-center justify-center transition-all',
                                'bg-primary text-on-primary' => $isPast || $isCurrent,
                                'bg-surface-container-high text-on-surface-variant/40' => $isFuture,
                                'ring-4 ring-primary/20' => $isCurrent,
                            ])>
                                <span class="material-symbols-outlined text-lg" @if($isPast || $isCurrent) style="font-variation-settings: 'FILL' 1" @endif>
                                    {{ $isPast ? 'check' : $stepIcon }}
                                </span>
                            </div>
                            <span @class([
                                'text-[11px] font-medium',
                                'text-primary font-semibold' => $isCurrent,
                                'text-on-surface-variant' => $isPast,
                                'text-on-surface-variant/40' => $isFuture,
                            ])>{{ $stepLabel }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Mobile stepper (compact progress bar) --}}
                <div class="sm:hidden">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold {{ $statusConfig['text'] }}">{{ $statusConfig['label'] }}</span>
                        <span class="text-[11px] text-on-surface-variant/60">Step {{ $currentStepIndex + 1 }} of {{ count($statusSteps) }}</span>
                    </div>
                    <div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
                        <div class="h-full cta-gradient rounded-full transition-all duration-500"
                            style="width: {{ (($currentStepIndex + 1) / count($statusSteps)) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        @elseif($isCancelled)
            <div class="px-5 sm:px-7 py-3 border-t border-[rgba(218,193,186,0.12)] bg-error-container/10">
                <div class="flex items-center gap-2 text-sm text-error">
                    <span class="material-symbols-outlined text-lg">info</span>
                    <span>This booking was cancelled{{ $booking->cancelled_at ? ' on ' . $booking->cancelled_at->format('M d, Y \a\t g:i A') : '' }}.</span>
                </div>
            </div>
        @endif
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Left column: Main details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Booking Info Card --}}
            <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-[rgba(218,193,186,0.12)] flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">info</span>
                    <h3 class="font-semibold text-on-surface">Booking Details</h3>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
                        <div>
                            <p class="text-[11px] font-medium text-on-surface-variant/60 uppercase tracking-wider mb-1">Date</p>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-lg">calendar_today</span>
                                <span class="text-sm font-semibold text-on-surface">{{ $booking->scheduled_date->format('l, M d, Y') }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium text-on-surface-variant/60 uppercase tracking-wider mb-1">Time</p>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-lg">schedule</span>
                                <span class="text-sm font-semibold text-on-surface">{{ $booking->scheduled_time ? \Carbon\Carbon::parse($booking->scheduled_time)->format('g:i A') : 'Flexible' }}</span>
                            </div>
                        </div>
                        @if($booking->address)
                            <div class="sm:col-span-2">
                                <p class="text-[11px] font-medium text-on-surface-variant/60 uppercase tracking-wider mb-1">Address</p>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-primary text-lg mt-0.5">location_on</span>
                                    <div class="text-sm text-on-surface">
                                        <span class="font-semibold">{{ $booking->address->address_line_1 }}</span>
                                        @if($booking->address->address_line_2)
                                            <br><span class="text-on-surface-variant">{{ $booking->address->address_line_2 }}</span>
                                        @endif
                                        <br><span class="text-on-surface-variant">{{ $booking->address->city?->name ?? '' }} — {{ $booking->address->pincode }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($booking->notes)
                            <div class="sm:col-span-2">
                                <p class="text-[11px] font-medium text-on-surface-variant/60 uppercase tracking-wider mb-1">Notes</p>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-primary text-lg mt-0.5">notes</span>
                                    <p class="text-sm text-on-surface-variant">{{ $booking->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Assigned Daimaa Card --}}
            @if($assignedDaimaa)
                <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b border-[rgba(218,193,186,0.12)] flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">person</span>
                        <h3 class="font-semibold text-on-surface">Your Daimaa</h3>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-primary-fixed-dim flex items-center justify-center shrink-0">
                                <span class="text-xl font-bold text-on-primary-fixed">{{ strtoupper(substr($assignedDaimaa->name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-semibold text-on-surface">{{ $assignedDaimaa->name }}</h4>
                                @if($assignedDaimaa->phone)
                                    <p class="text-sm text-on-surface-variant flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-sm">call</span>
                                        {{ $assignedDaimaa->phone }}
                                    </p>
                                @endif
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-primary-fixed/30 text-primary">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1">verified</span>
                                    Verified
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Session Tracker Card --}}
            @if($booking->sessions->count())
                @php
                    $totalSessions = $booking->sessions->count();
                    $completedSessions = $booking->sessions->where('status', 'completed')->count();
                    $scheduledSessions = $booking->sessions->where('status', 'scheduled')->count();
                    $unscheduledSessions = $booking->sessions->where('status', 'upcoming')->count();
                    $progressPercent = $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100) : 0;
                @endphp
                <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b border-[rgba(218,193,186,0.12)]">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-lg">event_repeat</span>
                                <h3 class="font-semibold text-on-surface">Session Tracker</h3>
                            </div>
                            <span class="text-xs font-bold text-primary bg-primary-fixed/30 rounded-full px-2.5 py-0.5">
                                {{ $completedSessions }} / {{ $totalSessions }} done
                            </span>
                        </div>
                        {{-- Progress bar --}}
                        <div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
                            <div class="h-full cta-gradient rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                        </div>
                        {{-- Mini stats --}}
                        <div class="flex items-center gap-4 mt-2.5 text-[11px] text-on-surface-variant/60">
                            @if($scheduledSessions > 0)
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span> {{ $scheduledSessions }} scheduled</span>
                            @endif
                            @if($unscheduledSessions > 0)
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> {{ $unscheduledSessions }} to schedule</span>
                            @endif
                            @if($completedSessions > 0)
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> {{ $completedSessions }} completed</span>
                            @endif
                        </div>
                    </div>

                    <div class="divide-y divide-[rgba(218,193,186,0.12)]">
                        @foreach($booking->sessions->sortBy('session_number') as $session)
                            @php
                                $sessionStatus = match($session->status) {
                                    'completed' => ['bg' => 'bg-secondary-container/60', 'text' => 'text-secondary', 'icon' => 'check_circle', 'label' => 'Completed'],
                                    'scheduled' => ['bg' => 'bg-primary-fixed/30', 'text' => 'text-primary', 'icon' => 'event_available', 'label' => 'Scheduled'],
                                    'started' => ['bg' => 'bg-tertiary-fixed/30', 'text' => 'text-tertiary', 'icon' => 'autorenew', 'label' => 'In Progress'],
                                    'cancelled' => ['bg' => 'bg-error-container/50', 'text' => 'text-error', 'icon' => 'cancel', 'label' => 'Cancelled'],
                                    'no_show' => ['bg' => 'bg-error-container/50', 'text' => 'text-error', 'icon' => 'person_off', 'label' => 'No Show'],
                                    default => ['bg' => 'bg-tertiary-fixed/20', 'text' => 'text-tertiary', 'icon' => 'schedule', 'label' => 'Not Scheduled'],
                                };
                                $isEditing = $scheduleSessionId === $session->id;
                                $canSchedule = in_array($session->status, ['upcoming', 'scheduled']) && !$isCancelled;
                            @endphp

                            <div class="px-5 sm:px-6 py-4" wire:key="session-{{ $session->id }}">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    {{-- Session number badge --}}
                                    <div class="w-10 h-10 rounded-xl {{ $sessionStatus['bg'] }} flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined {{ $sessionStatus['text'] }} text-lg" style="font-variation-settings: 'FILL' 1">{{ $sessionStatus['icon'] }}</span>
                                    </div>

                                    {{-- Session info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="text-sm font-semibold text-on-surface">Session {{ $session->session_number }}</p>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $sessionStatus['bg'] }} {{ $sessionStatus['text'] }}">
                                                        {{ $sessionStatus['label'] }}
                                                    </span>
                                                </div>
                                                @if($session->service)
                                                    <p class="text-xs text-on-surface-variant mt-0.5 flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-xs">spa</span>
                                                        {{ $session->service->name }}
                                                    </p>
                                                @endif
                                            </div>

                                            {{-- Schedule / Reschedule button --}}
                                            @if($canSchedule && !$isEditing)
                                                <button wire:click="scheduleSession({{ $session->id }})"
                                                    class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors
                                                        {{ $session->status === 'upcoming'
                                                            ? 'cta-gradient text-on-primary shadow-sm hover:shadow-md'
                                                            : 'border border-primary/20 text-primary hover:bg-primary-fixed/20' }}">
                                                    <span class="material-symbols-outlined text-sm">{{ $session->status === 'upcoming' ? 'edit_calendar' : 'edit' }}</span>
                                                    {{ $session->status === 'upcoming' ? 'Schedule' : 'Reschedule' }}
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Scheduled date/time display --}}
                                        @if($session->scheduled_at && !$isEditing)
                                            <div class="flex items-center gap-3 mt-2 bg-surface-container rounded-xl px-3 py-2">
                                                <span class="material-symbols-outlined text-primary text-sm">calendar_today</span>
                                                <span class="text-xs font-medium text-on-surface">{{ $session->scheduled_at->format('D, M d, Y') }}</span>
                                                <span class="text-on-surface-variant/30">|</span>
                                                <span class="material-symbols-outlined text-primary text-sm">schedule</span>
                                                <span class="text-xs font-medium text-on-surface">{{ $session->scheduled_at->format('g:i A') }}</span>
                                            </div>
                                        @endif

                                        {{-- Inline scheduling form --}}
                                        @if($isEditing)
                                            <div class="mt-3 p-4 bg-primary-fixed/10 rounded-xl border border-primary/10 animate-fade-in" x-data>
                                                <p class="text-xs font-semibold text-primary mb-3 flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-sm">edit_calendar</span>
                                                    {{ $session->status === 'upcoming' ? 'Pick a date & time' : 'Change date & time' }}
                                                </p>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="text-[11px] font-medium text-on-surface-variant uppercase tracking-wider mb-1 block">Date</label>
                                                        <input type="date" wire:model="sessionDate"
                                                            min="{{ now()->format('Y-m-d') }}"
                                                            class="w-full px-3 py-2.5 bg-surface-container-lowest rounded-xl border border-outline-variant/20 text-sm text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">
                                                        @error('sessionDate') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div>
                                                        <label class="text-[11px] font-medium text-on-surface-variant uppercase tracking-wider mb-1 block">Time</label>
                                                        <select wire:model="sessionTime"
                                                            class="w-full px-3 py-2.5 bg-surface-container-lowest rounded-xl border border-outline-variant/20 text-sm text-on-surface focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors">
                                                            @foreach(['06:00','06:30','07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00'] as $slot)
                                                                <option value="{{ $slot }}">{{ \Carbon\Carbon::parse($slot)->format('g:i A') }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('sessionTime') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 mt-3">
                                                    <button wire:click="saveSessionSchedule"
                                                        class="inline-flex items-center gap-1.5 px-4 py-2 cta-gradient text-on-primary rounded-xl text-xs font-semibold shadow-sm hover:shadow-md transition-shadow">
                                                        <span class="material-symbols-outlined text-sm">check</span>
                                                        Confirm
                                                    </button>
                                                    <button wire:click="cancelSessionSchedule"
                                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-surface-container hover:bg-surface-container-high rounded-xl text-xs font-medium text-on-surface-variant transition-colors">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- OTP code for customer to share with Daimaa --}}
                                        @if($session->start_otp && in_array($session->status, ['upcoming', 'scheduled']))
                                            <div class="mt-3 p-4 bg-tertiary-fixed/15 border border-tertiary/15 rounded-xl">
                                                <p class="text-xs font-semibold text-tertiary mb-2 flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-sm">key</span>
                                                    Share this code with your Daimaa to start the session
                                                </p>
                                                <div class="flex items-center justify-center gap-2">
                                                    @foreach(str_split($session->start_otp) as $digit)
                                                        <span class="w-10 h-12 flex items-center justify-center bg-surface-container-lowest rounded-xl text-xl font-headline font-bold text-on-surface ghost-border">{{ $digit }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Daimaa assignment --}}
                                        @if($session->daimaa)
                                            <p class="text-[11px] text-on-surface-variant/50 mt-2 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs">person</span>
                                                {{ $session->daimaa->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Review Card --}}
            @if($isCompleted)
                <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b border-[rgba(218,193,186,0.12)] flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">rate_review</span>
                        <h3 class="font-semibold text-on-surface">Your Review</h3>
                    </div>
                    <div class="p-5 sm:p-6">
                        @if($booking->review)
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-xl bg-tertiary-fixed flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-tertiary text-xl" style="font-variation-settings: 'FILL' 1">star</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-1 mb-1.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="material-symbols-outlined text-lg {{ $i <= $booking->review->rating ? 'text-tertiary' : 'text-surface-dim' }}" style="font-variation-settings: 'FILL' 1">star</span>
                                        @endfor
                                        <span class="text-xs text-on-surface-variant ml-1.5">{{ $booking->review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($booking->review->comment)
                                        <p class="text-sm text-on-surface-variant leading-relaxed">{{ $booking->review->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        @elseif(!$showReviewForm)
                            <div class="text-center py-4">
                                <div class="w-14 h-14 rounded-2xl bg-tertiary-fixed/30 flex items-center justify-center mx-auto mb-3">
                                    <span class="material-symbols-outlined text-tertiary text-2xl">edit_note</span>
                                </div>
                                <p class="text-sm text-on-surface font-medium mb-1">Share your experience</p>
                                <p class="text-xs text-on-surface-variant mb-4">Your feedback helps other mothers find the best care</p>
                                <button wire:click="$set('showReviewForm', true)" class="btn-primary text-sm px-5 py-2.5 inline-flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base">rate_review</span>
                                    Write a Review
                                </button>
                            </div>
                        @else
                            <div class="space-y-4 animate-fade-in" x-data="{ hoverRating: 0 }">
                                <div>
                                    <p class="text-sm font-medium text-on-surface mb-2">How was your experience?</p>
                                    <div class="flex gap-1.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button wire:click="$set('rating', {{ $i }})"
                                                @mouseenter="hoverRating = {{ $i }}"
                                                @mouseleave="hoverRating = 0"
                                                class="p-1 rounded-lg hover:bg-tertiary-fixed/20 transition-colors">
                                                <span class="material-symbols-outlined text-3xl transition-all
                                                    {{ $i <= $rating ? 'text-tertiary scale-110' : 'text-surface-dim' }}"
                                                    :class="hoverRating >= {{ $i }} ? 'text-tertiary !scale-110' : ''"
                                                    style="font-variation-settings: 'FILL' 1">star</span>
                                            </button>
                                        @endfor
                                    </div>
                                    <p class="text-xs text-on-surface-variant mt-1">
                                        {{ match($rating) {
                                            1 => 'Poor',
                                            2 => 'Fair',
                                            3 => 'Good',
                                            4 => 'Very Good',
                                            5 => 'Excellent',
                                            default => '',
                                        } }}
                                    </p>
                                </div>
                                <div>
                                    <textarea wire:model="reviewComment" class="input-field" rows="4" placeholder="Tell us about your experience..."></textarea>
                                    @error('reviewComment') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex items-center gap-3">
                                    <button wire:click="submitReview" class="btn-primary text-sm px-5 py-2.5 inline-flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">send</span>
                                        Submit Review
                                    </button>
                                    <button wire:click="$set('showReviewForm', false)" class="btn-outline text-sm px-4 py-2.5">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Right column: Sidebar --}}
        <div class="space-y-5">

            {{-- Payment Summary Card --}}
            <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                <div class="px-5 py-4 border-b border-[rgba(218,193,186,0.12)] flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">receipt_long</span>
                    <h3 class="font-semibold text-on-surface">Payment Summary</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-on-surface-variant">Subtotal</span>
                        <span class="font-medium text-on-surface">₹{{ number_format($booking->subtotal) }}</span>
                    </div>
                    @if($booking->discount_amount > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-on-surface-variant flex items-center gap-1">
                                Discount
                                @if($booking->coupon)
                                    <span class="text-[10px] font-bold text-tertiary bg-tertiary-fixed/20 px-1.5 py-0.5 rounded-full">{{ $booking->coupon->code }}</span>
                                @endif
                            </span>
                            <span class="font-medium text-secondary">-₹{{ number_format($booking->discount_amount) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-[rgba(218,193,186,0.12)] pt-3 flex items-center justify-between">
                        <span class="text-sm font-semibold text-on-surface">Total</span>
                        <span class="text-xl font-headline font-bold text-primary">₹{{ number_format($booking->total_amount) }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-surface-container rounded-xl px-3 py-2.5 mt-2">
                        <span class="material-symbols-outlined text-sm text-on-surface-variant/60">info</span>
                        <p class="text-[11px] text-on-surface-variant/60">No upfront payment. Pay after your session.</p>
                    </div>
                </div>
            </div>

            {{-- Timeline Card --}}
            <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                <div class="px-5 py-4 border-b border-[rgba(218,193,186,0.12)] flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">timeline</span>
                    <h3 class="font-semibold text-on-surface">Timeline</h3>
                </div>
                <div class="p-5">
                    @if($booking->statusHistories->count())
                        <div class="space-y-0">
                            @foreach($booking->statusHistories->sortByDesc('created_at') as $history)
                                @php
                                    $historyConfig = match($history->to_status) {
                                        'pending' => ['bg' => 'bg-tertiary', 'icon' => 'edit_calendar'],
                                        'confirmed' => ['bg' => 'bg-primary', 'icon' => 'verified'],
                                        'assigned' => ['bg' => 'bg-primary', 'icon' => 'person_check'],
                                        'in_progress' => ['bg' => 'bg-tertiary', 'icon' => 'autorenew'],
                                        'completed' => ['bg' => 'bg-secondary', 'icon' => 'check_circle'],
                                        'cancelled' => ['bg' => 'bg-error', 'icon' => 'cancel'],
                                        default => ['bg' => 'bg-surface-dim', 'icon' => 'circle'],
                                    };
                                @endphp
                                <div class="flex gap-3 relative">
                                    {{-- Vertical connector --}}
                                    @if(!$loop->last)
                                        <div class="absolute left-[13px] top-8 bottom-0 w-px bg-outline-variant/20"></div>
                                    @endif

                                    {{-- Dot --}}
                                    <div class="relative z-10 flex flex-col items-center">
                                        <div class="w-[26px] h-[26px] rounded-full {{ $historyConfig['bg'] }} flex items-center justify-center {{ $loop->first ? 'ring-4 ring-primary/10' : '' }}">
                                            <span class="material-symbols-outlined text-on-primary text-sm" style="font-variation-settings: 'FILL' 1">{{ $historyConfig['icon'] }}</span>
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="pb-5 flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-on-surface">{{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</p>
                                        <p class="text-[11px] text-on-surface-variant/60 mt-0.5">
                                            {{ $history->created_at->format('M d, Y') }} &middot; {{ $history->created_at->format('g:i A') }}
                                        </p>
                                        @if($history->notes)
                                            <p class="text-xs text-on-surface-variant mt-1 bg-surface-container rounded-lg px-2.5 py-1.5 inline-block">{{ $history->notes }}</p>
                                        @endif
                                        @if($history->changedByUser)
                                            <p class="text-[10px] text-on-surface-variant/40 mt-1">by {{ $history->changedByUser->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-on-surface-variant/60 text-center py-4">No timeline events yet</p>
                    @endif
                </div>
            </div>

            {{-- Actions Card --}}
            @if($canCancel)
                <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                    <div class="px-5 py-4 border-b border-[rgba(218,193,186,0.12)] flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">more_horiz</span>
                        <h3 class="font-semibold text-on-surface">Actions</h3>
                    </div>
                    <div class="p-5">
                        <button wire:click="cancelBooking({{ $booking->id }})" wire:confirm="Are you sure you want to cancel this booking? This action cannot be undone."
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-error rounded-xl border border-error/20 hover:bg-error-container/20 transition-colors">
                            <span class="material-symbols-outlined text-lg">cancel</span>
                            Cancel Booking
                        </button>
                        <p class="text-[11px] text-on-surface-variant/50 text-center mt-2">You can cancel before your Daimaa starts the session.</p>
                    </div>
                </div>
            @endif

            {{-- Quick Links --}}
            <div class="bg-surface-container-lowest rounded-2xl ghost-border p-5">
                <h3 class="text-sm font-semibold text-on-surface mb-3">Need Help?</h3>
                <div class="space-y-2">
                    <a href="{{ route('customer.book') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-container transition-colors group">
                        <span class="material-symbols-outlined text-primary text-lg">add_circle</span>
                        <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors">Book Another Session</span>
                    </a>
                    <a href="{{ route('customer.bookings') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-container transition-colors group">
                        <span class="material-symbols-outlined text-primary text-lg">list_alt</span>
                        <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors">All Bookings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
