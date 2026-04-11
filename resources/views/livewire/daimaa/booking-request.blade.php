<div>
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-5 py-4 bg-secondary-container/50 border border-secondary/15 rounded-2xl">
            <span class="material-symbols-outlined text-secondary text-2xl" style="font-variation-settings: 'FILL' 1">check_circle</span>
            <p class="text-base font-semibold text-secondary">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 px-5 py-4 bg-error-container/50 border border-error/15 rounded-2xl">
            <span class="material-symbols-outlined text-error text-2xl">warning</span>
            <p class="text-base font-semibold text-error">{{ session('error') }}</p>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 flex items-center gap-3 px-5 py-4 bg-tertiary-fixed/20 border border-tertiary/15 rounded-2xl">
            <span class="material-symbols-outlined text-tertiary text-2xl">info</span>
            <p class="text-base font-semibold text-tertiary">{{ session('info') }}</p>
        </div>
    @endif

    @if($pendingRequests->isNotEmpty())
        <div class="space-y-4">
            @foreach($pendingRequests as $request)
                @php
                    $booking = $request->booking;
                    $serviceName = $booking?->service?->name ?? $booking?->package?->name ?? 'Service';
                    $area = $booking?->address?->city?->name ?? 'Unknown area';
                    $pincode = $booking?->address?->pincode ?? '';
                    $isInstant = $booking?->is_instant;
                @endphp

                <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden border-2 border-primary/30 shadow-lg"
                     x-data="requestTimer('{{ $request->expires_at?->toIso8601String() }}')" x-init="start()">

                    {{-- Urgent header --}}
                    <div class="px-5 py-4 cta-gradient text-on-primary flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-3xl animate-pulse">notifications_active</span>
                            <div>
                                <h3 class="text-lg font-headline font-bold">Naya Booking Request!</h3>
                                @if($isInstant)
                                    <span class="text-sm font-semibold opacity-90">Instant Booking</span>
                                @endif
                            </div>
                        </div>
                        {{-- Countdown --}}
                        <div class="text-center">
                            <span class="text-2xl font-headline font-bold tabular-nums" x-text="display"></span>
                            <p class="text-xs opacity-80">Baki samay</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Service info --}}
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-primary-fixed/40 flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1">spa</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-headline font-bold text-on-surface">{{ $serviceName }}</h4>
                                <p class="text-base text-on-surface-variant">
                                    <span class="material-symbols-outlined text-sm align-middle">location_on</span>
                                    {{ $area }} {{ $pincode ? "— {$pincode}" : '' }}
                                </p>
                            </div>
                        </div>

                        {{-- Date & Time --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="px-4 py-3 bg-surface-container rounded-xl">
                                <span class="material-symbols-outlined text-primary text-xl mb-1">calendar_today</span>
                                <p class="text-base font-bold text-on-surface">
                                    {{ $booking?->scheduled_date?->format('d M Y') ?? 'TBD' }}
                                </p>
                                <p class="text-sm text-on-surface-variant">Date</p>
                            </div>
                            <div class="px-4 py-3 bg-surface-container rounded-xl">
                                <span class="material-symbols-outlined text-primary text-xl mb-1">schedule</span>
                                <p class="text-base font-bold text-on-surface">
                                    {{ $booking?->scheduled_time ? \Carbon\Carbon::parse($booking->scheduled_time)->format('g:i A') : 'TBD' }}
                                </p>
                                <p class="text-sm text-on-surface-variant">Time</p>
                            </div>
                        </div>

                        {{-- Score badge --}}
                        @if($request->match_score)
                            <div class="flex items-center gap-2 px-4 py-2 bg-tertiary-fixed/15 rounded-xl">
                                <span class="material-symbols-outlined text-tertiary text-lg">grade</span>
                                <span class="text-sm font-semibold text-tertiary">Match Score: {{ number_format($request->match_score, 1) }}/100</span>
                            </div>
                        @endif

                        {{-- Action buttons --}}
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <button wire:click="accept({{ $request->id }})"
                                wire:confirm="Kya aap yeh booking accept karna chahti hain?"
                                class="flex items-center justify-center gap-2 px-6 py-4 cta-gradient text-on-primary rounded-2xl text-lg font-bold shadow-md hover:shadow-lg transition-shadow">
                                <span class="material-symbols-outlined text-2xl">check</span>
                                Haan, Accept
                            </button>
                            <button wire:click="openDeclineModal({{ $request->id }})"
                                class="flex items-center justify-center gap-2 px-6 py-4 bg-surface-container-high text-on-surface rounded-2xl text-lg font-bold ghost-border hover:bg-error-container/30 hover:text-error transition-colors">
                                <span class="material-symbols-outlined text-2xl">close</span>
                                Nahi, Decline
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Decline reason modal --}}
    @if($decliningId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-data>
            <div class="w-full max-w-md bg-surface-container-lowest rounded-3xl shadow-2xl overflow-hidden">
                <div class="px-6 py-5 bg-error-container/30 border-b border-error/10">
                    <h3 class="text-xl font-headline font-bold text-error flex items-center gap-2">
                        <span class="material-symbols-outlined text-2xl">help</span>
                        Decline kyu kar rahi hain?
                    </h3>
                </div>

                <div class="p-6 space-y-4">
                    <p class="text-base text-on-surface-variant">
                        Kripya batayein kyun nahi le sakti — isse hum aapko behtar bookings de payenge.
                    </p>

                    <textarea wire:model="declineReason" rows="3" placeholder="Reason likhe (optional)..."
                        class="w-full px-4 py-3 bg-surface-container rounded-xl border border-outline-variant/20 text-on-surface text-base placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/30 transition-all"></textarea>

                    <p class="text-sm text-error/70 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        Decline karne se aapka reliability score {{ config('daimaa_matching.reliability_penalty_decline', 3) }} points se kam hoga.
                    </p>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <button wire:click="closeDeclineModal"
                            class="flex items-center justify-center gap-2 px-5 py-3.5 bg-surface-container text-on-surface rounded-2xl text-base font-bold ghost-border">
                            <span class="material-symbols-outlined">arrow_back</span>
                            Wapas Jaayein
                        </button>
                        <button wire:click="decline"
                            class="flex items-center justify-center gap-2 px-5 py-3.5 bg-error text-on-error rounded-2xl text-base font-bold shadow-md">
                            <span class="material-symbols-outlined">close</span>
                            Decline Karein
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
Alpine.data('requestTimer', (expiresAtIso) => ({
    display: '--:--',
    intervalId: null,

    start() {
        this.tick();
        this.intervalId = setInterval(() => this.tick(), 1000);
    },

    tick() {
        if (!expiresAtIso) {
            this.display = '--:--';
            return;
        }

        const expiresAt = new Date(expiresAtIso).getTime();
        const remaining = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));

        if (remaining <= 0) {
            this.display = '00:00';
            clearInterval(this.intervalId);
            setTimeout(() => $wire.$refresh(), 2000);
            return;
        }

        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;
        this.display = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    },

    destroy() {
        if (this.intervalId) clearInterval(this.intervalId);
    }
}));
</script>
@endscript
