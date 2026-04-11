<?php

namespace App\Livewire\Daimaa;

use App\Models\AvailabilitySlot;
use App\Models\DaimaaProfile;
use Livewire\Component;

class MySchedule extends Component
{
    public bool $isOnline = true;

    public array $days = [];

    protected static array $dayNames = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function mount(): void
    {
        $profile = DaimaaProfile::where('user_id', auth()->id())->first();
        $this->isOnline = (bool) ($profile?->is_online ?? true);

        $slots = AvailabilitySlot::where('daimaa_id', auth()->id())
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        foreach (self::$dayNames as $i => $name) {
            $slot = $slots->get($i);
            $this->days[$i] = [
                'name' => $name,
                'is_available' => (bool) ($slot?->is_available ?? false),
                'start_time' => $slot?->start_time ? substr($slot->start_time, 0, 5) : '08:00',
                'end_time' => $slot?->end_time ? substr($slot->end_time, 0, 5) : '18:00',
            ];
        }
    }

    public function toggleOnline(): void
    {
        $this->isOnline = !$this->isOnline;

        DaimaaProfile::where('user_id', auth()->id())
            ->update(['is_online' => $this->isOnline]);

        session()->flash(
            'schedule-message',
            $this->isOnline
                ? 'Aap ab ONLINE hain — naye booking aayenge.'
                : 'Aap ab OFFLINE hain — koi naya booking nahi aayega.'
        );
    }

    public function toggleDay(int $dayIndex): void
    {
        $this->days[$dayIndex]['is_available'] = !$this->days[$dayIndex]['is_available'];
        $this->saveSlot($dayIndex);
    }

    public function updatedDays($value, $key): void
    {
        // key like "3.start_time" or "5.end_time"
        $parts = explode('.', $key);
        if (count($parts) === 2 && in_array($parts[1], ['start_time', 'end_time'])) {
            $dayIndex = (int) $parts[0];
            $this->saveSlot($dayIndex);
        }
    }

    protected function saveSlot(int $dayIndex): void
    {
        $day = $this->days[$dayIndex];

        AvailabilitySlot::updateOrCreate(
            ['daimaa_id' => auth()->id(), 'day_of_week' => $dayIndex],
            [
                'is_available' => $day['is_available'],
                'start_time' => $day['start_time'] . ':00',
                'end_time' => $day['end_time'] . ':00',
            ]
        );
    }

    public function copyToAll(int $sourceDayIndex): void
    {
        $source = $this->days[$sourceDayIndex];

        foreach ($this->days as $i => &$day) {
            if ($i === $sourceDayIndex) {
                continue;
            }
            $day['is_available'] = $source['is_available'];
            $day['start_time'] = $source['start_time'];
            $day['end_time'] = $source['end_time'];
            $this->saveSlot($i);
        }

        session()->flash('schedule-message', "{$source['name']} ka schedule sabhi dino mein copy ho gaya.");
    }

    public function render()
    {
        return view('livewire.daimaa.my-schedule');
    }
}
