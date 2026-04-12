<?php

namespace App\Livewire\Admin;

use App\Models\DaimaaProfile;
use App\Models\Document;
use App\Models\PoliceVerification;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageDaimaas extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $statusFilter = 'all';
    public string $kycFilter = 'all';

    // Detail slide-over
    public bool $showDetail = false;
    public ?int $selectedDaimaaId = null;

    // Police verification
    public bool $showPoliceModal = false;
    public string $pvStatus = 'initiated';
    public string $pvReferenceNumber = '';
    public string $pvAgencyName = '';
    public string $pvNotes = '';
    public $pvReport = null;
    public ?string $pvExpiryDate = null;

    // Action modals
    public bool $showSuspendModal = false;
    public bool $showRejectModal = false;
    public string $actionReason = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function viewDaimaa(int $userId): void
    {
        $this->selectedDaimaaId = $userId;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedDaimaaId = null;
    }

    public function getSelectedDaimaaProperty(): ?User
    {
        if (!$this->selectedDaimaaId) return null;
        return User::with(['daimaaProfile.documents', 'daimaaProfile.policeVerifications', 'daimaaProfile.latestPoliceVerification'])
            ->find($this->selectedDaimaaId);
    }

    // ── Status Actions ──

    public function verifyDaimaa(): void
    {
        $profile = DaimaaProfile::where('user_id', $this->selectedDaimaaId)->first();
        if ($profile) {
            $profile->update(['status' => 'verified', 'verified_at' => now()]);
            session()->flash('toast', 'Daimaa verified successfully.');
        }
    }

    public function openSuspendModal(): void { $this->showSuspendModal = true; $this->actionReason = ''; }
    public function openRejectModal(): void { $this->showRejectModal = true; $this->actionReason = ''; }

    public function confirmSuspend(): void
    {
        $profile = DaimaaProfile::where('user_id', $this->selectedDaimaaId)->first();
        if ($profile) {
            $profile->update(['status' => 'suspended']);
            $this->showSuspendModal = false;
            session()->flash('toast', 'Daimaa suspended.');
        }
    }

    public function confirmReject(): void
    {
        $profile = DaimaaProfile::where('user_id', $this->selectedDaimaaId)->first();
        if ($profile) {
            $profile->update(['status' => 'rejected']);
            $this->showRejectModal = false;
            session()->flash('toast', 'Daimaa rejected.');
        }
    }

    // ── Police Verification ──

    public function openPoliceModal(): void
    {
        $this->showPoliceModal = true;
        $this->pvStatus = 'initiated';
        $this->pvReferenceNumber = '';
        $this->pvAgencyName = '';
        $this->pvNotes = '';
        $this->pvReport = null;
        $this->pvExpiryDate = null;
    }

    public function savePoliceVerification(): void
    {
        $profile = DaimaaProfile::where('user_id', $this->selectedDaimaaId)->first();
        if (!$profile) return;

        $reportPath = null;
        if ($this->pvReport) {
            $reportPath = $this->pvReport->store('police-reports', 'local');
        }

        PoliceVerification::create([
            'daimaa_profile_id' => $profile->id,
            'status' => $this->pvStatus,
            'initiated_by' => auth()->id(),
            'initiated_at' => now(),
            'cleared_at' => $this->pvStatus === 'cleared' ? now() : null,
            'expiry_date' => $this->pvExpiryDate ?: ($this->pvStatus === 'cleared' ? now()->addYear() : null),
            'reference_number' => $this->pvReferenceNumber ?: null,
            'agency_name' => $this->pvAgencyName ?: null,
            'report_file_path' => $reportPath,
            'notes' => $this->pvNotes ?: null,
        ]);

        $this->showPoliceModal = false;
        session()->flash('toast', 'Police verification record saved.');
    }

    public function updatePoliceStatus(int $pvId, string $newStatus): void
    {
        $pv = PoliceVerification::find($pvId);
        if (!$pv) return;

        $pv->update([
            'status' => $newStatus,
            'cleared_at' => $newStatus === 'cleared' ? now() : $pv->cleared_at,
            'expiry_date' => $newStatus === 'cleared' ? now()->addYear() : $pv->expiry_date,
        ]);

        session()->flash('toast', 'Police verification status updated.');
    }

    // ── Document Actions ──

    public function approveDocument(int $docId): void
    {
        Document::where('id', $docId)->update(['status' => 'approved']);
    }

    public function rejectDocument(int $docId): void
    {
        Document::where('id', $docId)->update(['status' => 'rejected']);
    }

    public function render()
    {
        $query = User::where('role', 'daimaa')
            ->with(['daimaaProfile', 'daimaaProfile.latestPoliceVerification']);

        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhereHas('daimaaProfile', fn ($pq) => $pq->whereJsonContains('service_area_pincodes', $s));
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->whereHas('daimaaProfile', fn ($q) => $q->where('status', $this->statusFilter));
        }

        $daimaas = $query->latest()->paginate(15);

        // Stats
        $totalCount = User::where('role', 'daimaa')->count();
        $verifiedCount = DaimaaProfile::where('status', 'verified')->count();
        $pendingCount = DaimaaProfile::where('status', 'pending')->count();
        $onlineCount = DaimaaProfile::where('status', 'verified')->where('is_online', true)->count();

        return view('livewire.admin.manage-daimaas', compact(
            'daimaas', 'totalCount', 'verifiedCount', 'pendingCount', 'onlineCount'
        ));
    }
}
