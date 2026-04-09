<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class MyBookings extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function updatingStatusFilter()
    {
        $this->resetPage();
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
        }
    }

    public function render()
    {
        $query = Booking::where('customer_id', auth()->id())
            ->with(['package', 'service', 'sessions.daimaa'])
            ->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.customer.my-bookings', [
            'bookings' => $query->paginate(10),
        ]);
    }
}
