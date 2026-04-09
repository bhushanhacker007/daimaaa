<x-app-layout>
    <x-slot:title>{{ __('Services') }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-12 bg-surface">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <h1 class="text-5xl font-headline font-bold text-primary mb-4">{{ __('Our Services') }}</h1>
                <p class="text-lg text-on-surface-variant">{{ __("From gentle newborn massage to complete postpartum recovery, choose the care that's right for you.") }}</p>
            </div>
        </div>
    </section>

    {{-- Services by category --}}
    @foreach($categories as $category)
    @if($category->services->count())
    <section class="py-16 {{ $loop->even ? 'bg-surface-container-low' : 'bg-surface' }}">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center gap-4 mb-10">
                <div class="h-12 w-12 bg-primary-fixed/40 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-2xl">{{ $category->icon ?? 'spa' }}</span>
                </div>
                <div>
                    <h2 class="text-2xl font-headline font-bold text-primary">{{ $category->name }}</h2>
                    <p class="text-sm text-on-surface-variant">{{ $category->description }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($category->services as $service)
                <div class="bg-surface-container-lowest rounded-3xl p-8 flex flex-col">
                    <div class="h-12 w-12 bg-primary-fixed/40 rounded-xl flex items-center justify-center mb-5">
                        <span class="material-symbols-outlined text-primary">{{ $service->icon ?? 'spa' }}</span>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-primary mb-2">{{ $service->name }}</h3>
                    <p class="text-sm text-on-surface-variant mb-6 flex-1">{{ $service->short_description }}</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold text-primary">₹{{ number_format($service->base_price) }}</span>
                        <span class="text-xs text-on-surface-variant bg-surface-container rounded-full px-3 py-1">{{ $service->duration_minutes }} {{ __('min') }}</span>
                    </div>
                    <a href="{{ route('register') }}" class="btn-outline text-center text-sm">{{ __('Book This Service') }}</a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endforeach

    {{-- Packages --}}
    <section class="py-24 bg-surface-container-high" id="packages">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-headline font-bold text-primary mb-4">{{ __('Packages') }}</h2>
                <p class="text-lg text-on-surface-variant">{{ __('Save more with bundled sessions') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($packages as $package)
                <div class="bg-surface-container-lowest rounded-3xl p-8 flex flex-col {{ $loop->index === 1 ? 'ring-2 ring-primary relative' : '' }}">
                    @if($loop->index === 1)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-on-primary text-xs font-bold rounded-full">{{ __('Most Popular') }}</div>
                    @endif
                    <h3 class="text-2xl font-headline font-bold text-primary mb-2">{{ $package->name }}</h3>
                    <p class="text-sm text-on-surface-variant mb-6 flex-1">{{ $package->description }}</p>
                    <div class="mb-4">
                        <span class="text-4xl font-bold text-primary">₹{{ number_format($package->price) }}</span>
                        <span class="text-sm text-on-surface-variant ml-1">/ {{ $package->total_sessions }} {{ __('sessions') }}</span>
                    </div>
                    @if($package->discount_percent > 0)
                    <div class="inline-flex items-center gap-1 px-3 py-1 bg-tertiary-fixed/30 rounded-full mb-6 w-fit">
                        <span class="material-symbols-outlined text-tertiary text-sm">local_offer</span>
                        <span class="text-xs font-bold text-tertiary">{{ __('Save') }} {{ number_format($package->discount_percent) }}%</span>
                    </div>
                    @endif
                    <ul class="space-y-3 mb-8">
                        @foreach($package->services as $svc)
                        <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                            {{ $svc->name }} &times; {{ $svc->pivot->session_count }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="{{ $loop->index === 1 ? 'btn-primary' : 'btn-outline' }} w-full text-center">{{ __('Book This Package') }}</a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>
