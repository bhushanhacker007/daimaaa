<x-dashboard-layout>
    <x-slot:title>Onboard a Daimaa — Admin</x-slot:title>
    <x-slot:heading>
        <span class="inline-flex items-center gap-2">
            <a href="{{ route('admin.daimaas') }}" class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Daimaas
            </a>
            <span class="text-on-surface-variant/50">/</span>
            <span>Onboard a Daimaa</span>
        </span>
    </x-slot:heading>
    <x-slot:sidebar>@include('admin._sidebar')</x-slot:sidebar>

    <div class="mb-6 max-w-5xl mx-auto">
        <div class="bg-gradient-to-br from-primary/5 via-primary-fixed/10 to-tertiary/5 rounded-3xl p-6 sm:p-7 border border-primary/15">
            <div class="flex items-start gap-4">
                <div class="hidden sm:flex w-14 h-14 rounded-2xl bg-primary/10 items-center justify-center text-primary shrink-0">
                    <span class="material-symbols-outlined" style="font-size: 32px;">diversity_2</span>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-headline font-bold text-on-surface mb-1">Onboard a new Daimaa</h1>
                    <p class="text-on-surface-variant text-sm sm:text-base leading-relaxed">
                        Create a Daimaa account end-to-end &mdash; account credentials, personal details, professional profile, KYC, bank details, and weekly availability. Sensitive fields are encrypted at rest.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <livewire:admin.onboard-daimaa />
</x-dashboard-layout>
