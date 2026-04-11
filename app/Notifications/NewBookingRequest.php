<?php

namespace App\Notifications;

use App\Models\BookingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingRequest extends Notification
{
    use Queueable;

    public function __construct(
        public BookingAssignment $assignment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $booking = $this->assignment->booking;
        $serviceName = $booking?->service?->name ?? $booking?->package?->name ?? 'Service';

        return [
            'type' => 'new_booking_request',
            'assignment_id' => $this->assignment->id,
            'booking_id' => $booking?->id,
            'booking_number' => $booking?->booking_number,
            'service_name' => $serviceName,
            'scheduled_date' => $booking?->scheduled_date?->format('d M Y'),
            'scheduled_time' => $booking?->scheduled_time,
            'is_instant' => $booking?->is_instant,
            'expires_at' => $this->assignment->expires_at?->toIso8601String(),
            'message' => "Aapke liye naya booking request — {$serviceName}",
        ];
    }
}
