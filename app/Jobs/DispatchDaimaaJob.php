<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\BookingAssignment;
use App\Services\DaimaaMatchingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchDaimaaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public int $bookingId,
        public int $startFromRank = 1,
    ) {}

    public function handle(): void
    {
        $booking = Booking::with(['address', 'service', 'package.services'])->find($this->bookingId);

        if (!$booking || !in_array($booking->status, ['pending', 'confirmed'])) {
            return;
        }

        // Check if already assigned by admin manually
        $activeAssignment = $booking->assignments()
            ->where('dispatch_status', 'accepted')
            ->exists();

        if ($activeAssignment) {
            return;
        }

        $candidates = DaimaaMatchingService::findBestMatches($booking);

        if ($candidates->isEmpty()) {
            self::escalateToAdmin($booking);
            return;
        }

        // Skip candidates we already offered to (previous ranks that declined/expired)
        $alreadyOfferedIds = $booking->assignments()->pluck('daimaa_id')->toArray();
        $remaining = $candidates->filter(fn ($c) => !in_array($c['daimaa']->id, $alreadyOfferedIds));

        if ($remaining->isEmpty()) {
            self::escalateToAdmin($booking);
            return;
        }

        $pick = $remaining->first();
        $rank = $this->startFromRank;
        $isInstant = $booking->is_instant;

        $windowMinutes = $isInstant
            ? config('daimaa_matching.instant_accept_window_minutes', 5)
            : config('daimaa_matching.accept_window_minutes', 15);

        $assignment = BookingAssignment::create([
            'booking_id' => $booking->id,
            'daimaa_id' => $pick['daimaa']->id,
            'assigned_by' => null, // system-assigned
            'assigned_at' => now(),
            'match_score' => $pick['score'],
            'score_breakdown' => $pick['breakdown'],
            'dispatch_rank' => $rank,
            'expires_at' => now()->addMinutes($windowMinutes),
            'dispatch_status' => 'pending',
        ]);

        $pick['daimaa']->daimaaProfile?->recordAssignment();

        // Send database notification
        $pick['daimaa']->notify(new \App\Notifications\NewBookingRequest($assignment));

        // Schedule expiry check
        CheckAssignmentExpiryJob::dispatch($assignment->id)
            ->delay(now()->addMinutes($windowMinutes));

        Log::info('Daimaa auto-dispatch', [
            'booking' => $booking->booking_number,
            'daimaa' => $pick['daimaa']->name,
            'score' => $pick['score'],
            'rank' => $rank,
        ]);
    }

    protected static function escalateToAdmin(Booking $booking): void
    {
        if ($booking->status !== 'needs_manual_assignment') {
            $oldStatus = $booking->status;
            $booking->update(['status' => 'needs_manual_assignment']);
            $booking->statusHistories()->create([
                'from_status' => $oldStatus,
                'to_status' => 'needs_manual_assignment',
                'changed_by' => null,
                'notes' => 'Auto-dispatch exhausted all candidates. Needs manual assignment.',
            ]);
        }

        Log::warning('Auto-dispatch failed — escalated to admin', [
            'booking' => $booking->booking_number,
        ]);
    }
}
