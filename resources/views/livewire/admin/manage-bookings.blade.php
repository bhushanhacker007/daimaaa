<div>
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" class="input-field" placeholder="Search bookings or customers...">
        </div>
        <select wire:model.live="statusFilter" class="input-field w-auto">
            <option value="">All Statuses</option>
            @foreach(['pending', 'confirmed', 'assigned', 'in_progress', 'completed', 'cancelled', 'refunded'] as $s)
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
                        {{ $b->assignments->last()?->daimaa?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if(!$b->assignments->count() && in_array($b->status, ['pending', 'confirmed']))
                        <button wire:click="$set('assignDaimaaBookingId', {{ $b->id }})" class="text-xs text-primary font-medium hover:underline">Assign</button>
                        @endif
                    </td>
                </tr>
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
        <div class="bg-surface-container-lowest rounded-3xl p-8 max-w-md w-full mx-4 ambient-shadow">
            <h3 class="text-xl font-headline font-bold text-primary mb-4">Assign Daimaa</h3>
            <select wire:model="selectedDaimaaId" class="input-field mb-4">
                <option value="">Select a verified Daimaa</option>
                @foreach($daimaas as $d)
                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->daimaaProfile?->years_of_experience }}y exp)</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button wire:click="assignDaimaa" class="btn-primary text-sm" @if(!$selectedDaimaaId) disabled @endif>Confirm Assignment</button>
                <button wire:click="$set('assignDaimaaBookingId', null)" class="btn-outline text-sm">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
