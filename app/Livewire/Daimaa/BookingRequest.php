<?php

namespace App\Livewire\Daimaa;

use App\Jobs\DispatchDaimaaJob;
use App\Models\BookingAssignment;
use App\Models\BookingSession;
use Livewire\Component;

class BookingRequest extends Component
{
    public ?string $declineReason = null;
    public ?int $decliningId = null;

    public function accept(int $assignmentId)
    {
        $assignment = BookingAssignment::where('daimaa_id', auth()->id())
            ->where('dispatch_status', 'pending')
            ->with('booking')
            ->findOrFail($assignmentId);

        if ($assignment->isExpired()) {
            session()->flash('error', 'Samay khatam ho gaya — yeh request expire ho chuki hai.');
            return;
        }

        $assignment->update([
            'dispatch_status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $booking = $assignment->booking;

        // Assign daimaa to all sessions
        $existingSessions = $booking->sessions()->count();

        if ($existingSessions > 0) {
            $booking->sessions()->update(['daimaa_id' => auth()->id()]);
        } else {
            $sessionsToCreate = $booking->package ? $booking->package->total_sessions : 1;
            $scheduledAt = $booking->scheduled_date && $booking->scheduled_time
                ? \Carbon\Carbon::parse($booking->scheduled_date->format('Y-m-d') . ' ' . $booking->scheduled_time)
                : ($booking->scheduled_date ? \Carbon\Carbon::parse($booking->scheduled_date)->setHour(9) : null);

            for ($i = 1; $i <= $sessionsToCreate; $i++) {
                BookingSession::create([
                    'booking_id' => $booking->id,
                    'daimaa_id' => auth()->id(),
                    'service_id' => $booking->service_id,
                    'session_number' => $i,
                    'scheduled_at' => $i === 1 ? $scheduledAt : null,
                    'status' => $i === 1 && $scheduledAt ? 'scheduled' : 'upcoming',
                ]);
            }
        }

        // Generate OTPs
        $booking->sessions()->whereNull('start_otp')->each(fn (BookingSession $s) => $s->generateOtp());

        // Update booking status
        $oldStatus = $booking->status;
        $booking->update(['status' => 'assigned']);
        $booking->statusHistories()->create([
            'from_status' => $oldStatus,
            'to_status' => 'assigned',
            'changed_by' => auth()->id(),
            'notes' => 'Auto-assigned and accepted by Daimaa.',
        ]);

        session()->flash('success', 'Booking accept ho gayi! Aap sessions mein dekh sakte hain.');
    }

    public function openDeclineModal(int $assignmentId)
    {
        $this->decliningId = $assignmentId;
        $this->declineReason = null;
    }

    public function closeDeclineModal()
    {
        $this->decliningId = null;
        $this->declineReason = null;
    }

    public function decline()
    {
        if (!$this->decliningId) return;

        $assignment = BookingAssignment::where('daimaa_id', auth()->id())
            ->where('dispatch_status', 'pending')
            ->with('booking')
            ->findOrFail($this->decliningId);

        $assignment->update([
            'dispatch_status' => 'declined',
            'rejected_at' => now(),
            'rejection_reason' => $this->declineReason,
        ]);

        // Penalize reliability
        $penalty = config('daimaa_matching.reliability_penalty_decline', 3);
        auth()->user()->daimaaProfile?->penalizeReliability($penalty);
        auth()->user()->daimaaProfile?->recordAssignment(declined: true);

        // Dispatch to next candidate
        $nextRank = ($assignment->dispatch_rank ?? 0) + 1;
        DispatchDaimaaJob::dispatch($assignment->booking_id, $nextRank);

        $this->closeDeclineModal();
        session()->flash('info', 'Request decline ho gayi.');
    }

    public function render()
    {
        $pendingRequests = BookingAssignment::where('daimaa_id', auth()->id())
            ->where('dispatch_status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->with(['booking.service', 'booking.package', 'booking.address.city'])
            ->latest('assigned_at')
            ->get();

        return view('livewire.daimaa.booking-request', [
            'pendingRequests' => $pendingRequests,
        ]);
    }
}
