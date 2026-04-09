<div>
    <a href="{{ route('customer.bookings') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary transition-colors mb-6">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Back to Bookings
    </a>

    @if($booking)
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest rounded-2xl p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs text-on-surface-variant">{{ $booking->booking_number }}</p>
                        <h2 class="text-2xl font-headline font-bold text-primary">{{ $booking->package?->name ?? $booking->service?->name }}</h2>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        {{ match($booking->status) {
                            'pending' => 'bg-tertiary-fixed/30 text-tertiary',
                            'confirmed', 'assigned' => 'bg-primary-fixed/40 text-primary',
                            'completed' => 'bg-primary text-on-primary',
                            'cancelled' => 'bg-error-container text-on-error-container',
                            default => 'bg-surface-container text-on-surface-variant',
                        } }}">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-on-surface-variant">Date</p>
                        <p class="font-semibold text-on-surface">{{ $booking->scheduled_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-on-surface-variant">Time</p>
                        <p class="font-semibold text-on-surface">{{ $booking->scheduled_time ? \Carbon\Carbon::parse($booking->scheduled_time)->format('g:i A') : 'Flexible' }}</p>
                    </div>
                    <div>
                        <p class="text-on-surface-variant">Address</p>
                        <p class="font-semibold text-on-surface">{{ $booking->address?->address_line_1 }}, {{ $booking->address?->pincode }}</p>
                    </div>
                    <div>
                        <p class="text-on-surface-variant">Total</p>
                        <p class="font-semibold text-primary text-lg">₹{{ number_format($booking->total_amount) }}</p>
                    </div>
                </div>
            </div>

            {{-- Sessions --}}
            @if($booking->sessions->count())
            <div class="bg-surface-container-lowest rounded-2xl p-6">
                <h3 class="font-headline font-bold text-primary mb-4">Sessions</h3>
                <div class="space-y-3">
                    @foreach($booking->sessions as $session)
                    <div class="flex items-center justify-between p-3 bg-surface-container rounded-xl">
                        <div>
                            <p class="text-sm font-semibold text-on-surface">Session {{ $session->session_number }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $session->daimaa?->name ?? 'Unassigned' }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">{{ ucfirst($session->status) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Review --}}
            @if($booking->status === 'completed')
            <div class="bg-surface-container-lowest rounded-2xl p-6">
                <h3 class="font-headline font-bold text-primary mb-4">Review</h3>
                @if($booking->review)
                <div class="flex gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                    <span class="material-symbols-outlined text-lg {{ $i <= $booking->review->rating ? 'text-tertiary' : 'text-surface-dim' }}" style="font-variation-settings: 'FILL' 1">star</span>
                    @endfor
                </div>
                <p class="text-sm text-on-surface-variant">{{ $booking->review->comment }}</p>
                @elseif(!$showReviewForm)
                <button wire:click="$set('showReviewForm', true)" class="btn-outline text-sm">Write a Review</button>
                @else
                <div class="space-y-4">
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <button wire:click="$set('rating', {{ $i }})">
                            <span class="material-symbols-outlined text-2xl {{ $i <= $rating ? 'text-tertiary' : 'text-surface-dim' }}" style="font-variation-settings: 'FILL' 1">star</span>
                        </button>
                        @endfor
                    </div>
                    <textarea wire:model="reviewComment" class="input-field" rows="3" placeholder="Share your experience..."></textarea>
                    @error('reviewComment') <p class="text-sm text-error">{{ $message }}</p> @enderror
                    <div class="flex gap-2">
                        <button wire:click="submitReview" class="btn-primary text-sm">Submit Review</button>
                        <button wire:click="$set('showReviewForm', false)" class="btn-outline text-sm">Cancel</button>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Status timeline --}}
        <div class="bg-surface-container-lowest rounded-2xl p-6 h-fit">
            <h3 class="font-headline font-bold text-primary mb-4">Timeline</h3>
            <div class="space-y-4">
                @foreach($booking->statusHistories->sortByDesc('created_at') as $history)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="h-3 w-3 bg-primary rounded-full mt-1"></div>
                        @if(!$loop->last)<div class="w-px flex-1 bg-outline-variant/20 my-1"></div>@endif
                    </div>
                    <div class="pb-4">
                        <p class="text-sm font-semibold text-on-surface">{{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $history->created_at->diffForHumans() }}</p>
                        @if($history->notes)<p class="text-xs text-on-surface-variant mt-1">{{ $history->notes }}</p>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
