<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Geocode a full address string into [latitude, longitude].
     * Uses OpenStreetMap Nominatim (free, no API key).
     * Returns null on failure — caller should fall back to pincode matching.
     */
    public static function geocode(string $address, string $pincode = '', string $city = ''): ?array
    {
        $query = trim("{$address}, {$city}, {$pincode}, India");
        $cacheKey = 'geocode:' . md5($query);

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        // Rate-limit: max 1 request per second per Nominatim ToS
        $lockKey = 'geocode_rate_lock';
        $lock = Cache::lock($lockKey, 2);

        try {
            $lock->block(5);

            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Daimaaa/1.0 (contact@daimaaa.com)'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'in',
                ]);

            if ($response->successful() && $response->json()) {
                $result = $response->json()[0] ?? null;
                if ($result && isset($result['lat'], $result['lon'])) {
                    $coords = [
                        'latitude' => round((float) $result['lat'], 7),
                        'longitude' => round((float) $result['lon'], 7),
                    ];
                    Cache::put($cacheKey, $coords, now()->addDays(30));
                    return $coords;
                }
            }

            // Fallback: try pincode alone
            if ($pincode) {
                return self::geocodePincode($pincode);
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Geocoding failed', ['query' => $query, 'error' => $e->getMessage()]);
            return $pincode ? self::geocodePincode($pincode) : null;
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Geocode using just a pincode — less precise but reliable fallback.
     */
    public static function geocodePincode(string $pincode): ?array
    {
        $cacheKey = 'geocode_pin:' . $pincode;

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Daimaaa/1.0 (contact@daimaaa.com)'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'postalcode' => $pincode,
                    'country' => 'India',
                    'format' => 'json',
                    'limit' => 1,
                ]);

            if ($response->successful() && $response->json()) {
                $result = $response->json()[0] ?? null;
                if ($result && isset($result['lat'], $result['lon'])) {
                    $coords = [
                        'latitude' => round((float) $result['lat'], 7),
                        'longitude' => round((float) $result['lon'], 7),
                    ];
                    Cache::put($cacheKey, $coords, now()->addDays(90));
                    return $coords;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Pincode geocoding failed', ['pincode' => $pincode, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Haversine distance between two points in kilometers.
     */
    public static function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
