<div>
    <div class="flex flex-wrap gap-2 mb-6">
        <button wire:click="$set('statusFilter', '')" class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ $statusFilter === '' ? 'cta-gradient text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">All</button>
        @foreach(['pending', 'confirmed', 'assigned', 'in_progress', 'completed', 'cancelled'] as $status)
        <button wire:click="$set('statusFilter', '{{ $status }}')" class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ $statusFilter === $status ? 'cta-gradient text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</button>
        @endforeach
    </div>

    @if($bookings->count())
    <div class="space-y-4">
        @foreach($bookings as $booking)
        <div class="bg-surface-container-lowest rounded-2xl p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <p class="text-xs text-on-surface-variant">{{ $booking->booking_number }}</p>
                    <h3 class="text-lg font-semibold text-on-surface">
                        {{ $booking->package?->name ?? $booking->service?->name ?? 'Custom Booking' }}
                    </h3>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                    {{ match($booking->status) {
                        'pending' => 'bg-tertiary-fixed/30 text-tertiary',
                        'confirmed', 'assigned' => 'bg-primary-fixed/40 text-primary',
                        'in_progress' => 'bg-secondary-container text-on-secondary-fixed',
                        'completed' => 'bg-primary text-on-primary',
                        'cancelled', 'refunded' => 'bg-error-container text-on-error-container',
                        default => 'bg-surface-container text-on-surface-variant',
                    } }}">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>
            <div class="flex flex-wrap gap-4 text-sm text-on-surface-variant mb-4">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-lg">calendar_today</span> {{ $booking->scheduled_date->format('M d, Y') }}</span>
                @if($booking->scheduled_time)
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-lg">schedule</span> {{ \Carbon\Carbon::parse($booking->scheduled_time)->format('g:i A') }}</span>
                @endif
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-lg">payments</span> ₹{{ number_format($booking->total_amount) }}</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn-outline text-xs px-4 py-2">View Details</a>
                @if(in_array($booking->status, ['pending', 'confirmed']))
                <button wire:click="cancelBooking({{ $booking->id }})" wire:confirm="Are you sure you want to cancel this booking?" class="px-4 py-2 text-xs font-medium text-error rounded-full ghost-border hover:bg-error-container transition-colors">Cancel</button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $bookings->links() }}</div>
    @else
    <div class="text-center py-16 bg-surface-container-lowest rounded-2xl">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4">calendar_month</span>
        <p class="text-on-surface-variant mb-4">No bookings yet</p>
        <a href="{{ route('customer.book') }}" class="btn-primary">Book Your First Session</a>
    </div>
    @endif
</div>
