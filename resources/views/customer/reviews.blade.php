<x-dashboard-layout>
    <x-slot:title>My Reviews — Daimaa</x-slot:title>
    <x-slot:heading>My Reviews</x-slot:heading>
    <x-slot:sidebar>@include('customer._sidebar')</x-slot:sidebar>

    @php $reviews = Auth::user()->reviews()->with(['booking.package', 'booking.service', 'daimaa'])->latest()->get(); @endphp

    @forelse($reviews as $review)
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-on-surface">{{ $review->booking?->package?->name ?? $review->booking?->service?->name }}</h3>
            <div class="flex gap-1">
                @for($i = 1; $i <= 5; $i++)
                <span class="material-symbols-outlined text-sm {{ $i <= $review->rating ? 'text-tertiary' : 'text-surface-dim' }}" style="font-variation-settings: 'FILL' 1">star</span>
                @endfor
            </div>
        </div>
        <p class="text-sm text-on-surface-variant mb-2">{{ $review->comment }}</p>
        <p class="text-xs text-on-surface-variant/60">{{ $review->created_at->diffForHumans() }} · Daimaa: {{ $review->daimaa?->name ?? 'N/A' }}</p>
    </div>
    @empty
    <div class="text-center py-16 bg-surface-container-lowest rounded-2xl">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4">rate_review</span>
        <p class="text-on-surface-variant">No reviews yet. Complete a booking to leave a review.</p>
    </div>
    @endforelse
</x-dashboard-layout>
