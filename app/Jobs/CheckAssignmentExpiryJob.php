<?php

namespace App\Jobs;

use App\Models\BookingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckAssignmentExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public int $assignmentId,
    ) {}

    public function handle(): void
    {
        $assignment = BookingAssignment::with(['booking', 'daimaa.daimaaProfile'])->find($this->assignmentId);

        if (!$assignment) {
            return;
        }

        // Already handled (accepted or declined by the daimaa)
        if ($assignment->dispatch_status !== 'pending') {
            return;
        }

        // Mark expired
        $assignment->update(['dispatch_status' => 'expired']);

        // Penalize reliability
        $penalty = config('daimaa_matching.reliability_penalty_timeout', 2);
        $assignment->daimaa?->daimaaProfile?->penalizeReliability($penalty);

        Log::info('Assignment expired', [
            'assignment' => $assignment->id,
            'daimaa' => $assignment->daimaa?->name,
            'booking' => $assignment->booking?->booking_number,
        ]);

        // Cascade to next candidate
        $nextRank = ($assignment->dispatch_rank ?? 0) + 1;

        DispatchDaimaaJob::dispatch($assignment->booking_id, $nextRank);
    }
}
