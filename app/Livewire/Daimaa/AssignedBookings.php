<?php

namespace App\Livewire\Daimaa;

use App\Models\BookingSession;
use Livewire\Component;
use Livewire\WithPagination;

class AssignedBookings extends Component
{
    use WithPagination;

    public string $filter = 'upcoming';

    public ?int $otpSessionId = null;
    public string $otpInput = '';
    public ?string $otpError = null;

    public function openOtpModal(int $sessionId)
    {
        $hasActive = BookingSession::where('daimaa_id', auth()->id())
            ->where('status', 'started')
            ->exists();

        if ($hasActive) {
            session()->flash('error', 'Pehle chalu session khatam karein — ek samay mein sirf ek session chalu ho sakta hai.');
            return;
        }

        $this->otpSessionId = $sessionId;
        $this->otpInput = '';
        $this->otpError = null;
    }

    public function closeOtpModal()
    {
        $this->otpSessionId = null;
        $this->otpInput = '';
        $this->otpError = null;
    }

    public function verifyAndStart()
    {
        $session = BookingSession::where('daimaa_id', auth()->id())->findOrFail($this->otpSessionId);

        if (!$session->start_otp) {
            $this->startSession($session);
            return;
        }

        if (trim($this->otpInput) !== $session->start_otp) {
            $this->otpError = 'Galat code hai, dobara try karein';
            return;
        }

        $this->startSession($session);
    }

    protected function startSession(BookingSession $session): void
    {
        $hasActive = BookingSession::where('daimaa_id', auth()->id())
            ->where('status', 'started')
            ->where('id', '!=', $session->id)
            ->exists();

        if ($hasActive) {
            $this->otpError = 'Pehle chalu session khatam karein';
            return;
        }

        $session->update([
            'status' => 'started',
            'started_at' => now(),
            'start_otp' => null,
        ]);

        $booking = $session->booking;
        if (in_array($booking->status, ['assigned', 'confirmed', 'pending'])) {
            $booking->update(['status' => 'in_progress']);
            $booking->statusHistories()->create([
                'from_status' => $booking->getOriginal('status') ?: $booking->status,
                'to_status' => 'in_progress',
                'changed_by' => auth()->id(),
            ]);
        }

        $this->closeOtpModal();
    }

    public function markCompleted(int $sessionId)
    {
        $session = BookingSession::where('daimaa_id', auth()->id())
            ->with(['service', 'booking'])
            ->findOrFail($sessionId);

        if ($session->started_at) {
            $durationMinutes = $session->sessionDurationMinutes();
            $elapsed = now()->diffInSeconds($session->started_at);
            if ($elapsed < ($durationMinutes * 60)) {
                $remaining = ($durationMinutes * 60) - $elapsed;
                $mins = (int) ceil($remaining / 60);
                session()->flash('error', "Session abhi khatam nahi hua — {$mins} minute baki hain.");
                return;
            }
        }

        $session->update(['status' => 'completed', 'completed_at' => now()]);
        $session->calculateEarning();

        $booking = $session->booking;
        $allDone = $booking->sessions()->where('status', '!=', 'completed')->doesntExist();
        if ($allDone) {
            $booking->update(['status' => 'completed', 'completed_at' => now()]);
            $booking->statusHistories()->create([
                'from_status' => 'in_progress',
                'to_status' => 'completed',
                'changed_by' => auth()->id(),
            ]);
        }
    }

    public function render()
    {
        $sessions = BookingSession::where('daimaa_id', auth()->id())
            ->when($this->filter !== 'all', function ($q) {
                if ($this->filter === 'upcoming') {
                    $q->whereIn('status', ['upcoming', 'scheduled']);
                } else {
                    $q->where('status', $this->filter);
                }
            })
            ->with(['service', 'booking.customer', 'booking.package', 'booking.service', 'booking.address'])
            ->latest('scheduled_at')
            ->paginate(10);

        $hasActiveSession = BookingSession::where('daimaa_id', auth()->id())
            ->where('status', 'started')
            ->exists();

        return view('livewire.daimaa.assigned-bookings', [
            'sessions' => $sessions,
            'hasActiveSession' => $hasActiveSession,
        ]);
    }
}
