<?php

namespace App\Livewire\Admin;

use App\Models\DaimaaProfile;
use Livewire\Component;
use Livewire\WithPagination;

class KycReview extends Component
{
    use WithPagination;

    public string $filter = 'pending';

    public function approve(int $profileId)
    {
        $profile = DaimaaProfile::findOrFail($profileId);
        $profile->update(['status' => 'verified', 'verified_at' => now()]);
        $profile->documents()->update(['status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
    }

    public function reject(int $profileId)
    {
        $profile = DaimaaProfile::findOrFail($profileId);
        $profile->update(['status' => 'rejected']);
        $profile->documents()->update(['status' => 'rejected', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
    }

    public function render()
    {
        $profiles = DaimaaProfile::with(['user', 'documents'])
            ->when($this->filter !== 'all', fn ($q) => $q->where('status', $this->filter))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.kyc-review', ['profiles' => $profiles]);
    }
}
