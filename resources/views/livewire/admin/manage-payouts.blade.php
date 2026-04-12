<div>
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-secondary-container/50 border border-secondary/15 rounded-xl">
            <span class="material-symbols-outlined text-secondary">check_circle</span>
            <p class="text-sm font-semibold text-secondary">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-error-container/50 border border-error/15 rounded-xl">
            <span class="material-symbols-outlined text-error">warning</span>
            <p class="text-sm font-semibold text-error">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-xl ghost-border p-4">
            <p class="text-xs font-medium text-on-surface-variant mb-1">Total Payouts</p>
            <p class="text-xl font-headline font-bold text-on-surface">{{ $totalPayouts }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-xl ghost-border p-4">
            <p class="text-xs font-medium text-on-surface-variant mb-1">Pending Amount</p>
            <p class="text-xl font-headline font-bold text-tertiary">₹{{ number_format($totalPending) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-xl ghost-border p-4">
            <p class="text-xs font-medium text-on-surface-variant mb-1">Total Paid</p>
            <p class="text-xl font-headline font-bold text-secondary">₹{{ number_format($totalProcessed) }}</p>
        </div>
    </div>

    {{-- Filters & Generate --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="flex-1 flex gap-3">
            <select wire:model.live="statusFilter" class="input-field w-auto text-sm">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="processed">Processed</option>
                <option value="failed">Failed</option>
            </select>
            <select wire:model.live="daimaaFilter" class="input-field w-auto text-sm">
                <option value="">All Daimaas</option>
                @foreach($daimaas as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-3">
            <input type="date" wire:model="generateWeek" class="input-field text-sm w-auto">
            <button wire:click="openGenerateModal" class="btn-primary text-sm whitespace-nowrap">
                <span class="material-symbols-outlined text-sm mr-1">add_circle</span>
                Generate Payouts
            </button>
        </div>
    </div>

    {{-- Payouts Table --}}
    <div class="bg-surface-container-lowest rounded-2xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-on-surface-variant text-left">
                    <th class="px-4 py-3 font-medium">ID</th>
                    <th class="px-4 py-3 font-medium">Daimaa</th>
                    <th class="px-4 py-3 font-medium">Period</th>
                    <th class="px-4 py-3 font-medium">Sessions</th>
                    <th class="px-4 py-3 font-medium">Amount</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Reference</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $p)
                    <tr class="hover:bg-surface-container/50 transition-colors">
                        <td class="px-4 py-3 text-on-surface-variant text-xs">#{{ $p->id }}</td>
                        <td class="px-4 py-3 font-medium text-on-surface">{{ $p->daimaa?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-on-surface-variant">
                            {{ $p->period }}
                            @if($p->period_start && $p->period_end)
                                <br><span class="text-xs text-on-surface-variant/60">{{ $p->period_start->format('d M') }} – {{ $p->period_end->format('d M') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-on-surface-variant">{{ $p->sessions_count }}</td>
                        <td class="px-4 py-3 font-semibold text-primary">₹{{ number_format($p->amount) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                {{ match($p->status) {
                                    'processed' => 'bg-secondary-container/50 text-secondary',
                                    'pending' => 'bg-tertiary-fixed/30 text-tertiary',
                                    'processing' => 'bg-primary-fixed/30 text-primary',
                                    'failed' => 'bg-error-container/50 text-error',
                                    default => 'bg-surface-container text-on-surface-variant',
                                } }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-on-surface-variant">{{ $p->reference ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @if($p->status === 'pending')
                                    <button wire:click="processCashfreePayout({{ $p->id }})"
                                        wire:confirm="Transfer via Cashfree Payouts?"
                                        wire:loading.attr="disabled"
                                        class="text-xs px-2.5 py-1 rounded-lg bg-primary/10 text-primary font-bold hover:bg-primary/20 transition-colors">
                                        <span wire:loading.remove wire:target="processCashfreePayout({{ $p->id }})">Cashfree Pay</span>
                                        <span wire:loading wire:target="processCashfreePayout({{ $p->id }})">...</span>
                                    </button>
                                    <button wire:click="markProcessedManual({{ $p->id }})"
                                        wire:confirm="Mark paid manually (no bank transfer)?"
                                        class="text-xs px-2.5 py-1 rounded-lg bg-secondary-container/50 text-secondary font-bold hover:bg-secondary-container transition-colors">Manual</button>
                                    <button wire:click="markFailed({{ $p->id }})"
                                        wire:confirm="Mark this payout as failed?"
                                        class="text-xs px-2.5 py-1 rounded-lg bg-error/10 text-error font-bold hover:bg-error/20 transition-colors">Fail</button>
                                @elseif($p->status === 'processing')
                                    <button wire:click="checkTransferStatus({{ $p->id }})"
                                        wire:loading.attr="disabled"
                                        class="text-xs px-2.5 py-1 rounded-lg bg-tertiary/10 text-tertiary font-bold hover:bg-tertiary/20 transition-colors">
                                        <span wire:loading.remove wire:target="checkTransferStatus({{ $p->id }})">Check Status</span>
                                        <span wire:loading wire:target="checkTransferStatus({{ $p->id }})">...</span>
                                    </button>
                                @elseif($p->status === 'failed')
                                    <button wire:click="processCashfreePayout({{ $p->id }})"
                                        wire:confirm="Retry transfer via Cashfree?"
                                        class="text-xs px-2.5 py-1 rounded-lg bg-primary/10 text-primary font-bold hover:bg-primary/20 transition-colors">Retry</button>
                                    <button wire:click="markProcessedManual({{ $p->id }})"
                                        wire:confirm="Mark paid manually?"
                                        class="text-xs px-2.5 py-1 rounded-lg bg-secondary-container/50 text-secondary font-bold">Manual</button>
                                @else
                                    <span class="text-xs text-on-surface-variant/40 px-2">Done</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-on-surface-variant">
                            No payouts found. Use "Generate Payouts" to create them from completed sessions.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payouts->links() }}</div>

    {{-- Generate Payouts Modal --}}
    @if($showGenerateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-on-surface/30" wire:click.self="closeGenerateModal">
            <div class="bg-surface-container-lowest rounded-3xl p-6 max-w-lg w-full mx-4 ambient-shadow max-h-[80vh] overflow-y-auto">
                <h3 class="text-xl font-headline font-bold text-primary mb-2">Generate Weekly Payouts</h3>
                <p class="text-sm text-on-surface-variant mb-4">
                    Week of <strong>{{ \Carbon\Carbon::parse($generateWeek)->startOfWeek()->format('d M Y') }}</strong>
                    – <strong>{{ \Carbon\Carbon::parse($generateWeek)->endOfWeek()->format('d M Y') }}</strong>
                </p>

                @if(empty($previewData))
                    <div class="text-center py-8">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant/30 mb-3">info</span>
                        <p class="text-sm text-on-surface-variant">No unpaid sessions found for this week. Either all payouts have been generated already or no sessions were completed.</p>
                    </div>
                @else
                    <div class="space-y-2 mb-4">
                        @foreach($previewData as $pd)
                            <div class="flex items-center justify-between p-3 bg-surface-container rounded-xl">
                                <div>
                                    <p class="text-sm font-bold text-on-surface">{{ $pd['daimaa_name'] }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ $pd['sessions_count'] }} {{ Str::plural('session', $pd['sessions_count']) }}</p>
                                </div>
                                <p class="text-base font-bold text-primary">₹{{ number_format($pd['amount']) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between p-3 bg-primary-fixed/20 rounded-xl mb-4">
                        <span class="text-sm font-bold text-primary">Total</span>
                        <span class="text-lg font-headline font-bold text-primary">₹{{ number_format(collect($previewData)->sum('amount')) }}</span>
                    </div>
                @endif

                <div class="flex gap-2">
                    @if(!empty($previewData))
                        <button wire:click="generatePayouts" class="btn-primary text-sm flex-1">
                            <span wire:loading.remove wire:target="generatePayouts">Generate {{ count($previewData) }} Payouts</span>
                            <span wire:loading wire:target="generatePayouts">Generating...</span>
                        </button>
                    @endif
                    <button wire:click="closeGenerateModal" class="btn-outline text-sm {{ empty($previewData) ? 'flex-1' : '' }}">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
