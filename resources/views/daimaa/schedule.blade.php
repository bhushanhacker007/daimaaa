<x-dashboard-layout>
    <x-slot:title>My Schedule — Daimaa</x-slot:title>
    <x-slot:heading>My Schedule</x-slot:heading>
    <x-slot:sidebar>@include('daimaa._sidebar')</x-slot:sidebar>

    @php $slots = \App\Models\AvailabilitySlot::where('daimaa_id', auth()->id())->orderBy('day_of_week')->get(); @endphp

    <div class="bg-surface-container-lowest rounded-2xl p-6">
        <h3 class="font-headline font-bold text-primary mb-4">Weekly Availability</h3>
        <div class="space-y-3">
            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $i => $day)
            @php $slot = $slots->firstWhere('day_of_week', $i); @endphp
            <div class="flex items-center justify-between p-3 bg-surface-container rounded-xl">
                <span class="font-medium text-on-surface w-28">{{ $day }}</span>
                @if($slot && $slot->is_available)
                <span class="text-sm text-primary font-medium">{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} — {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}</span>
                @else
                <span class="text-sm text-on-surface-variant/50">Unavailable</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</x-dashboard-layout>
