<div>
    {{-- Filter tabs --}}
    <div class="flex gap-2 mb-6 overflow-x-auto no-scrollbar pb-1">
        @foreach(['upcoming' => ['label' => 'Upcoming', 'icon' => 'event'], 'started' => ['label' => 'In Progress', 'icon' => 'autorenew'], 'completed' => ['label' => 'Done', 'icon' => 'check_circle'], 'all' => ['label' => 'All', 'icon' => 'list']] as $key => $meta)
            <button wire:click="$set('filter', '{{ $key }}')"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-base font-semibold transition-all
                    {{ $filter === $key ? 'cta-gradient text-on-primary shadow-sm' : 'bg-surface-container-lowest ghost-border text-on-surface-variant hover:bg-surface-container' }}">
                <span class="material-symbols-outlined text-xl" @if($filter === $key) style="font-variation-settings: 'FILL' 1" @endif>{{ $meta['icon'] }}</span>
                {{ $meta['label'] }}
            </button>
        @endforeach
    </div>

    {{-- Flash error --}}
    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 px-5 py-4 bg-error-container/60 border border-error/15 rounded-2xl">
            <span class="material-symbols-outlined text-error text-2xl">warning</span>
            <p class="text-base font-semibold text-error">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Session cards --}}
    <div class="space-y-4">
        @forelse($sessions as $session)
            @php
                $sessionStatusConfig = match($session->status) {
                    'upcoming', 'scheduled' => ['bg' => 'bg-tertiary-fixed/30', 'text' => 'text-tertiary', 'icon' => 'event', 'label' => 'Upcoming', 'border' => 'border-tertiary/10'],
                    'started' => ['bg' => 'bg-primary-fixed/40', 'text' => 'text-primary', 'icon' => 'autorenew', 'label' => 'In Progress', 'border' => 'border-primary/15'],
                    'completed' => ['bg' => 'bg-secondary-container/60', 'text' => 'text-secondary', 'icon' => 'check_circle', 'label' => 'Completed', 'border' => 'border-secondary/10'],
                    'cancelled' => ['bg' => 'bg-error-container/50', 'text' => 'text-error', 'icon' => 'cancel', 'label' => 'Cancelled', 'border' => 'border-error/10'],
                    default => ['bg' => 'bg-surface-container', 'text' => 'text-on-surface-variant', 'icon' => 'circle', 'label' => ucfirst($session->status), 'border' => 'border-transparent'],
                };
                $serviceName = $session->service?->name ?? $session->booking?->package?->name ?? $session->booking?->service?->name ?? 'Visit';
                $durationMinutes = $session->sessionDurationMinutes();
            @endphp

            <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden border {{ $sessionStatusConfig['border'] }}" wire:key="session-{{ $session->id }}">
                <div class="p-5 sm:p-6">
                    {{-- Header row --}}
                    <div class="flex items-start gap-4 mb-4">
                        <div class="shrink-0">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl {{ $sessionStatusConfig['bg'] }} flex flex-col items-center justify-center">
                                @if($session->scheduled_at)
                                    <span class="text-lg sm:text-xl font-headline font-bold {{ $sessionStatusConfig['text'] }}">{{ $session->scheduled_at->format('g:i') }}</span>
                                    <span class="text-xs font-semibold {{ $sessionStatusConfig['text'] }}/70">{{ $session->scheduled_at->format('A') }}</span>
                                @else
                                    <span class="material-symbols-outlined text-2xl {{ $sessionStatusConfig['text'] }}">schedule</span>
                                    <span class="text-[10px] font-medium {{ $sessionStatusConfig['text'] }}/70">TBD</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="text-lg font-bold text-on-surface leading-tight">{{ $serviceName }}</h3>
                                <span class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold {{ $sessionStatusConfig['bg'] }} {{ $sessionStatusConfig['text'] }}">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1">{{ $sessionStatusConfig['icon'] }}</span>
                                    {{ $sessionStatusConfig['label'] }}
                                </span>
                            </div>
                            <p class="text-sm text-on-surface-variant">
                                Session #{{ $session->session_number }} &middot; {{ $session->booking?->booking_number }}
                            </p>
                            @if($session->scheduled_at)
                                <p class="text-sm text-on-surface-variant mt-1 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base text-primary">calendar_today</span>
                                    {{ $session->scheduled_at->format('D, M d, Y') }}
                                </p>
                            @endif
                            <p class="text-xs text-on-surface-variant/60 mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">timer</span>
                                {{ $durationMinutes }} minutes
                            </p>
                        </div>
                    </div>

                    {{-- Customer and address --}}
                    <div class="bg-surface-container rounded-2xl p-4 mb-4">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary text-xl">person</span>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-on-surface">{{ $session->booking?->customer?->name ?? 'Customer' }}</p>
                                    @if($session->booking?->customer?->phone)
                                        <p class="text-sm text-on-surface-variant">{{ $session->booking->customer->phone }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($session->booking?->customer?->phone)
                                <a href="tel:{{ $session->booking->customer->phone }}"
                                    class="shrink-0 w-12 h-12 rounded-2xl bg-primary flex items-center justify-center shadow-md hover:shadow-lg transition-shadow">
                                    <span class="material-symbols-outlined text-on-primary text-xl">call</span>
                                </a>
                            @endif
                        </div>

                        @if($session->booking?->address)
                            <div class="flex items-start gap-2 mt-3 pt-3 border-t border-outline-variant/10">
                                <span class="material-symbols-outlined text-primary text-lg mt-0.5 shrink-0">location_on</span>
                                <div class="flex-1 text-sm text-on-surface-variant">
                                    <span class="font-medium text-on-surface">{{ $session->booking->address->address_line_1 }}</span>
                                    @if($session->booking->address->address_line_2)
                                        <br>{{ $session->booking->address->address_line_2 }}
                                    @endif
                                    <br>{{ $session->booking->address->city?->name ?? '' }} — {{ $session->booking->address->pincode }}
                                </div>
                                {{-- Google Maps navigate button --}}
                                @php
                                    $addr = $session->booking->address;
                                    $mapQuery = urlencode(trim($addr->address_line_1 . ', ' . ($addr->address_line_2 ? $addr->address_line_2 . ', ' : '') . ($addr->city?->name ? $addr->city->name . ', ' : '') . $addr->pincode));
                                @endphp
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $mapQuery }}"
                                    target="_blank" rel="noopener"
                                    class="shrink-0 w-12 h-12 rounded-2xl bg-tertiary flex items-center justify-center shadow-md hover:shadow-lg transition-shadow">
                                    <span class="material-symbols-outlined text-on-tertiary text-xl">directions</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Action area --}}
                    @if(in_array($session->status, ['upcoming', 'scheduled']))
                        @if($hasActiveSession)
                            <div class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-surface-container-high text-on-surface-variant/50 rounded-2xl text-base font-bold cursor-not-allowed">
                                <span class="material-symbols-outlined text-2xl">block</span>
                                Pehle chalu session khatam karein
                            </div>
                        @else
                            <button wire:click="openOtpModal({{ $session->id }})"
                                class="w-full flex items-center justify-center gap-3 px-6 py-4 cta-gradient text-on-primary rounded-2xl text-base font-bold shadow-md hover:shadow-lg transition-shadow">
                                <span class="material-symbols-outlined text-2xl">play_arrow</span>
                                Start Session
                            </button>
                        @endif

                    @elseif($session->status === 'started')
                        {{-- Countdown timer with circular progress --}}
                        <div x-data="sessionTimer({{ $session->id }}, '{{ $session->started_at->toIso8601String() }}', {{ $durationMinutes }})"
                             x-init="startTimer()" class="space-y-4">

                            {{-- Circular progress ring --}}
                            <div class="flex flex-col items-center py-4">
                                <div class="relative w-44 h-44 sm:w-52 sm:h-52">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 200 200">
                                        <circle cx="100" cy="100" r="88" fill="none" stroke="currentColor"
                                            class="text-surface-container-high" stroke-width="12" />
                                        <circle cx="100" cy="100" r="88" fill="none"
                                            :stroke="progressColor"
                                            stroke-width="12"
                                            stroke-linecap="round"
                                            :stroke-dasharray="circumference"
                                            :stroke-dashoffset="dashOffset"
                                            style="transition: stroke-dashoffset 1s linear, stroke 5s ease;" />
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-3xl sm:text-4xl font-headline font-bold text-on-surface" x-text="display"></span>
                                        <span class="text-sm text-on-surface-variant mt-1" x-text="isOvertime ? 'Overtime' : 'remaining'"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Complete button only visible after time is up --}}
                            <template x-if="isOvertime">
                                <button wire:click="markCompleted({{ $session->id }})"
                                    wire:confirm="Session khatam karein?"
                                    class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-secondary text-on-secondary rounded-2xl text-base font-bold shadow-md hover:shadow-lg transition-shadow animate-pulse">
                                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                                    Session Khatam Karein
                                </button>
                            </template>

                            {{-- Locked message while timer is running --}}
                            <template x-if="!isOvertime">
                                <div class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-surface-container-high text-on-surface-variant/50 rounded-2xl text-base font-bold">
                                    <span class="material-symbols-outlined text-2xl">lock_clock</span>
                                    <span>Session chalu hai…</span>
                                </div>
                            </template>
                        </div>

                    @elseif($session->status === 'completed')
                        <div class="flex items-center justify-center gap-2 px-4 py-3 bg-secondary-container/30 rounded-2xl">
                            <span class="material-symbols-outlined text-secondary text-xl" style="font-variation-settings: 'FILL' 1">check_circle</span>
                            <span class="text-base font-semibold text-secondary">Session Completed</span>
                            @if($session->completed_at)
                                <span class="text-sm text-on-surface-variant/60 ml-1">&middot; {{ $session->completed_at->format('M d, g:i A') }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-surface-container-lowest rounded-2xl ghost-border">
                <div class="w-20 h-20 rounded-full bg-surface-container flex items-center justify-center mx-auto mb-5">
                    <span class="material-symbols-outlined text-on-surface-variant/30 text-4xl">event_busy</span>
                </div>
                <p class="text-lg text-on-surface font-semibold mb-1">
                    No {{ $filter === 'all' ? '' : ($filter === 'started' ? 'in-progress' : $filter) }} visits
                </p>
                <p class="text-base text-on-surface-variant">
                    @if($filter === 'upcoming')
                        You do not have any upcoming visits right now.
                    @elseif($filter === 'started')
                        No sessions in progress.
                    @elseif($filter === 'completed')
                        Completed visits will appear here.
                    @else
                        No visits found.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $sessions->links() }}</div>

    {{-- OTP Verification Modal --}}
    @if($otpSessionId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-on-surface/40 px-4"
             x-data="otpForm()"
             x-init="$nextTick(() => focusFirst())"
             wire:click.self="closeOtpModal">
            <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 max-w-sm w-full ambient-shadow" @click.stop>
                {{-- Header --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-primary-fixed flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-primary text-3xl">key</span>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-on-surface mb-1">Enter OTP</h3>
                    <p class="text-base text-on-surface-variant leading-relaxed">
                        Customer se 6-digit code lein aur neeche enter karein
                    </p>
                </div>

                {{-- 6-digit OTP input --}}
                <div class="flex items-center justify-center gap-2 sm:gap-3 mb-5">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" maxlength="1"
                            id="otp-{{ $i }}"
                            class="w-11 h-14 sm:w-12 sm:h-16 text-center text-2xl font-headline font-bold rounded-xl bg-surface-container border-2 border-outline-variant/20 text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/30 transition-all appearance-none"
                            x-model="digits[{{ $i }}]"
                            @input="handleInput($event, {{ $i }})"
                            @keydown.backspace="handleBackspace($event, {{ $i }})"
                            @paste.prevent="handlePaste($event)" />
                    @endfor
                </div>

                {{-- Error --}}
                @if($otpError)
                    <div class="flex items-center gap-2 bg-error-container/30 border border-error/15 rounded-xl px-4 py-3 mb-4">
                        <span class="material-symbols-outlined text-error text-lg">error</span>
                        <p class="text-sm font-medium text-error">{{ $otpError }}</p>
                    </div>
                @endif

                {{-- Buttons --}}
                <div class="space-y-3">
                    <button @click="submit()"
                        :disabled="digits.join('').length < 6"
                        :class="digits.join('').length < 6 ? 'opacity-50 cursor-not-allowed' : ''"
                        class="w-full flex items-center justify-center gap-3 px-6 py-4 cta-gradient text-on-primary rounded-2xl text-base font-bold shadow-md hover:shadow-lg transition-shadow">
                        <span wire:loading.remove wire:target="verifyAndStart">
                            <span class="material-symbols-outlined text-xl">verified</span>
                        </span>
                        <span wire:loading wire:target="verifyAndStart" class="material-symbols-outlined text-xl animate-spin">autorenew</span>
                        <span wire:loading.remove wire:target="verifyAndStart">Verify & Start</span>
                        <span wire:loading wire:target="verifyAndStart">Checking...</span>
                    </button>
                    <button wire:click="closeOtpModal"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-surface-container hover:bg-surface-container-high rounded-2xl text-base font-medium text-on-surface-variant transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
Alpine.data('otpForm', () => ({
    digits: ['', '', '', '', '', ''],

    focusFirst() {
        const el = document.getElementById('otp-0');
        if (el) el.focus();
    },

    handleInput(event, index) {
        const val = event.target.value.replace(/\D/g, '');
        this.digits[index] = val.charAt(0) || '';
        if (val && index < 5) {
            const next = document.getElementById('otp-' + (index + 1));
            if (next) next.focus();
        }
    },

    handleBackspace(event, index) {
        if (!this.digits[index] && index > 0) {
            const prev = document.getElementById('otp-' + (index - 1));
            if (prev) {
                this.digits[index - 1] = '';
                prev.focus();
            }
        }
    },

    handlePaste(event) {
        const text = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
        for (let i = 0; i < 6; i++) {
            this.digits[i] = text.charAt(i) || '';
        }
        const lastFilled = Math.min(text.length, 5);
        const el = document.getElementById('otp-' + lastFilled);
        if (el) el.focus();
    },

    submit() {
        const code = this.digits.join('');
        if (code.length === 6) {
            $wire.set('otpInput', code);
            $wire.verifyAndStart();
        }
    },
}));

Alpine.data('sessionTimer', (sessionId, startedAtIso, durationMinutes) => ({
    totalSeconds: durationMinutes * 60,
    remaining: 0,
    display: '00:00',
    isOvertime: false,
    circumference: 2 * Math.PI * 88,
    dashOffset: 0,
    progressColor: '#4caf50',
    spoken15: false,
    spokenEnd: false,
    intervalId: null,

    startTimer() {
        this.tick();
        this.intervalId = setInterval(() => this.tick(), 1000);
    },

    tick() {
        const startedAt = new Date(startedAtIso).getTime();
        const elapsed = Math.floor((Date.now() - startedAt) / 1000);
        this.remaining = Math.max(0, this.totalSeconds - elapsed);
        this.isOvertime = elapsed >= this.totalSeconds;

        const displaySeconds = this.isOvertime ? elapsed - this.totalSeconds : this.remaining;
        const mins = Math.floor(displaySeconds / 60);
        const secs = displaySeconds % 60;
        this.display = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

        const fraction = this.totalSeconds > 0 ? this.remaining / this.totalSeconds : 0;
        this.dashOffset = this.circumference * (1 - fraction);

        if (fraction > 0.25) {
            this.progressColor = '#4caf50';
        } else if (fraction > 0.10) {
            this.progressColor = '#ff9800';
        } else {
            this.progressColor = '#f44336';
        }

        if (!this.spoken15 && this.remaining <= 900 && this.remaining > 0 && !this.isOvertime) {
            this.spoken15 = true;
            this.speakHindi('Aapka session khatam hone mein pandrah minute baki hain');
        }

        if (!this.spokenEnd && this.remaining <= 0) {
            this.spokenEnd = true;
            this.speakHindi('Aapka session ka samay khatam ho gaya hai, dhanyavaad');
            setTimeout(() => {
                $wire.markCompleted(sessionId);
            }, 3000);
            clearInterval(this.intervalId);
        }
    },

    speakHindi(text) {
        if (!('speechSynthesis' in window)) return;
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'hi-IN';
        utterance.rate = 0.9;
        utterance.pitch = 1;
        utterance.volume = 1;
        window.speechSynthesis.speak(utterance);
    },

    destroy() {
        if (this.intervalId) clearInterval(this.intervalId);
    }
}));
</script>
@endscript
