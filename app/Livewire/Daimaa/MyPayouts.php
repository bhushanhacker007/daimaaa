<?php

namespace App\Livewire\Daimaa;

use App\Models\BookingSession;
use App\Models\Payout;
use Livewire\Component;
use Livewire\WithPagination;

class MyPayouts extends Component
{
    use WithPagination;

    public string $tab = 'overview'; // overview, sessions, payouts

    public function render()
    {
        $daimaaId = auth()->id();

        // Earnings stats
        $totalEarned = BookingSession::where('daimaa_id', $daimaaId)
            ->where('status', 'completed')
            ->whereNotNull('earning_amount')
            ->sum('earning_amount');

        $thisWeekEarned = BookingSession::where('daimaa_id', $daimaaId)
            ->where('status', 'completed')
            ->whereNotNull('earning_amount')
            ->where('completed_at', '>=', now()->startOfWeek())
            ->sum('earning_amount');

        $thisMonthEarned = BookingSession::where('daimaa_id', $daimaaId)
            ->where('status', 'completed')
            ->whereNotNull('earning_amount')
            ->where('completed_at', '>=', now()->startOfMonth())
            ->sum('earning_amount');

        $completedSessions = BookingSession::where('daimaa_id', $daimaaId)
            ->where('status', 'completed')
            ->count();

        $totalPaidOut = Payout::where('daimaa_id', $daimaaId)
            ->where('status', 'processed')
            ->sum('amount');

        $pendingPayout = Payout::where('daimaa_id', $daimaaId)
            ->where('status', 'pending')
            ->sum('amount');

        $balanceDue = max(0, $totalEarned - $totalPaidOut);

        // Session-level earnings (paginated)
        $sessionEarnings = BookingSession::where('daimaa_id', $daimaaId)
            ->where('status', 'completed')
            ->whereNotNull('earning_amount')
            ->with(['service', 'booking.customer', 'booking.service', 'booking.package'])
            ->latest('completed_at')
            ->paginate(10, ['*'], 'sessionsPage');

        // Weekly breakdown (last 8 weeks)
        $weeklyBreakdown = [];
        for ($i = 0; $i < 8; $i++) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();

            $weekEarnings = BookingSession::where('daimaa_id', $daimaaId)
                ->where('status', 'completed')
                ->whereNotNull('earning_amount')
                ->whereBetween('completed_at', [$weekStart, $weekEnd])
                ->sum('earning_amount');

            $weekSessions = BookingSession::where('daimaa_id', $daimaaId)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$weekStart, $weekEnd])
                ->count();

            $weeklyBreakdown[] = [
                'label' => $weekStart->format('d M') . ' – ' . $weekEnd->format('d M'),
                'amount' => (float) $weekEarnings,
                'sessions' => $weekSessions,
                'start' => $weekStart->format('Y-m-d'),
            ];
        }

        // Payout history
        $payouts = Payout::where('daimaa_id', $daimaaId)
            ->latest()
            ->paginate(10, ['*'], 'payoutsPage');

        return view('livewire.daimaa.my-payouts', [
            'totalEarned' => $totalEarned,
            'thisWeekEarned' => $thisWeekEarned,
            'thisMonthEarned' => $thisMonthEarned,
            'completedSessions' => $completedSessions,
            'totalPaidOut' => $totalPaidOut,
            'pendingPayout' => $pendingPayout,
            'balanceDue' => $balanceDue,
            'sessionEarnings' => $sessionEarnings,
            'weeklyBreakdown' => $weeklyBreakdown,
            'payouts' => $payouts,
        ]);
    }
}
