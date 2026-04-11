<div>
    {{-- Earnings Overview Cards --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-2xl cta-gradient flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary text-2xl">account_balance_wallet</span>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-headline font-bold text-on-surface">₹{{ number_format($totalEarned) }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Total Earned</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-secondary text-2xl">savings</span>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-headline font-bold text-secondary">₹{{ number_format($balanceDue) }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Balance Due</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-tertiary flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-tertiary text-2xl">calendar_today</span>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-headline font-bold text-on-surface">₹{{ number_format($thisWeekEarned) }}</p>
            <p class="text-sm text-on-surface-variant mt-1">This Week</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl ghost-border p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-primary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-2xl">check_circle</span>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-headline font-bold text-on-surface">{{ $completedSessions }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Sessions Done</p>
        </div>
    </div>

    {{-- Paid / Pending summary --}}
    <div class="flex items-center gap-3 mb-6 p-4 bg-surface-container-lowest rounded-2xl ghost-border">
        <div class="flex-1 text-center">
            <p class="text-lg font-bold text-secondary">₹{{ number_format($totalPaidOut) }}</p>
            <p class="text-xs text-on-surface-variant">Already Paid</p>
        </div>
        <div class="w-px h-10 bg-outline-variant/20"></div>
        <div class="flex-1 text-center">
            <p class="text-lg font-bold text-tertiary">₹{{ number_format($pendingPayout) }}</p>
            <p class="text-xs text-on-surface-variant">Pending Payout</p>
        </div>
        <div class="w-px h-10 bg-outline-variant/20"></div>
        <div class="flex-1 text-center">
            <p class="text-lg font-bold text-on-surface">₹{{ number_format($thisMonthEarned) }}</p>
            <p class="text-xs text-on-surface-variant">This Month</p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex gap-2 mb-6 overflow-x-auto no-scrollbar pb-1">
        @foreach(['overview' => ['label' => 'Weekly', 'icon' => 'bar_chart'], 'sessions' => ['label' => 'Sessions', 'icon' => 'receipt_long'], 'payouts' => ['label' => 'Payouts', 'icon' => 'payments']] as $key => $meta)
            <button wire:click="$set('tab', '{{ $key }}')"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-base font-semibold transition-all
                    {{ $tab === $key ? 'cta-gradient text-on-primary shadow-sm' : 'bg-surface-container-lowest ghost-border text-on-surface-variant hover:bg-surface-container' }}">
                <span class="material-symbols-outlined text-xl" @if($tab === $key) style="font-variation-settings: 'FILL' 1" @endif>{{ $meta['icon'] }}</span>
                {{ $meta['label'] }}
            </button>
        @endforeach
    </div>

    {{-- TAB: Weekly Breakdown --}}
    @if($tab === 'overview')
        <div class="space-y-3">
            <h3 class="text-lg font-headline font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">insights</span>
                Weekly Earnings
            </h3>

            @foreach($weeklyBreakdown as $week)
                <div class="bg-surface-container-lowest rounded-2xl ghost-border p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-on-surface">{{ $week['label'] }}</p>
                        <p class="text-lg font-headline font-bold {{ $week['amount'] > 0 ? 'text-primary' : 'text-on-surface-variant/40' }}">
                            ₹{{ number_format($week['amount']) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-2.5 bg-surface-container rounded-full overflow-hidden">
                            @php $maxWeek = max(1, collect($weeklyBreakdown)->max('amount')); @endphp
                            <div class="h-full cta-gradient rounded-full transition-all" style="width: {{ $maxWeek > 0 ? min(100, ($week['amount'] / $maxWeek) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs text-on-surface-variant shrink-0">{{ $week['sessions'] }} {{ Str::plural('session', $week['sessions']) }}</span>
                    </div>
                </div>
            @endforeach

            @if(collect($weeklyBreakdown)->sum('amount') == 0)
                <div class="text-center py-10">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant/30 mb-3">trending_up</span>
                    <p class="text-base text-on-surface-variant">Abhi koi earning nahi hui. Sessions complete karne par yahan dikhega.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB: Session Earnings --}}
    @if($tab === 'sessions')
        <div class="space-y-3">
            <h3 class="text-lg font-headline font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">receipt_long</span>
                Session Earnings
            </h3>

            @forelse($sessionEarnings as $se)
                <div class="bg-surface-container-lowest rounded-2xl ghost-border p-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-secondary-container/40 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-secondary text-xl" style="font-variation-settings: 'FILL' 1">check_circle</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-base font-bold text-on-surface">
                                        {{ $se->service?->name ?? $se->booking?->service?->name ?? $se->booking?->package?->name ?? 'Session' }}
                                    </p>
                                    <p class="text-sm text-on-surface-variant">
                                        {{ $se->booking?->customer?->name ?? 'Customer' }}
                                        &middot; #{{ $se->booking?->booking_number }}
                                    </p>
                                </div>
                                <p class="text-lg font-headline font-bold text-secondary shrink-0">+₹{{ number_format($se->earning_amount) }}</p>
                            </div>
                            <p class="text-xs text-on-surface-variant/60 mt-1">
                                <span class="material-symbols-outlined text-xs align-middle">schedule</span>
                                {{ $se->completed_at?->format('d M Y, g:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant/30 mb-3">receipt_long</span>
                    <p class="text-base text-on-surface-variant">Koi session earning abhi nahi hai.</p>
                </div>
            @endforelse

            <div class="mt-4">{{ $sessionEarnings->links() }}</div>
        </div>
    @endif

    {{-- TAB: Payout History --}}
    @if($tab === 'payouts')
        <div class="space-y-3">
            <h3 class="text-lg font-headline font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">payments</span>
                Payout History
            </h3>

            @forelse($payouts as $payout)
                <div class="bg-surface-container-lowest rounded-2xl ghost-border p-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0
                            {{ $payout->status === 'processed' ? 'bg-secondary-container/40' : ($payout->status === 'pending' ? 'bg-tertiary-fixed/30' : 'bg-error-container/40') }}">
                            <span class="material-symbols-outlined text-xl
                                {{ $payout->status === 'processed' ? 'text-secondary' : ($payout->status === 'pending' ? 'text-tertiary' : 'text-error') }}"
                                style="font-variation-settings: 'FILL' 1">
                                {{ $payout->status === 'processed' ? 'check_circle' : ($payout->status === 'pending' ? 'hourglass_top' : 'error') }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-base font-bold text-on-surface">{{ $payout->period }}</p>
                                    <p class="text-sm text-on-surface-variant">
                                        {{ $payout->sessions_count }} {{ Str::plural('session', $payout->sessions_count) }}
                                        @if($payout->period_start && $payout->period_end)
                                            &middot; {{ $payout->period_start->format('d M') }} – {{ $payout->period_end->format('d M') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-lg font-headline font-bold {{ $payout->status === 'processed' ? 'text-secondary' : 'text-on-surface' }}">
                                        ₹{{ number_format($payout->amount) }}
                                    </p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $payout->status === 'processed' ? 'bg-secondary-container/50 text-secondary' : ($payout->status === 'pending' ? 'bg-tertiary-fixed/30 text-tertiary' : 'bg-error-container/50 text-error') }}">
                                        {{ $payout->statusLabel() }}
                                    </span>
                                </div>
                            </div>
                            @if($payout->reference)
                                <p class="text-xs text-on-surface-variant/50 mt-1">Ref: {{ $payout->reference }}</p>
                            @endif
                            @if($payout->processed_at)
                                <p class="text-xs text-on-surface-variant/50 mt-0.5">Paid on {{ $payout->processed_at->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant/30 mb-3">payments</span>
                    <p class="text-base text-on-surface-variant">Koi payout abhi nahi hua. Jab admin payment process karenge tab yahan dikhega.</p>
                </div>
            @endforelse

            <div class="mt-4">{{ $payouts->links() }}</div>
        </div>
    @endif
</div>
