<x-app-layout>
    <x-slot:title>{{ $service->name }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-16 bg-surface">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <a href="{{ route('services') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary transition-colors mb-8">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                {{ __('Back to Services') }}
            </a>

            <div class="flex items-center gap-4 mb-8">
                <div class="h-16 w-16 bg-primary-fixed/40 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-3xl">{{ $service->icon ?? 'spa' }}</span>
                </div>
                <div>
                    <p class="text-sm text-on-surface-variant uppercase tracking-wider">{{ $service->category->name }}</p>
                    <h1 class="text-4xl font-headline font-bold text-primary">{{ $service->name }}</h1>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-3xl p-8 mb-8">
                <div class="flex flex-wrap gap-6 mb-8">
                    <div class="flex items-center gap-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-primary">schedule</span>
                        <span>{{ $service->duration_minutes }} {{ __('minutes') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-primary">home</span>
                        <span>{{ __('At your home') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-primary">verified_user</span>
                        <span>{{ __('Verified Daimaa') }}</span>
                    </div>
                </div>
                <div class="prose prose-lg max-w-none text-on-surface-variant">
                    <p>{{ $service->description }}</p>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-3xl p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <p class="text-sm text-on-surface-variant">{{ __('Starting from') }}</p>
                    <span class="text-4xl font-bold text-primary">₹{{ number_format($service->base_price) }}</span>
                    <span class="text-on-surface-variant">/ {{ __('session') }}</span>
                </div>
                <a href="{{ route('register') }}" class="btn-primary text-lg px-8 py-4">{{ __('Book Now') }}</a>
            </div>
        </div>
    </section>
</x-app-layout>
