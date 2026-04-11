<?php

namespace App\Services;

use App\Models\BookingSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RouteOptimizerService
{
    /**
     * Get optimally-ordered sessions for a Daimaa on a given date.
     * Uses nearest-neighbor greedy algorithm starting from home location.
     *
     * Returns collection of BookingSession models in visit order,
     * with an added `route_order` attribute.
     */
    public static function optimizeRoute(int $daimaaId, ?Carbon $date = null): Collection
    {
        $date = $date ?? today();

        $sessions = BookingSession::where('daimaa_id', $daimaaId)
            ->whereIn('status', ['upcoming', 'scheduled', 'started'])
            ->whereDate('scheduled_at', $date)
            ->with(['booking.address', 'booking.customer', 'booking.service', 'booking.package', 'service'])
            ->orderBy('scheduled_at')
            ->get();

        if ($sessions->count() <= 1) {
            return $sessions->map(function ($s, $i) {
                $s->route_order = $i + 1;
                return $s;
            });
        }

        // Get Daimaa home coordinates
        $daimaa = User::with('daimaaProfile')->find($daimaaId);
        $homeLat = (float) ($daimaa?->daimaaProfile?->home_latitude ?? 0);
        $homeLng = (float) ($daimaa?->daimaaProfile?->home_longitude ?? 0);

        // If we have timestamps, respect strict time windows — only optimize "flexible" sessions
        // For simplicity: if sessions have specific times set, sort by time; otherwise, use nearest-neighbor
        $allHaveTimes = $sessions->every(fn ($s) => $s->scheduled_at !== null);

        if ($allHaveTimes && $homeLat == 0) {
            // No geo data — just return time-sorted
            return $sessions->sortBy('scheduled_at')->values()->map(function ($s, $i) {
                $s->route_order = $i + 1;
                return $s;
            });
        }

        // Nearest-neighbor: start from home, pick closest unvisited each step
        $ordered = collect();
        $remaining = $sessions->keyBy('id');
        $currentLat = $homeLat;
        $currentLng = $homeLng;

        while ($remaining->isNotEmpty()) {
            $nearest = null;
            $nearestDist = PHP_FLOAT_MAX;

            foreach ($remaining as $session) {
                $sLat = (float) ($session->booking?->address?->latitude ?? 0);
                $sLng = (float) ($session->booking?->address?->longitude ?? 0);

                if ($sLat == 0 && $sLng == 0) {
                    // No coordinates — keep original position
                    $dist = 0;
                } else {
                    $dist = GeocodingService::haversineKm($currentLat, $currentLng, $sLat, $sLng);
                }

                if ($dist < $nearestDist) {
                    $nearestDist = $dist;
                    $nearest = $session;
                }
            }

            if ($nearest) {
                $ordered->push($nearest);
                $remaining->forget($nearest->id);
                $currentLat = (float) ($nearest->booking?->address?->latitude ?? $currentLat);
                $currentLng = (float) ($nearest->booking?->address?->longitude ?? $currentLng);
            } else {
                break;
            }
        }

        return $ordered->values()->map(function ($s, $i) {
            $s->route_order = $i + 1;
            return $s;
        });
    }

    /**
     * Generate a Google Maps multi-stop directions URL.
     */
    public static function getGoogleMapsUrl(Collection $sessions, ?float $homeLat = null, ?float $homeLng = null): string
    {
        $waypoints = [];

        // Start from home if available
        if ($homeLat && $homeLng) {
            $waypoints[] = "{$homeLat},{$homeLng}";
        }

        foreach ($sessions as $session) {
            $lat = $session->booking?->address?->latitude;
            $lng = $session->booking?->address?->longitude;

            if ($lat && $lng) {
                $waypoints[] = "{$lat},{$lng}";
            } else {
                $addr = $session->booking?->address;
                if ($addr) {
                    $waypoints[] = urlencode("{$addr->address_line_1}, {$addr->pincode}");
                }
            }
        }

        if (count($waypoints) < 2) {
            return '#';
        }

        $origin = array_shift($waypoints);
        $destination = array_pop($waypoints);
        $waypointsStr = implode('|', $waypoints);

        $url = "https://www.google.com/maps/dir/?api=1&origin={$origin}&destination={$destination}";
        if (!empty($waypointsStr)) {
            $url .= "&waypoints={$waypointsStr}";
        }
        $url .= '&travelmode=driving';

        return $url;
    }
}
