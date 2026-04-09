<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use Livewire\Component;

class BookingDetail extends Component
{
    public int $bookingId;
    public ?Booking $booking = null;
    public bool $showReviewForm = false;
    public int $rating = 5;
    public string $reviewComment = '';

    public function mount(int $bookingId)
    {
        $this->bookingId = $bookingId;
        $this->booking = Booking::where('customer_id', auth()->id())
            ->with(['package', 'service', 'address.city', 'sessions.daimaa', 'review', 'payments', 'items.itemable', 'statusHistories.changedByUser'])
            ->findOrFail($bookingId);
    }

    public function submitReview()
    {
        $this->validate([
            'rating' => 'required|integer|between:1,5',
            'reviewComment' => 'required|string|min:10|max:1000',
        ]);

        $this->booking->review()->create([
            'customer_id' => auth()->id(),
            'daimaa_id' => $this->booking->sessions->first()?->daimaa_id,
            'rating' => $this->rating,
            'comment' => $this->reviewComment,
        ]);

        $this->showReviewForm = false;
        $this->booking->refresh();
    }

    public function render()
    {
        return view('livewire.customer.booking-detail');
    }
}
