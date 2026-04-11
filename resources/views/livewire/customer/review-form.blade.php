<div>
    {{-- Flash messages --}}
    @if(session('review_success'))
        <div class="flex items-center gap-3 bg-primary-fixed/60 text-primary rounded-2xl px-5 py-4 mb-6 animate-fade-in">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            <p class="text-sm font-medium">{{ session('review_success') }}</p>
        </div>
    @endif
    @if(session('review_error'))
        <div class="flex items-center gap-3 bg-error-container text-on-error-container rounded-2xl px-5 py-4 mb-6 animate-fade-in">
            <span class="material-symbols-outlined">error</span>
            <p class="text-sm font-medium">{{ session('review_error') }}</p>
        </div>
    @endif

    {{-- Section 1: Bookings waiting for review --}}
    @if($reviewableBookings->count())
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-tertiary">edit_note</span>
                <h2 class="text-lg font-headline font-bold text-on-surface">Share Your Experience</h2>
            </div>
            <p class="text-sm text-on-surface-variant mb-4">
                {{ $reviewableBookings->count() }} completed {{ Str::plural('booking', $reviewableBookings->count()) }} waiting for your feedback
            </p>

            <div class="space-y-4">
                @foreach($reviewableBookings as $booking)
                    <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden transition-all duration-300">
                        {{-- Booking info row --}}
                        <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-11 h-11 rounded-xl bg-primary-fixed flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary">{{ $booking->service?->icon ?? ($booking->package ? 'inventory_2' : 'spa') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-on-surface truncate">{{ $booking->package?->name ?? $booking->service?->name ?? 'Booking' }}</p>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                                        <span class="text-xs text-on-surface-variant">{{ $booking->booking_number }}</span>
                                        @if($booking->completed_at)
                                            <span class="text-xs text-on-surface-variant/60">{{ $booking->completed_at->format('M j, Y') }}</span>
                                        @endif
                                        @php $assignedDaimaa = $booking->assignments->firstWhere('accepted_at', '!=', null)?->daimaa; @endphp
                                        @if($assignedDaimaa)
                                            <span class="text-xs text-primary flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs">person</span>
                                                {{ $assignedDaimaa->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($bookingId !== $booking->id)
                                <button
                                    wire:click="startReview({{ $booking->id }})"
                                    class="btn-primary text-sm px-5 py-2.5 shrink-0"
                                >
                                    <span class="material-symbols-outlined text-base mr-1.5">star</span>
                                    Write Review
                                </button>
                            @endif
                        </div>

                        {{-- Inline review form (expands inside card) --}}
                        @if($bookingId === $booking->id)
                            <div class="px-5 pb-5 animate-fade-in" style="border-top: 1px solid rgba(218, 193, 186, 0.2);">
                                {{-- Star rating --}}
                                <div class="mt-4 mb-4">
                                    <p class="text-sm font-medium text-on-surface mb-2">How was your experience?</p>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button
                                                wire:click="setRating({{ $i }})"
                                                class="group p-1 transition-transform duration-150 hover:scale-110 active:scale-95"
                                            >
                                                <span
                                                    class="material-symbols-outlined text-3xl transition-colors duration-150 {{ $i <= $rating ? 'text-tertiary' : 'text-surface-dim hover:text-tertiary/50' }}"
                                                    style="font-variation-settings: 'FILL' {{ $i <= $rating ? '1' : '0' }}"
                                                >star</span>
                                            </button>
                                        @endfor
                                        @if($rating > 0)
                                            <span class="text-sm font-medium text-on-surface-variant ml-2">
                                                {{ ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$rating] }}
                                            </span>
                                        @endif
                                    </div>
                                    @error('rating') <p class="text-xs text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-xs">error</span> {{ $message }}</p> @enderror
                                </div>

                                {{-- Comment --}}
                                <div class="mb-4">
                                    <label for="review-comment-{{ $booking->id }}" class="text-sm font-medium text-on-surface mb-1.5 block">
                                        Tell us more <span class="text-on-surface-variant/50 font-normal">(optional)</span>
                                    </label>
                                    <textarea
                                        id="review-comment-{{ $booking->id }}"
                                        wire:model="comment"
                                        class="input-field"
                                        rows="3"
                                        maxlength="1000"
                                        placeholder="What did you like? How was the Daimaa? Any suggestions?"
                                    ></textarea>
                                    <p class="text-xs text-on-surface-variant/50 mt-1 text-right">{{ strlen($comment) }}/1000</p>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-3">
                                    <button
                                        wire:click="submitReview"
                                        wire:loading.attr="disabled"
                                        class="btn-primary text-sm px-6 py-2.5"
                                        @if($rating === 0) disabled @endif
                                    >
                                        <span wire:loading.remove wire:target="submitReview" class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-base">send</span>
                                            Submit Review
                                        </span>
                                        <span wire:loading wire:target="submitReview" class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            Submitting...
                                        </span>
                                    </button>
                                    <button
                                        wire:click="cancelForm"
                                        class="btn-outline text-sm px-5 py-2.5"
                                    >Cancel</button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section 2: Submitted reviews --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-primary">reviews</span>
            <h2 class="text-lg font-headline font-bold text-on-surface">Your Reviews</h2>
            @if($myReviews->count())
                <span class="text-xs bg-primary-fixed text-primary font-bold px-2.5 py-0.5 rounded-full">{{ $myReviews->count() }}</span>
            @endif
        </div>

        @forelse($myReviews as $review)
            <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden mb-4 transition-all duration-300">
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                        {{-- Review content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-9 h-9 rounded-xl bg-primary-fixed flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary text-lg">{{ $review->booking?->service?->icon ?? ($review->booking?->package ? 'inventory_2' : 'spa') }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-on-surface text-sm">{{ $review->booking?->package?->name ?? $review->booking?->service?->name ?? 'Booking' }}</p>
                                    <p class="text-xs text-on-surface-variant/60">{{ $review->booking?->booking_number }}</p>
                                </div>
                            </div>

                            {{-- Stars --}}
                            <div class="flex items-center gap-0.5 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-lg {{ $i <= $review->rating ? 'text-tertiary' : 'text-surface-dim' }}" style="font-variation-settings: 'FILL' 1">star</span>
                                @endfor
                                <span class="text-xs font-medium text-on-surface-variant ml-1.5">{{ ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$review->rating] }}</span>
                            </div>

                            @if($review->comment)
                                <p class="text-sm text-on-surface-variant leading-relaxed mb-2">{{ $review->comment }}</p>
                            @endif

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-on-surface-variant/50">
                                <span>{{ $review->created_at->diffForHumans() }}</span>
                                @if($review->daimaa)
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">person</span>
                                        Daimaa: {{ $review->daimaa->name }}
                                    </span>
                                @endif
                                @if($review->is_published)
                                    <span class="flex items-center gap-1 text-primary/60">
                                        <span class="material-symbols-outlined text-xs">visibility</span>
                                        Published
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Edit/delete buttons --}}
                        @if(!$editingReviewId || $editingReviewId !== $review->id)
                            <div class="flex sm:flex-col gap-2 shrink-0">
                                <button
                                    wire:click="editReview({{ $review->id }})"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-primary hover:text-primary/80 transition-colors px-3 py-2 rounded-lg hover:bg-primary-fixed/30"
                                >
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                    Edit
                                </button>
                                <button
                                    wire:click="deleteReview({{ $review->id }})"
                                    wire:confirm="Are you sure you want to delete this review?"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-error hover:text-error/80 transition-colors px-3 py-2 rounded-lg hover:bg-error/5"
                                >
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Delete
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Inline edit form --}}
                    @if($editingReviewId === $review->id)
                        <div class="mt-4 pt-4 animate-fade-in" style="border-top: 1px solid rgba(218, 193, 186, 0.2);">
                            {{-- Star rating --}}
                            <div class="mb-3">
                                <p class="text-sm font-medium text-on-surface mb-2">Update your rating</p>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button
                                            wire:click="setRating({{ $i }})"
                                            class="group p-1 transition-transform duration-150 hover:scale-110 active:scale-95"
                                        >
                                            <span
                                                class="material-symbols-outlined text-3xl transition-colors duration-150 {{ $i <= $rating ? 'text-tertiary' : 'text-surface-dim hover:text-tertiary/50' }}"
                                                style="font-variation-settings: 'FILL' {{ $i <= $rating ? '1' : '0' }}"
                                            >star</span>
                                        </button>
                                    @endfor
                                    @if($rating > 0)
                                        <span class="text-sm font-medium text-on-surface-variant ml-2">
                                            {{ ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$rating] }}
                                        </span>
                                    @endif
                                </div>
                                @error('rating') <p class="text-xs text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-xs">error</span> {{ $message }}</p> @enderror
                            </div>

                            {{-- Comment --}}
                            <div class="mb-4">
                                <textarea
                                    wire:model="comment"
                                    class="input-field"
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Update your thoughts..."
                                ></textarea>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-3">
                                <button wire:click="submitReview" wire:loading.attr="disabled" class="btn-primary text-sm px-6 py-2.5">
                                    <span wire:loading.remove wire:target="submitReview" class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-base">save</span>
                                        Update Review
                                    </span>
                                    <span wire:loading wire:target="submitReview" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Saving...
                                    </span>
                                </button>
                                <button wire:click="cancelForm" class="btn-outline text-sm px-5 py-2.5">Cancel</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            @if(!$reviewableBookings->count())
                <div class="text-center py-16 bg-surface-container-lowest rounded-2xl ghost-border">
                    <span class="material-symbols-outlined text-5xl text-on-surface-variant/20 mb-3 block">rate_review</span>
                    <p class="text-on-surface-variant font-medium mb-1">No reviews yet</p>
                    <p class="text-sm text-on-surface-variant/60">After completing a booking, you can share your experience here.</p>
                </div>
            @else
                <div class="text-center py-10 bg-surface-container-lowest rounded-2xl ghost-border">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant/20 mb-2 block">reviews</span>
                    <p class="text-sm text-on-surface-variant/60">Your submitted reviews will appear here.</p>
                </div>
            @endif
        @endforelse
    </div>
</div>
