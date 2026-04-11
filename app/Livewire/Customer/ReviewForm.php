<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Models\Review;
use Livewire\Component;

class ReviewForm extends Component
{
    public int $rating = 0;
    public string $comment = '';
    public ?int $bookingId = null;
    public ?int $editingReviewId = null;

    protected function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'bookingId' => 'required|exists:bookings,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'rating.required' => 'Please select a star rating.',
            'rating.min' => 'Please select at least 1 star.',
            'bookingId.required' => 'Please select a booking to review.',
        ];
    }

    public function setRating(int $stars)
    {
        $this->rating = $stars;
    }

    public function startReview(int $bookingId)
    {
        $this->resetForm();
        $this->bookingId = $bookingId;
    }

    public function editReview(int $reviewId)
    {
        $review = Review::where('customer_id', auth()->id())->findOrFail($reviewId);
        $this->editingReviewId = $review->id;
        $this->bookingId = $review->booking_id;
        $this->rating = $review->rating;
        $this->comment = $review->comment ?? '';
    }

    public function cancelForm()
    {
        $this->resetForm();
    }

    public function submitReview()
    {
        $this->validate();

        $user = auth()->user();
        $booking = Booking::where('customer_id', $user->id)
            ->where('id', $this->bookingId)
            ->firstOrFail();

        $daimaaId = $booking->assignments()->whereNotNull('accepted_at')->first()?->daimaa_id;

        if ($this->editingReviewId) {
            $review = Review::where('customer_id', $user->id)->findOrFail($this->editingReviewId);
            $review->update([
                'rating' => $this->rating,
                'comment' => $this->comment ?: null,
            ]);
            session()->flash('review_success', 'Review updated successfully!');
        } else {
            if ($booking->review) {
                session()->flash('review_error', 'You have already reviewed this booking.');
                $this->resetForm();
                return;
            }

            Review::create([
                'booking_id' => $booking->id,
                'customer_id' => $user->id,
                'daimaa_id' => $daimaaId,
                'rating' => $this->rating,
                'comment' => $this->comment ?: null,
                'is_published' => true,
            ]);
            session()->flash('review_success', 'Thank you for your review!');
        }

        $this->resetForm();
    }

    public function deleteReview(int $reviewId)
    {
        Review::where('customer_id', auth()->id())->findOrFail($reviewId)->delete();
        session()->flash('review_success', 'Review deleted.');
    }

    public function resetForm()
    {
        $this->rating = 0;
        $this->comment = '';
        $this->bookingId = null;
        $this->editingReviewId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $user = auth()->user();

        $reviewableBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->whereDoesntHave('review')
            ->with(['package', 'service', 'assignments.daimaa'])
            ->latest('completed_at')
            ->get();

        $myReviews = Review::where('customer_id', $user->id)
            ->with(['booking.package', 'booking.service', 'daimaa'])
            ->latest()
            ->get();

        return view('livewire.customer.review-form', [
            'reviewableBookings' => $reviewableBookings,
            'myReviews' => $myReviews,
        ]);
    }
}
