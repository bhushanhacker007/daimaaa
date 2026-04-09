<?php

namespace App\Services;

use App\Models\Pincode;

class PincodeService
{
    public static function isServiceable(string $pincode): bool
    {
        return Pincode::where('pincode', $pincode)->where('is_serviceable', true)->exists();
    }

    public static function getCity(string $pincode): ?string
    {
        $pin = Pincode::with('city')->where('pincode', $pincode)->first();
        return $pin?->city?->name;
    }
}
