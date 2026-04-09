<x-dashboard-layout>
    <x-slot:title>Daimaa Profile</x-slot:title>
    <x-slot:heading>My Profile</x-slot:heading>
    <x-slot:sidebar>@include('daimaa._sidebar')</x-slot:sidebar>

    @php $profile = Auth::user()->daimaaProfile; @endphp

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest rounded-2xl p-6">
                <h3 class="font-headline font-bold text-primary mb-4">Personal Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-on-surface-variant">Name</p><p class="font-semibold">{{ Auth::user()->name }}</p></div>
                    <div><p class="text-on-surface-variant">Email</p><p class="font-semibold">{{ Auth::user()->email }}</p></div>
                    <div><p class="text-on-surface-variant">Phone</p><p class="font-semibold">{{ Auth::user()->phone ?? 'Not set' }}</p></div>
                    <div><p class="text-on-surface-variant">Experience</p><p class="font-semibold">{{ $profile?->years_of_experience ?? 0 }} years</p></div>
                </div>
            </div>
            @if($profile?->bio)
            <div class="bg-surface-container-lowest rounded-2xl p-6">
                <h3 class="font-headline font-bold text-primary mb-3">About</h3>
                <p class="text-on-surface-variant">{{ $profile->bio }}</p>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-2xl p-6">
                <h3 class="font-headline font-bold text-primary mb-4">Verification Status</h3>
                <div class="flex items-center gap-3">
                    @if($profile?->status === 'verified')
                    <span class="material-symbols-outlined text-primary text-3xl">verified</span>
                    <div>
                        <p class="font-semibold text-primary">Verified</p>
                        <p class="text-xs text-on-surface-variant">Since {{ $profile->verified_at?->format('M d, Y') }}</p>
                    </div>
                    @elseif($profile?->status === 'pending')
                    <span class="material-symbols-outlined text-tertiary text-3xl">hourglass_top</span>
                    <div>
                        <p class="font-semibold text-tertiary">Under Review</p>
                        <p class="text-xs text-on-surface-variant">We're reviewing your documents</p>
                    </div>
                    @else
                    <span class="material-symbols-outlined text-error text-3xl">cancel</span>
                    <div>
                        <p class="font-semibold text-error">{{ ucfirst($profile?->status ?? 'Not submitted') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
