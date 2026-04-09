<div>
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach(['upcoming' => 'Upcoming', 'started' => 'In Progress', 'completed' => 'Completed', 'all' => 'All'] as $key => $label)
        <button wire:click="$set('filter', '{{ $key }}')" class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ $filter === $key ? 'cta-gradient text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">{{ $label }}</button>
        @endforeach
    </div>

    @forelse($sessions as $session)
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <p class="text-xs text-on-surface-variant">Session #{{ $session->session_number }} · {{ $session->booking?->booking_number }}</p>
                <h3 class="text-lg font-semibold text-on-surface">{{ $session->booking?->package?->name ?? $session->booking?->service?->name }}</h3>
                <p class="text-sm text-on-surface-variant">Customer: {{ $session->booking?->customer?->name }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                {{ match($session->status) {
                    'upcoming' => 'bg-tertiary-fixed/30 text-tertiary',
                    'started' => 'bg-primary-fixed/40 text-primary',
                    'completed' => 'bg-primary text-on-primary',
                    default => 'bg-surface-container text-on-surface-variant',
                } }}">
                {{ ucfirst($session->status) }}
            </span>
        </div>
        <div class="flex flex-wrap gap-4 text-sm text-on-surface-variant mb-4">
            @if($session->scheduled_at)
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-lg">calendar_today</span> {{ $session->scheduled_at->format('M d, Y') }}</span>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-lg">schedule</span> {{ $session->scheduled_at->format('g:i A') }}</span>
            @endif
            @if($session->booking?->address)
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-lg">location_on</span> {{ $session->booking->address->pincode }}</span>
            @endif
        </div>
        <div class="flex gap-2">
            @if($session->status === 'upcoming')
            <button wire:click="markStarted({{ $session->id }})" class="btn-primary text-sm">
                <span class="material-symbols-outlined mr-1 text-lg">play_arrow</span> Start Session
            </button>
            @elseif($session->status === 'started')
            <button wire:click="markCompleted({{ $session->id }})" class="btn-primary text-sm">
                <span class="material-symbols-outlined mr-1 text-lg">check_circle</span> Complete Session
            </button>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-16 bg-surface-container-lowest rounded-2xl">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4">event_busy</span>
        <p class="text-on-surface-variant">No {{ $filter === 'all' ? '' : $filter }} sessions found.</p>
    </div>
    @endforelse

    <div class="mt-4">{{ $sessions->links() }}</div>
</div>
