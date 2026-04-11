<?php

namespace App\Services;

use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\BookingSession;
use App\Models\DaimaaServiceQualification;
use App\Models\Payout;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DaimaaMatchingService
{
    /**
     * Find and rank best Daimaa matches for a booking.
     * Returns a collection of ['daimaa' => User, 'score' => float, 'breakdown' => array].
     */
    public static function findBestMatches(Booking $booking): Collection
    {
        $booking->loadMissing(['address', 'service', 'package.services']);

        $eligible = self::getEligibleDaimaas($booking);

        if ($eligible->isEmpty()) {
            return collect();
        }

        $scored = $eligible->map(fn (User $daimaa) => [
            'daimaa' => $daimaa,
            ...self::scoreCandidate($daimaa, $booking),
        ]);

        return $scored
            ->sortByDesc('score')
            ->take(config('daimaa_matching.max_candidates', 5))
            ->values();
    }

    /**
     * Step 1: Hard Filtering — returns Daimaas who CAN do the job.
     */
    protected static function getEligibleDaimaas(Booking $booking): Collection
    {
        $requiredServiceIds = self::getRequiredServiceIds($booking);
        $customerPincode = $booking->address?->pincode;
        $scheduledAt = self::getBookingScheduledAt($booking);

        $daimaas = User::where('role', 'daimaa')
            ->whereHas('daimaaProfile', fn ($q) => $q->where('status', 'verified')->where('is_online', true))
            ->with(['daimaaProfile', 'serviceQualifications'])
            ->get();

        return $daimaas->filter(function (User $daimaa) use ($requiredServiceIds, $customerPincode, $scheduledAt, $booking) {
            // 1. Skill match: qualified for ALL required services
            $qualifiedIds = $daimaa->serviceQualifications
                ->where('is_qualified', true)
                ->pluck('service_id')
                ->toArray();

            foreach ($requiredServiceIds as $sid) {
                if (!in_array($sid, $qualifiedIds)) {
                    return false;
                }
            }

            // 2. Geo-fence: pincode in service area
            $serviceAreaPincodes = $daimaa->daimaaProfile?->service_area_pincodes ?? [];
            if (!empty($serviceAreaPincodes) && $customerPincode) {
                if (!in_array($customerPincode, $serviceAreaPincodes)) {
                    // If lat/lng available, check distance as fallback
                    if (!self::isWithinRadius($daimaa, $booking)) {
                        return false;
                    }
                }
            }

            // 3. Calendar availability
            if ($scheduledAt && !self::isAvailable($daimaa, $scheduledAt, $booking)) {
                return false;
            }

            return true;
        });
    }

    /**
     * Extract all service IDs required for the booking.
     */
    protected static function getRequiredServiceIds(Booking $booking): array
    {
        if ($booking->service_id) {
            return [$booking->service_id];
        }

        if ($booking->package_id && $booking->package) {
            return $booking->package->services->pluck('id')->toArray();
        }

        return [];
    }

    /**
     * Resolve the primary scheduled datetime for the booking.
     */
    protected static function getBookingScheduledAt(Booking $booking): ?Carbon
    {
        if ($booking->scheduled_date && $booking->scheduled_time) {
            return Carbon::parse($booking->scheduled_date->format('Y-m-d') . ' ' . $booking->scheduled_time);
        }

        if ($booking->scheduled_date) {
            return Carbon::parse($booking->scheduled_date)->setHour(9);
        }

        return null;
    }

    /**
     * Check if daimaa's home is within max_radius_km of the customer address.
     */
    protected static function isWithinRadius(User $daimaa, Booking $booking): bool
    {
        $profile = $daimaa->daimaaProfile;
        $address = $booking->address;

        if (!$profile?->home_latitude || !$address?->latitude) {
            return true; // Can't determine — don't exclude
        }

        $distance = GeocodingService::haversineKm(
            (float) $profile->home_latitude,
            (float) $profile->home_longitude,
            (float) $address->latitude,
            (float) $address->longitude
        );

        return $distance <= config('daimaa_matching.max_radius_km', 10);
    }

    /**
     * Check calendar: availability slot exists AND no conflicting session.
     */
    protected static function isAvailable(User $daimaa, Carbon $scheduledAt, Booking $booking): bool
    {
        $dayOfWeek = $scheduledAt->dayOfWeek;
        $time = $scheduledAt->format('H:i:s');

        $hasSlot = AvailabilitySlot::where('daimaa_id', $daimaa->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>=', $time)
            ->exists();

        // If no availability slots configured, assume available (new daimaa)
        $slotsConfigured = AvailabilitySlot::where('daimaa_id', $daimaa->id)->exists();
        if ($slotsConfigured && !$hasSlot) {
            return false;
        }

        // Check for conflicting sessions (service duration + travel buffer)
        $durationMinutes = $booking->service?->duration_minutes ?? 60;
        $bufferMinutes = config('daimaa_matching.travel_buffer_minutes', 30);
        $totalBlockMinutes = $durationMinutes + $bufferMinutes;

        $windowStart = $scheduledAt->copy()->subMinutes($totalBlockMinutes);
        $windowEnd = $scheduledAt->copy()->addMinutes($totalBlockMinutes);

        $hasConflict = BookingSession::where('daimaa_id', $daimaa->id)
            ->whereIn('status', ['upcoming', 'scheduled', 'started'])
            ->where('scheduled_at', '>=', $windowStart)
            ->where('scheduled_at', '<=', $windowEnd)
            ->exists();

        return !$hasConflict;
    }

    /**
     * Step 2: Score a single candidate on four weighted factors.
     */
    protected static function scoreCandidate(User $daimaa, Booking $booking): array
    {
        $weights = config('daimaa_matching.weights');
        $totalWeight = array_sum($weights);

        $qualityRaw = self::calcQualityScore($daimaa);
        $distanceRaw = self::calcDistanceScore($daimaa, $booking);
        $reliabilityRaw = self::calcReliabilityScore($daimaa);
        $fairnessRaw = self::calcFairnessScore($daimaa);

        $breakdown = [
            'quality' => round($qualityRaw, 1),
            'distance' => round($distanceRaw, 1),
            'reliability' => round($reliabilityRaw, 1),
            'fairness' => round($fairnessRaw, 1),
        ];

        $weighted = (
            $qualityRaw * ($weights['quality'] / $totalWeight) +
            $distanceRaw * ($weights['distance'] / $totalWeight) +
            $reliabilityRaw * ($weights['reliability'] / $totalWeight) +
            $fairnessRaw * ($weights['fairness'] / $totalWeight)
        );

        return [
            'score' => round($weighted, 2),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Quality: avg rating mapped to 0-100, penalized if fewer than N reviews.
     */
    protected static function calcQualityScore(User $daimaa): float
    {
        $reviews = Review::where('daimaa_id', $daimaa->id)->where('is_published', true);
        $count = $reviews->count();
        $avgRating = $count > 0 ? (float) $reviews->avg('rating') : 3.0;

        // Map 1-5 rating to 0-100
        $score = (($avgRating - 1) / 4) * 100;

        // Penalize if too few reviews (linear ramp up to min threshold)
        $minReviews = config('daimaa_matching.min_reviews_for_full_quality', 5);
        if ($count < $minReviews) {
            $score *= ($count / $minReviews);
        }

        return min(100, max(0, $score));
    }

    /**
     * Distance: 0 km = 100, max_radius_km = 0, linear interpolation.
     * Prefers last-completed-session location, falls back to home lat/lng.
     */
    protected static function calcDistanceScore(User $daimaa, Booking $booking): float
    {
        $customerLat = $booking->address?->latitude;
        $customerLng = $booking->address?->longitude;

        if (!$customerLat || !$customerLng) {
            return 50; // Unknown — neutral score
        }

        // Try last completed session's customer address
        $lastSession = BookingSession::where('daimaa_id', $daimaa->id)
            ->where('status', 'completed')
            ->latest('completed_at')
            ->with('booking.address')
            ->first();

        $daimaaLat = $lastSession?->booking?->address?->latitude;
        $daimaaLng = $lastSession?->booking?->address?->longitude;

        // Fall back to home coordinates
        if (!$daimaaLat || !$daimaaLng) {
            $daimaaLat = $daimaa->daimaaProfile?->home_latitude;
            $daimaaLng = $daimaa->daimaaProfile?->home_longitude;
        }

        if (!$daimaaLat || !$daimaaLng) {
            return 50;
        }

        $distance = GeocodingService::haversineKm(
            (float) $daimaaLat, (float) $daimaaLng,
            (float) $customerLat, (float) $customerLng
        );

        $maxKm = config('daimaa_matching.max_radius_km', 10);

        return max(0, min(100, (1 - ($distance / $maxKm)) * 100));
    }

    /**
     * Reliability: directly from profile's 0-100 score.
     */
    protected static function calcReliabilityScore(User $daimaa): float
    {
        return (float) ($daimaa->daimaaProfile?->reliability_score ?? 100);
    }

    /**
     * Fairness: inverse of this week's earnings relative to the peer average.
     * Daimaas who earned less get a boost.
     */
    protected static function calcFairnessScore(User $daimaa): float
    {
        $weekStart = now()->startOfWeek();

        // This Daimaa's weekly completed sessions
        $daimaaSessionCount = BookingSession::where('daimaa_id', $daimaa->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $weekStart)
            ->count();

        // Peer average weekly sessions (all active daimaas)
        $allDaimaaIds = User::where('role', 'daimaa')
            ->whereHas('daimaaProfile', fn ($q) => $q->where('status', 'verified'))
            ->pluck('id');

        if ($allDaimaaIds->count() <= 1) {
            return 75; // Single daimaa — neutral-high
        }

        $totalPeerSessions = BookingSession::whereIn('daimaa_id', $allDaimaaIds)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $weekStart)
            ->count();

        $peerAvg = $totalPeerSessions / $allDaimaaIds->count();

        if ($peerAvg == 0) {
            return 75;
        }

        // Ratio: if daimaa did less than average, score > 50; if more, score < 50
        $ratio = $daimaaSessionCount / $peerAvg;

        // Invert: ratio of 0 → 100, ratio of 2+ → 0
        return max(0, min(100, (1 - ($ratio / 2)) * 100));
    }
}
