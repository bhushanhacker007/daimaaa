<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Coupon;

class BookingService
{
    public static function validateCoupon(string $code, float $subtotal, int $userId): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();
        if (! $coupon) return ['valid' => false, 'message' => 'Invalid coupon code.'];
        if (! $coupon->is_active) return ['valid' => false, 'message' => 'This coupon is no longer active.'];
        if ($coupon->valid_from && now()->lt($coupon->valid_from)) return ['valid' => false, 'message' => 'This coupon is not yet valid.'];
        if ($coupon->valid_until && now()->gt($coupon->valid_until)) return ['valid' => false, 'message' => 'This coupon has expired.'];
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) return ['valid' => false, 'message' => 'This coupon has reached its usage limit.'];
        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) return ['valid' => false, 'message' => 'Minimum order amount is ₹' . number_format($coupon->min_order_amount) . '.'];

        $discount = $coupon->type === 'percent'
            ? $subtotal * ($coupon->value / 100)
            : $coupon->value;
        if ($coupon->max_discount) $discount = min($discount, $coupon->max_discount);

        return ['valid' => true, 'discount' => $discount, 'coupon' => $coupon, 'message' => 'Coupon applied! You save ₹' . number_format($discount)];
    }

    public static function updateStatus(Booking $booking, string $newStatus, ?int $changedBy = null, ?string $notes = null): void
    {
        $oldStatus = $booking->status;
        $booking->update([
            'status' => $newStatus,
            'confirmed_at' => $newStatus === 'confirmed' ? now() : $booking->confirmed_at,
            'completed_at' => $newStatus === 'completed' ? now() : $booking->completed_at,
            'cancelled_at' => $newStatus === 'cancelled' ? now() : $booking->cancelled_at,
        ]);

        $booking->statusHistories()->create([
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'changed_by' => $changedBy ?? auth()->id(),
            'notes' => $notes,
        ]);

        AuditService::log('status_changed', $booking, ['status' => $oldStatus], ['status' => $newStatus]);
    }
}
