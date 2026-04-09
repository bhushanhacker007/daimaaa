<?php

namespace App\Livewire\Daimaa;

use App\Models\BookingSession;
use Livewire\Component;

class AssignedBookings extends Component
{
    public string $filter = 'upcoming';

    public function markStarted(int $sessionId)
    {
        $session = BookingSession::where('daimaa_id', auth()->id())->findOrFail($sessionId);
        $session->update(['status' => 'started', 'started_at' => now()]);

        $booking = $session->booking;
        if ($booking->status === 'assigned') {
            $booking->update(['status' => 'in_progress']);
            $booking->statusHistories()->create(['from_status' => 'assigned', 'to_status' => 'in_progress', 'changed_by' => auth()->id()]);
        }
    }

    public function markCompleted(int $sessionId)
    {
        $session = BookingSession::where('daimaa_id', auth()->id())->findOrFail($sessionId);
        $session->update(['status' => 'completed', 'completed_at' => now()]);

        $booking = $session->booking;
        $allDone = $booking->sessions()->where('status', '!=', 'completed')->doesntExist();
        if ($allDone) {
            $booking->update(['status' => 'completed', 'completed_at' => now()]);
            $booking->statusHistories()->create(['from_status' => 'in_progress', 'to_status' => 'completed', 'changed_by' => auth()->id()]);
        }
    }

    public function render()
    {
        $sessions = BookingSession::where('daimaa_id', auth()->id())
            ->when($this->filter !== 'all', fn ($q) => $q->where('status', $this->filter))
            ->with(['booking.customer', 'booking.package', 'booking.service', 'booking.address'])
            ->latest('scheduled_at')
            ->paginate(10);

        return view('livewire.daimaa.assigned-bookings', ['sessions' => $sessions]);
    }
}
