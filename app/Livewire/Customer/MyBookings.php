<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class MyBookings extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $search = '';

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
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
        $userId = auth()->id();

        $counts = Booking::where('customer_id', $userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('pending','confirmed','assigned','in_progress') THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'assigned' THEN 1 ELSE 0 END) as assigned,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress
            ")
            ->first();

        $query = Booking::where('customer_id', $userId)
            ->with(['package', 'service', 'sessions.daimaa', 'address', 'assignments.daimaa', 'review'])
            ->latest();

        if ($this->statusFilter) {
            if ($this->statusFilter === 'active') {
                $query->whereIn('status', ['pending', 'confirmed', 'assigned', 'in_progress']);
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('booking_number', 'like', $term)
                  ->orWhereHas('package', fn ($p) => $p->where('name', 'like', $term))
                  ->orWhereHas('service', fn ($s) => $s->where('name', 'like', $term));
            });
        }

        return view('livewire.customer.my-bookings', [
            'bookings' => $query->paginate(10),
            'counts' => $counts,
        ]);
    }
}
