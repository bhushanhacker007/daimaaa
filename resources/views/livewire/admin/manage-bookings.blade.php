<div>
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" class="input-field" placeholder="Search bookings or customers...">
        </div>
        <select wire:model.live="statusFilter" class="input-field w-auto">
            <option value="">All Statuses</option>
            @foreach(['pending', 'confirmed', 'assigned', 'in_progress', 'completed', 'cancelled', 'refunded', 'needs_manual_assignment'] as $s)
            <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-surface-container text-on-surface-variant text-left">
                <th class="px-4 py-3 font-medium">Booking</th>
                <th class="px-4 py-3 font-medium">Customer</th>
                <th class="px-4 py-3 font-medium">Service/Package</th>
                <th class="px-4 py-3 font-medium">Date</th>
                <th class="px-4 py-3 font-medium">Amount</th>
                <th class="px-4 py-3 font-medium">Status</th>
                <th class="px-4 py-3 font-medium">Daimaa</th>
                <th class="px-4 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($bookings as $b)
                <tr class="hover:bg-surface-container/50 transition-colors">
                    <td class="px-4 py-3 font-medium text-on-surface text-xs">{{ $b->booking_number }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $b->customer?->name }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $b->package?->name ?? $b->service?->name }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $b->scheduled_date->format('M d') }}</td>
                    <td class="px-4 py-3 font-semibold text-primary">₹{{ number_format($b->total_amount) }}</td>
                    <td class="px-4 py-3">
                        <select wire:change="updateStatus({{ $b->id }}, $event.target.value)" class="text-xs bg-surface-container rounded-lg px-2 py-1 border-0">
                            @foreach(['pending', 'confirmed', 'assigned', 'in_progress', 'completed', 'cancelled'] as $s)
                            <option value="{{ $s }}" {{ $b->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3 text-on-surface-variant text-xs">
                        @php
                            $accepted = $b->assignments->firstWhere('dispatch_status', 'accepted');
                            $latestPending = $b->assignments->firstWhere('dispatch_status', 'pending');
                        @endphp
                        @if($accepted)
                            <span class="text-secondary font-semibold">{{ $accepted->daimaa?->name }}</span>
                        @elseif($latestPending)
                            <span class="text-tertiary">Dispatching... (#{{ $latestPending->dispatch_rank }})</span>
                        @elseif($b->assignments->isNotEmpty())
                            <span class="text-on-surface-variant">{{ $b->assignments->last()?->daimaa?->name ?? '—' }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 space-x-1">
                        @if(in_array($b->status, ['pending', 'confirmed', 'needs_manual_assignment']))
                            <button wire:click="$set('assignDaimaaBookingId', {{ $b->id }})" class="text-xs text-primary font-medium hover:underline">Assign</button>
                        @endif
                        @if($b->assignments->count() > 0)
                            <button onclick="document.getElementById('trail-{{ $b->id }}').classList.toggle('hidden')" class="text-xs text-tertiary font-medium hover:underline">Trail</button>
                        @endif
                    </td>
                </tr>
                {{-- Dispatch audit trail (hidden by default) --}}
                @if($b->assignments->count() > 0)
                <tr id="trail-{{ $b->id }}" class="hidden bg-surface-container/20">
                    <td colspan="8" class="px-4 py-3">
                        <p class="text-xs font-bold text-on-surface mb-2">Dispatch Trail</p>
                        <div class="space-y-1.5">
                            @foreach($b->assignments->sortBy('dispatch_rank') as $a)
                                <div class="flex items-center gap-3 text-xs">
                                    <span class="w-5 h-5 rounded-full text-[10px] font-bold flex items-center justify-center shrink-0
                                        {{ $a->dispatch_status === 'accepted' ? 'bg-secondary text-on-secondary' : ($a->dispatch_status === 'declined' ? 'bg-error text-on-error' : ($a->dispatch_status === 'expired' ? 'bg-surface-container-high text-on-surface-variant' : 'bg-tertiary text-on-tertiary')) }}">
                                        {{ $a->dispatch_rank ?? '—' }}
                                    </span>
                                    <span class="font-semibold text-on-surface">{{ $a->daimaa?->name ?? 'Unknown' }}</span>
                                    <span class="text-on-surface-variant">Score: {{ $a->match_score ? number_format($a->match_score, 1) : '—' }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase
                                        {{ match($a->dispatch_status) {
                                            'accepted' => 'bg-secondary-container/50 text-secondary',
                                            'declined' => 'bg-error-container/50 text-error',
                                            'expired' => 'bg-surface-container text-on-surface-variant',
                                            default => 'bg-tertiary-fixed/30 text-tertiary',
                                        } }}">
                                        {{ $a->dispatch_status }}
                                    </span>
                                    @if($a->rejection_reason)
                                        <span class="text-on-surface-variant italic">"{{ Str::limit($a->rejection_reason, 30) }}"</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-on-surface-variant">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $bookings->links() }}</div>

    {{-- Assignment modal --}}
    @if($assignDaimaaBookingId)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-on-surface/30" wire:click.self="$set('assignDaimaaBookingId', null)">
        <div class="bg-surface-container-lowest rounded-3xl p-8 max-w-md w-full mx-4 ambient-shadow" x-data="{ picked: '' }">
            <h3 class="text-xl font-headline font-bold text-primary mb-4">Assign Daimaa</h3>
            @if($daimaas->isEmpty())
                <p class="text-sm text-on-surface-variant mb-4">No verified Daimaas found. Please verify a Daimaa first.</p>
            @else
                <select wire:model="selectedDaimaaId" x-model="picked" class="input-field mb-4">
                    <option value="">Select a verified Daimaa</option>
                    @foreach($daimaas as $d)
                    <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->daimaaProfile?->years_of_experience ?? 0 }}y exp)</option>
                    @endforeach
                </select>
            @endif
            <div class="flex gap-2">
                <button wire:click="assignDaimaa" class="btn-primary text-sm" x-bind:disabled="!picked"
                    :class="!picked ? 'opacity-50 cursor-not-allowed' : ''">
                    <span wire:loading.remove wire:target="assignDaimaa">Confirm Assignment</span>
                    <span wire:loading wire:target="assignDaimaa">Assigning...</span>
                </button>
                <button wire:click="$set('assignDaimaaBookingId', null)" class="btn-outline text-sm">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
