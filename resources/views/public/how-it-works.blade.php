<x-app-layout>
    <x-slot:title>{{ __('How It Works') }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-16 bg-surface">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-headline font-bold text-primary mb-4">{{ __('How It Works') }}</h1>
            <p class="text-lg text-on-surface-variant">{{ __('From booking to bliss in four simple steps') }}</p>
        </div>
    </section>

    <section class="py-16 bg-surface-container-low">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 space-y-16">
            @foreach([
                ['icon' => 'travel_explore', 'title' => __('Browse & Choose'), 'desc' => __('Explore our services, packages, and add-ons. Whether you need a single mother massage or a complete 40-day package, find what suits your needs and budget. Compare options, read descriptions, and select the care that resonates with you.')],
                ['icon' => 'edit_calendar', 'title' => __('Schedule Your Visit'), 'desc' => __('Pick a date and time that works for your family. Enter your address and pincode so we can confirm serviceability in your area. Add any special requests or notes for your Daimaa.')],
                ['icon' => 'how_to_reg', 'title' => __('Meet Your Verified Daimaa'), 'desc' => __('We assign a verified, experienced Daimaa based on your location and service needs. Every Daimaa has been background-checked, KYC-verified, and evaluated for expertise. You can view their profile before the visit.')],
                ['icon' => 'self_improvement', 'title' => __('Relax & Recover'), 'desc' => __('Your Daimaa arrives at your doorstep with all supplies. Enjoy the warmth of traditional care in the comfort of your home. After each session, rate your experience and schedule the next one.')],
            ] as $i => $step)
            <div class="flex gap-8 items-start {{ $loop->even ? 'lg:flex-row-reverse' : '' }}">
                <div class="hidden lg:block shrink-0">
                    <div class="h-24 w-24 bg-surface-container-lowest rounded-full flex items-center justify-center ambient-shadow relative">
                        <span class="material-symbols-outlined text-primary text-4xl">{{ $step['icon'] }}</span>
                        <span class="absolute -top-1 -right-1 h-8 w-8 bg-tertiary text-on-tertiary rounded-full flex items-center justify-center text-sm font-bold">{{ $i + 1 }}</span>
                    </div>
                </div>
                <div class="flex-1 bg-surface-container-lowest rounded-3xl p-8">
                    <div class="lg:hidden flex items-center gap-3 mb-4">
                        <span class="h-8 w-8 bg-tertiary text-on-tertiary rounded-full flex items-center justify-center text-sm font-bold">{{ $i + 1 }}</span>
                        <span class="material-symbols-outlined text-primary text-2xl">{{ $step['icon'] }}</span>
                    </div>
                    <h3 class="text-2xl font-headline font-bold text-primary mb-3">{{ $step['title'] }}</h3>
                    <p class="text-on-surface-variant leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <section class="py-16 bg-surface text-center">
        <div class="max-w-xl mx-auto px-6">
            <h2 class="text-3xl font-headline font-bold text-primary mb-4">{{ __('Ready to Begin?') }}</h2>
            <p class="text-on-surface-variant mb-8">{{ __('Experience the care that generations of Indian mothers have trusted.') }}</p>
            <a href="{{ route('services') }}" class="btn-primary text-lg px-8 py-4">{{ __('Browse Services') }}</a>
        </div>
    </section>
</x-app-layout>
