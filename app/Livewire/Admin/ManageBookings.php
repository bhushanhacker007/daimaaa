<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\User;
use App\Models\BookingSession;
use Livewire\Component;
use Livewire\WithPagination;

class ManageBookings extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $assignDaimaaBookingId = null;
    public ?int $selectedDaimaaId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateStatus(int $bookingId, string $status)
    {
        $booking = Booking::findOrFail($bookingId);
        $oldStatus = $booking->status;
        $booking->update(['status' => $status]);
        $booking->statusHistories()->create([
            'from_status' => $oldStatus,
            'to_status' => $status,
            'changed_by' => auth()->id(),
        ]);
    }

    public function assignDaimaa()
    {
        if (!$this->assignDaimaaBookingId || !$this->selectedDaimaaId) return;

        $booking = Booking::findOrFail($this->assignDaimaaBookingId);
        $booking->assignments()->create([
            'daimaa_id' => $this->selectedDaimaaId,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        $sessionsToCreate = $booking->package ? $booking->package->total_sessions : 1;
        for ($i = 1; $i <= $sessionsToCreate; $i++) {
            BookingSession::create([
                'booking_id' => $booking->id,
                'daimaa_id' => $this->selectedDaimaaId,
                'session_number' => $i,
                'scheduled_at' => $booking->scheduled_date->addDays($i - 1),
                'status' => 'upcoming',
            ]);
        }

        $this->updateStatus($booking->id, 'assigned');
        $this->assignDaimaaBookingId = null;
        $this->selectedDaimaaId = null;
    }

    public function render()
    {
        $query = Booking::with(['customer', 'package', 'service', 'assignments.daimaa'])->latest();
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('booking_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', fn ($q2) => $q2->where('name', 'like', "%{$this->search}%"));
            });
        }
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $daimaas = User::where('role', 'daimaa')
            ->whereHas('daimaaProfile', fn ($q) => $q->where('status', 'verified'))
            ->get();

        return view('livewire.admin.manage-bookings', [
            'bookings' => $query->paginate(15),
            'daimaas' => $daimaas,
        ]);
    }
}
