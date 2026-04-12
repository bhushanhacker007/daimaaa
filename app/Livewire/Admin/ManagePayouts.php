<?php

namespace App\Livewire\Admin;

use App\Models\BookingSession;
use App\Models\Payout;
use App\Models\User;
use App\Services\CashfreeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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

    public function processCashfreePayout(int $payoutId)
    {
        $payout = Payout::with('daimaa.daimaaProfile')->findOrFail($payoutId);
        $profile = $payout->daimaa?->daimaaProfile;

        if (!$profile || !$profile->bank_account_number) {
            session()->flash('error', 'Daimaa bank details missing. Cannot process payout.');
            return;
        }

        try {
            $service = app(CashfreeService::class);

            if (!$profile->cashfree_beneficiary_id) {
                $beneResult = $service->addBeneficiary($profile);
                if (!$beneResult['success']) {
                    session()->flash('error', 'Failed to add beneficiary: ' . ($beneResult['message'] ?? 'Unknown error'));
                    return;
                }
            }

            $result = $service->initiateTransfer($payout);

            if ($result['success']) {
                session()->flash('success', "Payout #{$payoutId} submitted to Cashfree. Transfer ID: " . $result['transfer_id']);
            } else {
                session()->flash('error', 'Transfer failed: ' . ($result['message'] ?? 'Unknown error'));
            }
        } catch (\Throwable $e) {
            Log::error('Cashfree payout error', ['payout_id' => $payoutId, 'error' => $e->getMessage()]);
            session()->flash('error', 'Payout processing failed: ' . $e->getMessage());
        }
    }

    public function markProcessedManual(int $payoutId)
    {
        $payout = Payout::findOrFail($payoutId);
        $payout->update([
            'status' => 'processed',
            'processed_at' => now(),
            'reference' => 'MANUAL-' . strtoupper(substr(md5(now() . $payoutId), 0, 8)),
        ]);

        session()->flash('success', "Payout #{$payoutId} marked as processed (manual).");
    }

    public function checkTransferStatus(int $payoutId)
    {
        $payout = Payout::findOrFail($payoutId);
        if (!$payout->reference || !str_starts_with($payout->reference, 'PAY_')) {
            session()->flash('error', 'No Cashfree transfer reference found.');
            return;
        }

        $service = app(CashfreeService::class);
        $result = $service->getTransferStatus($payout->reference);

        if ($result['success']) {
            $cfStatus = strtolower($result['status'] ?? '');
            $newStatus = match (true) {
                in_array($cfStatus, ['success', 'completed']) => 'processed',
                in_array($cfStatus, ['failed', 'reversed', 'rejected']) => 'failed',
                default => 'processing',
            };

            $payout->update([
                'status' => $newStatus,
                'processed_at' => $newStatus === 'processed' ? now() : $payout->processed_at,
                'notes' => 'CF Status: ' . ($result['status'] ?? '—') . ($result['utr'] ? ' | UTR: ' . $result['utr'] : ''),
            ]);

            session()->flash('success', "Transfer status: {$result['status']}");
        } else {
            session()->flash('error', 'Could not fetch status: ' . ($result['message'] ?? 'Unknown'));
        }
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
