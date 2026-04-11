<?php

namespace App\Livewire\Admin;

use App\Models\BookingSession;
use App\Models\Payout;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ManagePayouts extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $daimaaFilter = '';

    public ?string $generateWeek = null;
    public bool $showGenerateModal = false;
    public array $previewData = [];

    public function mount()
    {
        $this->generateWeek = now()->subWeek()->startOfWeek()->format('Y-m-d');
    }

    public function openGenerateModal()
    {
        $this->previewData = $this->calculatePayoutPreview();
        $this->showGenerateModal = true;
    }

    public function closeGenerateModal()
    {
        $this->showGenerateModal = false;
        $this->previewData = [];
    }

    protected function calculatePayoutPreview(): array
    {
        $weekStart = Carbon::parse($this->generateWeek)->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        $period = $weekStart->format('Y') . '-W' . $weekStart->format('W');

        $daimaas = User::where('role', 'daimaa')
            ->whereHas('daimaaProfile', fn ($q) => $q->where('status', 'verified'))
            ->get();

        $preview = [];

        foreach ($daimaas as $daimaa) {
            // Check if payout already exists for this period
            $existingPayout = Payout::where('daimaa_id', $daimaa->id)
                ->where('period', $period)
                ->exists();

            if ($existingPayout) continue;

            $sessions = BookingSession::where('daimaa_id', $daimaa->id)
                ->where('status', 'completed')
                ->whereNotNull('earning_amount')
                ->whereBetween('completed_at', [$weekStart, $weekEnd])
                ->get();

            if ($sessions->isEmpty()) continue;

            $totalEarning = $sessions->sum('earning_amount');

            $preview[] = [
                'daimaa_id' => $daimaa->id,
                'daimaa_name' => $daimaa->name,
                'sessions_count' => $sessions->count(),
                'amount' => (float) $totalEarning,
                'period' => $period,
                'period_start' => $weekStart->format('Y-m-d'),
                'period_end' => $weekEnd->format('Y-m-d'),
            ];
        }

        return $preview;
    }

    public function generatePayouts()
    {
        if (empty($this->previewData)) {
            session()->flash('error', 'No payouts to generate.');
            $this->closeGenerateModal();
            return;
        }

        $count = 0;
        foreach ($this->previewData as $data) {
            Payout::create([
                'daimaa_id' => $data['daimaa_id'],
                'amount' => $data['amount'],
                'period' => $data['period'],
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'sessions_count' => $data['sessions_count'],
                'status' => 'pending',
            ]);
            $count++;
        }

        session()->flash('success', "{$count} payout(s) generated successfully.");
        $this->closeGenerateModal();
    }

    public function markProcessed(int $payoutId)
    {
        $payout = Payout::findOrFail($payoutId);
        $payout->update([
            'status' => 'processed',
            'processed_at' => now(),
            'reference' => 'MANUAL-' . strtoupper(substr(md5(now() . $payoutId), 0, 8)),
        ]);

        session()->flash('success', "Payout #{$payoutId} marked as processed.");
    }

    public function markFailed(int $payoutId)
    {
        $payout = Payout::findOrFail($payoutId);
        $payout->update(['status' => 'failed']);
    }

    public function render()
    {
        $query = Payout::with('daimaa')->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->daimaaFilter) {
            $query->where('daimaa_id', $this->daimaaFilter);
        }

        $daimaas = User::where('role', 'daimaa')
            ->whereHas('daimaaProfile')
            ->orderBy('name')
            ->get();

        // Summary stats
        $totalPending = Payout::where('status', 'pending')->sum('amount');
        $totalProcessed = Payout::where('status', 'processed')->sum('amount');
        $totalPayouts = Payout::count();

        return view('livewire.admin.manage-payouts', [
            'payouts' => $query->paginate(15),
            'daimaas' => $daimaas,
            'totalPending' => $totalPending,
            'totalProcessed' => $totalProcessed,
            'totalPayouts' => $totalPayouts,
        ]);
    }
}
