<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Models\BookingSession;
use Carbon\Carbon;
use Livewire\Component;

class BookingDetail extends Component
{
    public int $bookingId;
    public ?Booking $booking = null;
    public bool $showReviewForm = false;
    public int $rating = 5;
    public string $reviewComment = '';

    public ?int $scheduleSessionId = null;
    public string $sessionDate = '';
    public string $sessionTime = '';

    public function mount(int $bookingId)
    {
        $this->bookingId = $bookingId;
        $this->loadBooking();
    }

    protected function loadBooking(): void
    {
        $this->booking = Booking::where('customer_id', auth()->id())
            ->with(['package.services', 'service', 'address.city', 'sessions.daimaa', 'sessions.service', 'review', 'payments', 'items.itemable', 'statusHistories.changedByUser', 'assignments.daimaa', 'coupon'])
            ->findOrFail($this->bookingId);
    }

    public function cancelBooking(int $id)
    {
        $booking = Booking::where('customer_id', auth()->id())->findOrFail($id);
        if (in_array($booking->status, ['pending', 'confirmed'])) {
            $booking->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            $booking->statusHistories()->create([
                'from_status' => $booking->getOriginal('status'),
                'to_status' => 'cancelled',
                'changed_by' => auth()->id(),
                'notes' => 'Cancelled by customer.',
            ]);
            $this->loadBooking();
        }
    }

    public function scheduleSession(int $sessionId)
    {
        $session = BookingSession::where('booking_id', $this->bookingId)->findOrFail($sessionId);

        $this->scheduleSessionId = $sessionId;
        $this->sessionDate = $session->scheduled_at ? $session->scheduled_at->format('Y-m-d') : now()->addDays(1)->format('Y-m-d');
        $this->sessionTime = $session->scheduled_at ? $session->scheduled_at->format('H:i') : '09:00';
    }

    public function saveSessionSchedule()
    {
        $this->validate([
            'sessionDate' => 'required|date|after_or_equal:today',
            'sessionTime' => 'required|date_format:H:i',
        ], [
            'sessionDate.after_or_equal' => 'Please pick today or a future date.',
            'sessionTime.required' => 'Please pick a time slot.',
        ]);

        $session = BookingSession::where('booking_id', $this->bookingId)
            ->findOrFail($this->scheduleSessionId);

        $session->update([
            'scheduled_at' => Carbon::parse("{$this->sessionDate} {$this->sessionTime}"),
            'status' => 'scheduled',
        ]);

        $this->scheduleSessionId = null;
        $this->sessionDate = '';
        $this->sessionTime = '';
        $this->loadBooking();
    }

    public function cancelSessionSchedule()
    {
        $this->scheduleSessionId = null;
        $this->sessionDate = '';
        $this->sessionTime = '';
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
        $this->loadBooking();
    }

    public function render()
    {
        return view('livewire.customer.booking-detail');
    }
}
