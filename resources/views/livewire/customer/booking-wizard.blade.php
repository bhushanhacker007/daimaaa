<div
    class="max-w-6xl mx-auto"
    x-data="{ showMobileSummary: false }"
    x-init="$wire.on('stepChanged', () => { $el.closest('main')?.scrollTo({ top: 0, behavior: 'smooth' }); window.scrollTo({ top: 0, behavior: 'smooth' }); })"
>
    {{-- Step indicator --}}
    @php
        $steps = [
            ['icon' => 'spa', 'label' => 'Service'],
            ['icon' => 'add_box', 'label' => 'Add-ons'],
            ['icon' => 'location_on', 'label' => 'Address'],
            ['icon' => 'event', 'label' => 'Schedule'],
            ['icon' => 'verified', 'label' => 'Confirm'],
        ];
    @endphp
    <nav class="mb-6 sm:mb-8">
        {{-- Mobile: compact step bar --}}
        <div class="sm:hidden">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-primary">Step {{ $step }} of 5</p>
                <p class="text-sm font-medium text-on-surface">{{ $steps[$step - 1]['label'] }}</p>
            </div>
            <div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
                <div class="h-full cta-gradient rounded-full transition-all duration-500 ease-out" style="width: {{ ($step / 5) * 100 }}%"></div>
            </div>
        </div>
        {{-- Desktop: full step indicator --}}
        <ol class="hidden sm:flex items-center w-full max-w-2xl mx-auto">
            @foreach($steps as $i => $s)
            <li class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                <button
                    wire:click="goToStep({{ $i + 1 }})"
                    @class([
                        'group flex flex-col items-center gap-1.5 relative',
                        'cursor-pointer' => $i + 1 <= $maxStepReached,
                        'cursor-default' => $i + 1 > $maxStepReached,
                    ])
                >
                    <span @class([
                        'flex items-center justify-center w-11 h-11 rounded-full text-sm font-bold transition-all duration-300',
                        'cta-gradient text-on-primary shadow-lg shadow-primary/20 scale-110' => $step === $i + 1,
                        'bg-primary text-on-primary' => $step > $i + 1,
                        'bg-surface-container-high text-on-surface-variant' => $step < $i + 1 && $i + 1 <= $maxStepReached,
                        'bg-surface-container text-on-surface-variant/40' => $i + 1 > $maxStepReached,
                    ])>
                        @if($step > $i + 1)
                            <span class="material-symbols-outlined text-lg">check</span>
                        @else
                            <span class="material-symbols-outlined text-lg">{{ $s['icon'] }}</span>
                        @endif
                    </span>
                    <span @class([
                        'text-[11px] font-semibold tracking-wide uppercase transition-colors',
                        'text-primary' => $step === $i + 1,
                        'text-primary/70' => $step > $i + 1,
                        'text-on-surface-variant/50' => $step < $i + 1,
                    ])>{{ $s['label'] }}</span>
                </button>
                @if(!$loop->last)
                    <div @class([
                        'flex-1 h-[2px] mx-2 sm:mx-3 rounded-full transition-all duration-500',
                        'bg-primary' => $step > $i + 1,
                        'bg-gradient-to-r from-primary to-surface-container' => $step === $i + 1,
                        'bg-surface-container' => $step < $i + 1,
                    ])></div>
                @endif
            </li>
            @endforeach
        </ol>
    </nav>

    {{-- Two-column layout --}}
    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
        {{-- Main content --}}
        <div class="flex-1 min-w-0">

            {{-- ==================== STEP 1: Service / Package ==================== --}}
            @if($step === 1)
            <div class="space-y-5 sm:space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-xl sm:text-2xl font-headline font-bold text-primary">Choose Your Care</h2>
                    <p class="text-sm text-on-surface-variant mt-1">Select a care package or an individual service</p>
                </div>

                {{-- Type toggle --}}
                <div class="inline-flex bg-surface-container rounded-full p-1 gap-1">
                    <button
                        wire:click="$set('bookingType', 'package')"
                        @class([
                            'px-5 sm:px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300',
                            'cta-gradient text-on-primary shadow-md' => $bookingType === 'package',
                            'text-on-surface-variant hover:text-on-surface' => $bookingType !== 'package',
                        ])
                    >
                        <span class="material-symbols-outlined text-base align-text-bottom mr-1">inventory_2</span>
                        Packages
                    </button>
                    <button
                        wire:click="$set('bookingType', 'service')"
                        @class([
                            'px-5 sm:px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300',
                            'cta-gradient text-on-primary shadow-md' => $bookingType === 'service',
                            'text-on-surface-variant hover:text-on-surface' => $bookingType !== 'service',
                        ])
                    >
                        <span class="material-symbols-outlined text-base align-text-bottom mr-1">medical_services</span>
                        Individual
                    </button>
                </div>

                @if($bookingType === 'package')
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
                        @forelse($packages as $pkg)
                            @php
                                $isPkgSelected = $selectedPackageId === $pkg->id;
                                $totalServiceSessions = $pkg->services->sum('pivot.session_count');
                                $pricePerSession = $pkg->total_sessions > 0 ? round($pkg->price / $pkg->total_sessions) : 0;
                                $serviceIcons = [
                                    'Mother Massage' => 'self_improvement',
                                    'Baby Massage' => 'child_care',
                                    'Baby Bath' => 'bathtub',
                                    'Post-Pregnancy Belly Binding' => 'accessibility_new',
                                    'Herbal Steam Bath' => 'spa',
                                ];
                            @endphp
                            <button
                                wire:click="selectPackage({{ $pkg->id }})"
                                @class([
                                    'relative text-left rounded-3xl overflow-hidden transition-all duration-300 group flex flex-col',
                                    'ring-2 ring-primary shadow-xl shadow-primary/15 scale-[1.02]' => $isPkgSelected,
                                    'ghost-border hover:shadow-ambient hover:-translate-y-1' => !$isPkgSelected,
                                ])
                            >
                                {{-- Card Header --}}
                                <div @class([
                                    'relative px-5 sm:px-6 pt-5 sm:pt-6 pb-4',
                                    'cta-gradient' => $isPkgSelected,
                                    'bg-surface-container-lowest' => !$isPkgSelected,
                                ])>
                                    @if($pkg->is_featured)
                                        <span @class([
                                            'absolute top-3 right-3 sm:top-4 sm:right-4 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full flex items-center gap-1',
                                            'bg-on-primary/20 text-on-primary' => $isPkgSelected,
                                            'bg-tertiary text-on-tertiary' => !$isPkgSelected,
                                        ])>
                                            <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1">star</span>
                                            Popular
                                        </span>
                                    @endif

                                    @if($isPkgSelected)
                                        <div class="absolute top-3 left-4 sm:left-5 w-6 h-6 rounded-full bg-on-primary/20 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-on-primary text-base" style="font-variation-settings: 'FILL' 1">check</span>
                                        </div>
                                    @endif

                                    <h3 @class([
                                        'text-lg sm:text-xl font-headline font-bold pr-20',
                                        'text-on-primary mt-5' => $isPkgSelected,
                                        'text-primary mt-0' => !$isPkgSelected,
                                    ])>{{ $pkg->name }}</h3>

                                    <p @class([
                                        'text-xs sm:text-sm mt-1 line-clamp-2',
                                        'text-on-primary/70' => $isPkgSelected,
                                        'text-on-surface-variant' => !$isPkgSelected,
                                    ])>{{ $pkg->description }}</p>

                                    {{-- Price --}}
                                    <div class="flex items-end gap-2 mt-3">
                                        <span @class([
                                            'text-3xl sm:text-4xl font-headline font-bold leading-none',
                                            'text-on-primary' => $isPkgSelected,
                                            'text-primary' => !$isPkgSelected,
                                        ])>₹{{ number_format($pkg->price) }}</span>
                                        @if($pkg->discount_percent > 0)
                                            <span @class([
                                                'text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-full mb-1',
                                                'bg-on-primary/20 text-on-primary' => $isPkgSelected,
                                                'bg-tertiary-fixed text-on-tertiary-fixed' => !$isPkgSelected,
                                            ])>{{ (int) $pkg->discount_percent }}% off</span>
                                        @endif
                                    </div>
                                    <p @class([
                                        'text-[11px] mt-1',
                                        'text-on-primary/60' => $isPkgSelected,
                                        'text-on-surface-variant/60' => !$isPkgSelected,
                                    ])>≈ ₹{{ number_format($pricePerSession) }} per session</p>
                                </div>

                                {{-- Stats chips --}}
                                <div @class([
                                    'flex items-center gap-2 px-5 sm:px-6 py-3',
                                    'bg-primary-fixed' => $isPkgSelected,
                                    'bg-surface-container/50' => !$isPkgSelected,
                                ])>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $isPkgSelected ? 'bg-primary/15 text-primary' : 'bg-surface-container-lowest text-on-surface-variant' }}">
                                        <span class="material-symbols-outlined text-xs">event_repeat</span>
                                        {{ $pkg->total_sessions }} sessions
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $isPkgSelected ? 'bg-primary/15 text-primary' : 'bg-surface-container-lowest text-on-surface-variant' }}">
                                        <span class="material-symbols-outlined text-xs">checklist</span>
                                        {{ $pkg->services->count() }} services
                                    </span>
                                </div>

                                {{-- What's Included Section --}}
                                @if($pkg->services->count())
                                    <div class="px-5 sm:px-6 py-4 flex-1 {{ $isPkgSelected ? 'bg-primary-fixed' : 'bg-surface-container-lowest' }}">
                                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $isPkgSelected ? 'text-primary/60' : 'text-on-surface-variant/50' }} mb-3">What's included</p>
                                        <div class="space-y-2.5">
                                            @foreach($pkg->services as $svc)
                                                @php $svcIcon = $serviceIcons[$svc->name] ?? 'spa'; @endphp
                                                <div class="flex items-center gap-3">
                                                    <div @class([
                                                        'w-8 h-8 rounded-lg flex items-center justify-center shrink-0',
                                                        'bg-primary/10' => $isPkgSelected,
                                                        'bg-primary-fixed/50' => !$isPkgSelected,
                                                    ])>
                                                        <span class="material-symbols-outlined text-primary text-base">{{ $svcIcon }}</span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-on-surface leading-tight">{{ $svc->name }}</p>
                                                        <p class="text-[11px] text-on-surface-variant/60">{{ $svc->duration_minutes }} min per session</p>
                                                    </div>
                                                    <span @class([
                                                        'shrink-0 text-xs font-bold px-2 py-0.5 rounded-full',
                                                        'bg-primary text-on-primary' => $isPkgSelected,
                                                        'bg-surface-container text-on-surface-variant' => !$isPkgSelected,
                                                    ])>&times;{{ $svc->pivot->session_count }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Select CTA footer --}}
                                <div @class([
                                    'px-5 sm:px-6 py-3 text-center text-sm font-semibold transition-colors',
                                    'bg-primary text-on-primary' => $isPkgSelected,
                                    'bg-surface-container/30 text-primary group-hover:bg-primary group-hover:text-on-primary' => !$isPkgSelected,
                                ])>
                                    @if($isPkgSelected)
                                        <span class="flex items-center justify-center gap-1.5">
                                            <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1">check_circle</span>
                                            Selected
                                        </span>
                                    @else
                                        Select This Package
                                    @endif
                                </div>
                            </button>
                        @empty
                            <div class="col-span-full text-center py-12 text-on-surface-variant">
                                <span class="material-symbols-outlined text-5xl mb-3 block opacity-30">inventory_2</span>
                                <p>No packages available at the moment.</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @forelse($services as $svc)
                            @php
                                $isSelected = $selectedServiceId === $svc->id;
                                $isHourly = $svc->isHourlyPriced();
                            @endphp
                            <div
                                @class([
                                    'rounded-2xl transition-all duration-300 overflow-hidden',
                                    'bg-primary-fixed ring-2 ring-primary shadow-lg shadow-primary/10' => $isSelected,
                                    'bg-surface-container-lowest ghost-border hover:shadow-ambient hover:-translate-y-0.5' => !$isSelected,
                                ])
                            >
                                <button wire:click="selectService({{ $svc->id }})" class="w-full text-left p-4 sm:p-5">
                                    <div class="flex items-start gap-3 sm:gap-4">
                                        <div @class([
                                            'w-11 h-11 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center shrink-0 transition-colors',
                                            'bg-primary text-on-primary' => $isSelected,
                                            'bg-primary-fixed text-primary' => !$isSelected,
                                        ])>
                                            <span class="material-symbols-outlined">{{ $svc->icon ?? 'spa' }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <h3 class="font-semibold text-on-surface text-sm sm:text-base">{{ $svc->name }}</h3>
                                                @if($isSelected)
                                                    <span class="material-symbols-outlined text-primary shrink-0" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                                @endif
                                            </div>
                                            @if($svc->short_description)
                                                <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5 line-clamp-2">{{ $svc->short_description }}</p>
                                            @endif
                                            <div class="flex items-center gap-3 mt-2">
                                                @if($isHourly)
                                                    <span class="text-base sm:text-lg font-bold text-primary">₹{{ number_format($svc->price_per_hour) }}<span class="text-[10px] sm:text-xs font-normal text-on-surface-variant">/hr</span></span>
                                                @else
                                                    <span class="text-base sm:text-lg font-bold text-primary">₹{{ number_format($svc->base_price) }}</span>
                                                    <span class="text-[10px] sm:text-xs text-on-surface-variant/60 flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-xs">timer</span>
                                                        {{ $svc->duration_minutes }} min
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </button>

                                @if($isSelected && $isHourly)
                                    <div class="px-4 sm:px-5 pb-4 sm:pb-5 animate-fade-in" style="border-top: 1px solid rgba(218, 193, 186, 0.2);">
                                        <p class="text-xs font-medium text-on-surface-variant mt-3 mb-3 flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-sm text-primary">schedule</span>
                                            How many hours do you need?
                                        </p>
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-1.5 sm:gap-2">
                                                <button
                                                    wire:click="decrementHours"
                                                    @class([
                                                        'w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition-all duration-200',
                                                        'bg-surface-container text-on-surface-variant/30 cursor-not-allowed' => $selectedHours <= (float) $svc->min_hours,
                                                        'bg-surface-container-high text-on-surface hover:bg-primary hover:text-on-primary active:scale-95' => $selectedHours > (float) $svc->min_hours,
                                                    ])
                                                    @if($selectedHours <= (float) $svc->min_hours) disabled @endif
                                                ><span class="material-symbols-outlined text-xl">remove</span></button>

                                                <div class="min-w-[4.5rem] sm:min-w-[5.5rem] text-center px-2.5 sm:px-3 py-2 bg-primary/10 rounded-xl">
                                                    <span class="text-lg sm:text-xl font-bold text-primary">{{ $selectedHours == floor($selectedHours) ? number_format($selectedHours, 0) : number_format($selectedHours, 1) }}</span>
                                                    <span class="text-[10px] sm:text-xs font-medium text-primary/70 ml-0.5">{{ $selectedHours == 1 ? 'hr' : 'hrs' }}</span>
                                                </div>

                                                <button
                                                    wire:click="incrementHours"
                                                    @class([
                                                        'w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition-all duration-200',
                                                        'bg-surface-container text-on-surface-variant/30 cursor-not-allowed' => $selectedHours >= (float) $svc->max_hours,
                                                        'bg-surface-container-high text-on-surface hover:bg-primary hover:text-on-primary active:scale-95' => $selectedHours < (float) $svc->max_hours,
                                                    ])
                                                    @if($selectedHours >= (float) $svc->max_hours) disabled @endif
                                                ><span class="material-symbols-outlined text-xl">add</span></button>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg sm:text-xl font-bold text-primary">₹{{ number_format($svc->getPriceForHours($selectedHours)) }}</p>
                                                <p class="text-[10px] sm:text-[11px] text-on-surface-variant/60">₹{{ number_format($svc->price_per_hour) }} &times; {{ $selectedHours == floor($selectedHours) ? number_format($selectedHours, 0) : number_format($selectedHours, 1) }}h</p>
                                            </div>
                                        </div>

                                        @php
                                            $quickHours = [];
                                            $h = (float) $svc->min_hours;
                                            while ($h <= (float) $svc->max_hours) { $quickHours[] = $h; $h += (float) $svc->hour_increment; }
                                        @endphp
                                        @if(count($quickHours) > 1)
                                            <div class="flex flex-wrap gap-1.5 mt-3">
                                                @foreach($quickHours as $qh)
                                                    <button
                                                        wire:click="$set('selectedHours', {{ $qh }})"
                                                        @class([
                                                            'px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-150',
                                                            'bg-primary text-on-primary shadow-sm' => $selectedHours == $qh,
                                                            'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' => $selectedHours != $qh,
                                                        ])
                                                    >{{ $qh == floor($qh) ? number_format($qh, 0) : number_format($qh, 1) }}h</button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12 text-on-surface-variant">
                                <span class="material-symbols-outlined text-5xl mb-3 block opacity-30">medical_services</span>
                                <p>No services available at the moment.</p>
                            </div>
                        @endforelse
                    </div>
                @endif

                @error('selectedPackageId') <p class="text-sm text-error flex items-center gap-1 mt-2"><span class="material-symbols-outlined text-sm">error</span> {{ $message }}</p> @enderror
                @error('selectedServiceId') <p class="text-sm text-error flex items-center gap-1 mt-2"><span class="material-symbols-outlined text-sm">error</span> {{ $message }}</p> @enderror
            </div>
            @endif

            {{-- ==================== STEP 2: Add-ons ==================== --}}
            @if($step === 2)
            <div class="space-y-5 sm:space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-xl sm:text-2xl font-headline font-bold text-primary">Enhance Your Care</h2>
                    <p class="text-sm text-on-surface-variant mt-1">Optional extras to make your session special</p>
                </div>

                @if($addOns->count())
                    <div class="space-y-3">
                        @foreach($addOns as $addOn)
                            @php $isAddonSelected = in_array($addOn->id, $selectedAddOns); @endphp
                            <button
                                wire:click="toggleAddOn({{ $addOn->id }})"
                                @class([
                                    'w-full text-left rounded-2xl p-4 sm:p-5 flex items-center gap-3 sm:gap-4 transition-all duration-300',
                                    'bg-primary-fixed ring-2 ring-primary shadow-md' => $isAddonSelected,
                                    'bg-surface-container-lowest ghost-border hover:shadow-ambient' => !$isAddonSelected,
                                ])
                            >
                                <div @class([
                                    'w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-all duration-200',
                                    'bg-primary text-on-primary' => $isAddonSelected,
                                    'bg-surface-container-high' => !$isAddonSelected,
                                ])>
                                    @if($isAddonSelected)
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-on-surface text-sm sm:text-base">{{ $addOn->name }}</h3>
                                    @if($addOn->description)
                                        <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5 line-clamp-2">{{ $addOn->description }}</p>
                                    @endif
                                </div>
                                <span class="text-base sm:text-lg font-bold text-primary shrink-0">+₹{{ number_format($addOn->price) }}</span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-surface-container-lowest rounded-2xl ghost-border">
                        <span class="material-symbols-outlined text-5xl mb-3 block opacity-30">add_box</span>
                        <p class="text-on-surface-variant">No add-ons available right now</p>
                    </div>
                @endif

                @if(!count($selectedAddOns))
                    <div class="flex items-center gap-3 bg-surface-container rounded-xl px-4 py-3">
                        <span class="material-symbols-outlined text-on-surface-variant text-lg">info</span>
                        <p class="text-xs sm:text-sm text-on-surface-variant">No add-ons needed? That's fine! Tap <strong>Next</strong> to continue.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- ==================== STEP 3: Address ==================== --}}
            @if($step === 3)
            <div class="space-y-5 sm:space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-xl sm:text-2xl font-headline font-bold text-primary">Service Address</h2>
                    <p class="text-sm text-on-surface-variant mt-1">Where should our Daimaa visit?</p>
                </div>

                @if($addresses->count() && !$newAddress)
                    <div class="space-y-3">
                        @foreach($addresses as $addr)
                            <button
                                wire:click="$set('selectedAddressId', {{ $addr->id }})"
                                @class([
                                    'w-full text-left rounded-2xl p-4 sm:p-5 transition-all duration-300',
                                    'bg-primary-fixed ring-2 ring-primary shadow-md' => $selectedAddressId === $addr->id,
                                    'bg-surface-container-lowest ghost-border hover:shadow-ambient' => $selectedAddressId !== $addr->id,
                                ])
                            >
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div @class([
                                        'w-10 h-10 rounded-xl flex items-center justify-center shrink-0',
                                        'bg-primary text-on-primary' => $selectedAddressId === $addr->id,
                                        'bg-surface-container text-on-surface-variant' => $selectedAddressId !== $addr->id,
                                    ])>
                                        <span class="material-symbols-outlined text-lg">{{ $addr->label === 'Office' ? 'business' : ($addr->label === 'Other' ? 'pin_drop' : 'home') }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-on-surface text-sm sm:text-base">{{ $addr->label }}</span>
                                            @if($addr->is_default)
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary-fixed px-2 py-0.5 rounded-full">Default</span>
                                            @endif
                                        </div>
                                        <p class="text-xs sm:text-sm text-on-surface-variant mt-1">{{ $addr->address_line_1 }}</p>
                                        @if($addr->address_line_2)
                                            <p class="text-xs sm:text-sm text-on-surface-variant">{{ $addr->address_line_2 }}</p>
                                        @endif
                                        <p class="text-xs text-on-surface-variant/60 mt-1">{{ $addr->pincode }}{{ $addr->city ? ' · ' . $addr->city->name : '' }}</p>
                                    </div>
                                    @if($selectedAddressId === $addr->id)
                                        <span class="material-symbols-outlined text-primary shrink-0" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                    <button wire:click="$set('newAddress', true)" class="inline-flex items-center gap-2 text-sm text-primary font-semibold hover:text-primary-container transition-colors">
                        <span class="material-symbols-outlined text-lg">add_circle</span> Add new address
                    </button>
                @else
                    <div class="bg-surface-container-lowest rounded-3xl p-5 sm:p-8 ghost-border space-y-4 sm:space-y-5">
                        @if(!$addresses->count())
                            <div class="flex items-center gap-3 bg-tertiary-fixed/50 text-on-tertiary-fixed rounded-2xl px-4 py-3">
                                <span class="material-symbols-outlined text-lg">info</span>
                                <p class="text-xs sm:text-sm">No saved addresses. Please add one below.</p>
                            </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-on-surface mb-2 block">Address Label</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Home', 'Office', 'Other'] as $label)
                                    <button
                                        wire:click="$set('addressLabel', '{{ $label }}')"
                                        @class([
                                            'px-4 py-2.5 rounded-full text-sm font-medium transition-all duration-200',
                                            'cta-gradient text-on-primary shadow-sm' => $addressLabel === $label,
                                            'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' => $addressLabel !== $label,
                                        ])
                                    >
                                        <span class="material-symbols-outlined text-sm align-text-bottom mr-1">{{ $label === 'Office' ? 'business' : ($label === 'Other' ? 'pin_drop' : 'home') }}</span>
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="addr1" class="text-sm font-medium text-on-surface mb-1 block">Address Line 1 <span class="text-error">*</span></label>
                            <input id="addr1" type="text" wire:model.blur="addressLine1" class="input-field" placeholder="Flat / House No., Building Name">
                            @error('addressLine1') <p class="text-xs text-error flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-xs">error</span> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="addr2" class="text-sm font-medium text-on-surface mb-1 block">Address Line 2</label>
                            <input id="addr2" type="text" wire:model.blur="addressLine2" class="input-field" placeholder="Street, Area, Locality">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label for="landmark" class="text-sm font-medium text-on-surface mb-1 block">Landmark</label>
                                <input id="landmark" type="text" wire:model.blur="landmark" class="input-field" placeholder="Near...">
                            </div>
                            <div>
                                <label for="pincode" class="text-sm font-medium text-on-surface mb-1 block">Pincode <span class="text-error">*</span></label>
                                <input id="pincode" type="text" wire:model.blur="pincode" class="input-field" maxlength="6" placeholder="400001" inputmode="numeric">
                                @error('pincode') <p class="text-xs text-error flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-xs">error</span> {{ $message }}</p> @enderror
                            </div>
                        </div>
                        @if($addresses->count())
                            <button wire:click="$set('newAddress', false)" class="inline-flex items-center gap-2 text-sm text-primary font-semibold hover:text-primary-container transition-colors">
                                <span class="material-symbols-outlined text-lg">arrow_back</span> Use a saved address
                            </button>
                        @endif
                    </div>
                @endif
                @error('selectedAddressId') <p class="text-sm text-error flex items-center gap-1 mt-2"><span class="material-symbols-outlined text-sm">error</span> {{ $message }}</p> @enderror
            </div>
            @endif

            {{-- ==================== STEP 4: Schedule ==================== --}}
            @if($step === 4)
            @php $instantOk = $this->isInstantAvailable(); @endphp
            <div class="space-y-5 sm:space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-xl sm:text-2xl font-headline font-bold text-primary">When do you need your Daimaa?</h2>
                    <p class="text-sm text-on-surface-variant mt-1">Choose instant delivery or schedule for later</p>
                </div>

                {{-- Instant Booking Card --}}
                <button
                    wire:click="$set('scheduleMode', 'instant')"
                    @class([
                        'w-full text-left rounded-2xl sm:rounded-3xl overflow-hidden transition-all duration-300',
                        'ring-2 ring-primary shadow-lg shadow-primary/10' => $scheduleMode === 'instant',
                        'ghost-border hover:shadow-ambient' => $scheduleMode !== 'instant',
                        'opacity-50 cursor-not-allowed' => !$instantOk,
                    ])
                    @if(!$instantOk) disabled @endif
                >
                    <div class="flex items-center gap-4 p-4 sm:p-5 {{ $scheduleMode === 'instant' ? 'bg-primary-fixed' : 'bg-surface-container-lowest' }}">
                        <div @class([
                            'w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center shrink-0 transition-colors',
                            'cta-gradient shadow-md' => $scheduleMode === 'instant',
                            'bg-surface-container' => $scheduleMode !== 'instant',
                        ])>
                            <span class="material-symbols-outlined text-2xl {{ $scheduleMode === 'instant' ? 'text-on-primary' : 'text-tertiary' }}">bolt</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-on-surface text-base">Instant</h3>
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary text-on-tertiary">
                                    <span class="material-symbols-outlined text-xs">bolt</span> QUICK
                                </span>
                            </div>
                            <p class="text-sm text-on-surface-variant mt-0.5">Daimaa arrives within 30 minutes</p>
                            @if(!$instantOk)
                                <p class="text-xs text-error mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">info</span>
                                    Not available for the selected service
                                </p>
                            @elseif($this->getInstantSurcharge() > 0 && $scheduleMode === 'instant')
                                <p class="text-xs text-tertiary font-medium mt-1">+₹{{ number_format($this->getInstantSurcharge()) }} instant delivery fee</p>
                            @endif
                        </div>
                        <div class="shrink-0">
                            <div @class([
                                'w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all',
                                'border-primary bg-primary' => $scheduleMode === 'instant',
                                'border-outline-variant' => $scheduleMode !== 'instant',
                            ])>
                                @if($scheduleMode === 'instant')
                                    <span class="material-symbols-outlined text-on-primary text-sm">check</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($scheduleMode === 'instant')
                        <div class="px-4 sm:px-5 pb-4 sm:pb-5 bg-primary-fixed animate-fade-in">
                            <div class="flex items-center gap-3 bg-surface-container-lowest/70 rounded-xl px-4 py-3">
                                <span class="material-symbols-outlined text-primary text-lg">schedule</span>
                                <div>
                                    <p class="text-sm font-semibold text-on-surface">Estimated arrival: {{ now()->addMinutes(30)->format('g:i A') }}</p>
                                    <p class="text-xs text-on-surface-variant">Today, {{ now()->format('M j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </button>

                {{-- Schedule for Later Card --}}
                <div @class([
                    'rounded-2xl sm:rounded-3xl overflow-hidden transition-all duration-300',
                    'ring-2 ring-primary shadow-lg shadow-primary/10' => $scheduleMode === 'schedule',
                    'ghost-border hover:shadow-ambient' => $scheduleMode !== 'schedule',
                ])>
                    <button wire:click="$set('scheduleMode', 'schedule')" class="w-full text-left">
                        <div class="flex items-center gap-4 p-4 sm:p-5 {{ $scheduleMode === 'schedule' ? 'bg-primary-fixed' : 'bg-surface-container-lowest' }}">
                            <div @class([
                                'w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center shrink-0 transition-colors',
                                'cta-gradient shadow-md' => $scheduleMode === 'schedule',
                                'bg-surface-container' => $scheduleMode !== 'schedule',
                            ])>
                                <span class="material-symbols-outlined text-2xl {{ $scheduleMode === 'schedule' ? 'text-on-primary' : 'text-primary' }}">calendar_month</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-on-surface text-base">Schedule for later</h3>
                                <p class="text-sm text-on-surface-variant mt-0.5">Select your preferred day & time</p>
                            </div>
                            <div class="shrink-0">
                                <div @class([
                                    'w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all',
                                    'border-primary bg-primary' => $scheduleMode === 'schedule',
                                    'border-outline-variant' => $scheduleMode !== 'schedule',
                                ])>
                                    @if($scheduleMode === 'schedule')
                                        <span class="material-symbols-outlined text-on-primary text-sm">check</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </button>

                    @if($scheduleMode === 'schedule')
                        <div class="bg-primary-fixed px-4 sm:px-5 pb-5 sm:pb-6 space-y-5 animate-fade-in">
                            {{-- Date Picker: Horizontal scrollable days --}}
                            <div>
                                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-primary">calendar_today</span> Select Date
                                </p>
                                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                                    @for($d = 1; $d <= 14; $d++)
                                        @php
                                            $date = now()->addDays($d);
                                            $dateStr = $date->format('Y-m-d');
                                            $isDateSelected = $scheduledDate === $dateStr;
                                        @endphp
                                        <button
                                            wire:click="$set('scheduledDate', '{{ $dateStr }}')"
                                            @class([
                                                'shrink-0 flex flex-col items-center gap-0.5 w-[3.5rem] sm:w-16 py-2.5 sm:py-3 rounded-xl transition-all duration-200',
                                                'cta-gradient text-on-primary shadow-md scale-105' => $isDateSelected,
                                                'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-high' => !$isDateSelected,
                                            ])
                                        >
                                            <span class="text-[10px] font-semibold uppercase">{{ $date->format('D') }}</span>
                                            <span class="text-lg sm:text-xl font-bold leading-none">{{ $date->format('d') }}</span>
                                            <span class="text-[10px]">{{ $date->format('M') }}</span>
                                        </button>
                                    @endfor
                                </div>
                                @error('scheduledDate') <p class="text-xs text-error flex items-center gap-1 mt-2"><span class="material-symbols-outlined text-xs">error</span> {{ $message }}</p> @enderror
                            </div>

                            {{-- Time Slots Grid --}}
                            <div>
                                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-primary">schedule</span> Select start time
                                </p>
                                <div class="space-y-4">
                                    @foreach([
                                        ['label' => 'Morning', 'icon' => 'wb_sunny', 'color' => 'text-tertiary', 'times' => ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30']],
                                        ['label' => 'Afternoon', 'icon' => 'wb_twilight', 'color' => 'text-primary', 'times' => ['12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30']],
                                        ['label' => 'Evening', 'icon' => 'dark_mode', 'color' => 'text-secondary', 'times' => ['16:00', '16:30', '17:00', '17:30', '18:00', '18:30']],
                                    ] as $period)
                                        <div>
                                            <p class="text-[11px] font-semibold text-on-surface-variant/70 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                <span class="material-symbols-outlined text-xs {{ $period['color'] }}">{{ $period['icon'] }}</span> {{ $period['label'] }}
                                            </p>
                                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                                @foreach($period['times'] as $time)
                                                    @php $isPeak = in_array($time, ['10:00', '10:30', '11:00', '11:30', '12:00', '12:30']); @endphp
                                                    <button
                                                        wire:click="$set('scheduledTime', '{{ $time }}')"
                                                        @class([
                                                            'relative px-2 py-2.5 sm:py-3 rounded-xl text-sm font-medium transition-all duration-200 text-center',
                                                            'cta-gradient text-on-primary shadow-md ring-2 ring-primary/30' => $scheduledTime === $time,
                                                            'bg-surface-container-lowest text-on-surface hover:bg-surface-container-high' => $scheduledTime !== $time,
                                                        ])
                                                    >
                                                        {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                                                        @if($isPeak && $scheduledTime !== $time)
                                                            <span class="absolute -top-1.5 -right-0.5 text-[9px] font-bold text-tertiary">+ ₹10</span>
                                                        @endif
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Special Instructions --}}
                <div class="bg-surface-container-lowest rounded-2xl sm:rounded-3xl ghost-border p-4 sm:p-6">
                    <label for="notes" class="text-sm font-medium text-on-surface mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg text-primary">edit_note</span>
                        Special Instructions
                    </label>
                    <textarea id="notes" wire:model.blur="notes" class="input-field" rows="3" placeholder="Allergies, preferences, gate code..."></textarea>
                </div>
            </div>
            @endif

            {{-- ==================== STEP 5: Review & Confirm ==================== --}}
            @if($step === 5)
            <div class="space-y-4 sm:space-y-5 animate-fade-in">
                <div>
                    <h2 class="text-xl sm:text-2xl font-headline font-bold text-primary">Review & Confirm</h2>
                    <p class="text-sm text-on-surface-variant mt-1">Double-check everything before confirming</p>
                </div>

                {{-- Summary cards --}}
                @php
                    $summaryCards = [
                        ['step' => 1, 'icon' => 'spa', 'title' => 'Care Selected'],
                        ['step' => 3, 'icon' => 'location_on', 'title' => 'Address'],
                        ['step' => 4, 'icon' => 'event', 'title' => 'Schedule'],
                    ];
                @endphp

                {{-- Service --}}
                <div class="bg-surface-container-lowest rounded-2xl sm:rounded-3xl ghost-border overflow-hidden">
                    <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 bg-surface-container/50">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2 text-sm sm:text-base">
                            <span class="material-symbols-outlined text-lg text-primary">spa</span> Care Selected
                        </h3>
                        <button wire:click="goToStep(1)" class="text-xs text-primary font-semibold hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">edit</span> Edit
                        </button>
                    </div>
                    <div class="px-4 sm:px-6 py-3 sm:py-4">
                        <p class="font-semibold text-on-surface">
                            @if($bookingType === 'package' && $selectedPackageId)
                                {{ \App\Models\Package::find($selectedPackageId)?->name }}
                            @elseif($selectedServiceId)
                                {{ \App\Models\Service::find($selectedServiceId)?->name }}
                            @endif
                        </p>
                        <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5 capitalize">{{ $bookingType }}</p>
                        @if($bookingType === 'service' && $selectedServiceId)
                            @php $reviewSvc = \App\Models\Service::find($selectedServiceId); @endphp
                            @if($reviewSvc && $reviewSvc->isHourlyPriced())
                                <div class="flex flex-wrap items-center gap-3 sm:gap-4 mt-2 text-sm">
                                    <span class="flex items-center gap-1.5 text-primary font-medium">
                                        <span class="material-symbols-outlined text-base">schedule</span>
                                        {{ $selectedHours == floor($selectedHours) ? number_format($selectedHours, 0) : number_format($selectedHours, 1) }} {{ $selectedHours == 1 ? 'hour' : 'hours' }}
                                    </span>
                                    <span class="text-on-surface-variant text-xs">₹{{ number_format($reviewSvc->price_per_hour) }}/hr</span>
                                    <span class="font-bold text-primary">= ₹{{ number_format($reviewSvc->getPriceForHours($selectedHours)) }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Add-ons --}}
                @if(count($selectedAddOns))
                <div class="bg-surface-container-lowest rounded-2xl sm:rounded-3xl ghost-border overflow-hidden">
                    <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 bg-surface-container/50">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2 text-sm sm:text-base">
                            <span class="material-symbols-outlined text-lg text-primary">add_box</span> Add-ons
                        </h3>
                        <button wire:click="goToStep(2)" class="text-xs text-primary font-semibold hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-sm">edit</span> Edit</button>
                    </div>
                    <div class="px-4 sm:px-6 py-3 sm:py-4 space-y-2">
                        @foreach($selectedAddOns as $id)
                            @php $addOn = \App\Models\AddOn::find($id); @endphp
                            @if($addOn)
                                <div class="flex justify-between text-sm">
                                    <span class="text-on-surface">{{ $addOn->name }}</span>
                                    <span class="text-on-surface-variant font-medium">+₹{{ number_format($addOn->price) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Address --}}
                <div class="bg-surface-container-lowest rounded-2xl sm:rounded-3xl ghost-border overflow-hidden">
                    <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 bg-surface-container/50">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2 text-sm sm:text-base">
                            <span class="material-symbols-outlined text-lg text-primary">location_on</span> Address
                        </h3>
                        <button wire:click="goToStep(3)" class="text-xs text-primary font-semibold hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-sm">edit</span> Edit</button>
                    </div>
                    <div class="px-4 sm:px-6 py-3 sm:py-4">
                        @if($newAddress)
                            <p class="text-sm text-on-surface">{{ $addressLine1 }}</p>
                            @if($addressLine2)<p class="text-xs sm:text-sm text-on-surface-variant">{{ $addressLine2 }}</p>@endif
                            <p class="text-xs text-on-surface-variant mt-1">{{ $pincode }}</p>
                        @elseif($selectedAddressId)
                            @php $addr = auth()->user()->addresses()->find($selectedAddressId); @endphp
                            @if($addr)
                                <p class="text-sm font-medium text-on-surface">{{ $addr->label }}</p>
                                <p class="text-xs sm:text-sm text-on-surface-variant">{{ $addr->address_line_1 }}</p>
                                <p class="text-xs text-on-surface-variant mt-1">{{ $addr->pincode }}{{ $addr->city ? ' · ' . $addr->city->name : '' }}</p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="bg-surface-container-lowest rounded-2xl sm:rounded-3xl ghost-border overflow-hidden">
                    <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 bg-surface-container/50">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2 text-sm sm:text-base">
                            <span class="material-symbols-outlined text-lg text-primary">event</span> Schedule
                        </h3>
                        <button wire:click="goToStep(4)" class="text-xs text-primary font-semibold hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-sm">edit</span> Edit</button>
                    </div>
                    <div class="px-4 sm:px-6 py-3 sm:py-4">
                        @if($scheduleMode === 'instant')
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-tertiary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-on-tertiary text-lg">bolt</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-on-surface flex items-center gap-1.5">
                                        Instant Booking
                                        <span class="text-[10px] font-bold bg-tertiary text-on-tertiary px-2 py-0.5 rounded-full">30 MIN</span>
                                    </p>
                                    <p class="text-xs text-on-surface-variant">Daimaa arrives by {{ now()->addMinutes(30)->format('g:i A') }} today</p>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm text-primary">calendar_today</span>
                                    <span class="text-sm font-medium text-on-surface">{{ \Carbon\Carbon::parse($scheduledDate)->format('D, M j, Y') }}</span>
                                </div>
                                @if($scheduledTime)
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm text-primary">schedule</span>
                                        <span class="text-sm font-medium text-on-surface">{{ \Carbon\Carbon::parse($scheduledTime)->format('g:i A') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="bg-surface-container-lowest rounded-2xl sm:rounded-3xl ghost-border overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 sm:py-5">
                        <label class="text-sm font-medium text-on-surface mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-tertiary">local_offer</span> Have a coupon?
                        </label>
                        @if($couponValid)
                            <div class="flex items-center justify-between bg-primary-fixed/40 rounded-xl px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-lg" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span class="text-sm font-semibold text-primary uppercase">{{ $couponCode }}</span>
                                </div>
                                <button wire:click="removeCoupon" class="text-xs text-error font-semibold hover:underline">Remove</button>
                            </div>
                            @if($couponMessage)<p class="text-sm text-primary mt-2">{{ $couponMessage }}</p>@endif
                        @else
                            <div class="flex gap-2">
                                <input type="text" wire:model="couponCode" class="input-field flex-1 uppercase" placeholder="Enter code" wire:keydown.enter="applyCoupon">
                                <button wire:click="applyCoupon" wire:loading.attr="disabled" class="btn-secondary px-5 sm:px-6 shrink-0">
                                    <span wire:loading.remove wire:target="applyCoupon">Apply</span>
                                    <span wire:loading wire:target="applyCoupon">...</span>
                                </button>
                            </div>
                            @if($couponMessage && !$couponValid)
                                <p class="text-xs text-error flex items-center gap-1 mt-2"><span class="material-symbols-outlined text-xs">error</span> {{ $couponMessage }}</p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Mobile pricing summary (shown only on mobile in step 5) --}}
                <div class="lg:hidden bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
                    <div class="px-4 py-4 space-y-2.5">
                        <div class="flex justify-between text-sm">
                            <span class="text-on-surface-variant">Subtotal</span>
                            <span class="text-on-surface font-medium">₹{{ number_format($this->getSubtotal() - $this->getInstantSurcharge()) }}</span>
                        </div>
                        @if($this->getInstantSurcharge() > 0)
                            <div class="flex justify-between text-sm text-tertiary">
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-xs">bolt</span> Instant fee</span>
                                <span>+₹{{ number_format($this->getInstantSurcharge()) }}</span>
                            </div>
                        @endif
                        @if($this->getDiscount() > 0)
                            <div class="flex justify-between text-sm text-primary">
                                <span>Discount</span>
                                <span>-₹{{ number_format($this->getDiscount()) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold pt-2" style="border-top: 2px solid rgba(218, 193, 186, 0.3);">
                            <span class="text-on-surface">Total</span>
                            <span class="text-primary">₹{{ number_format($this->getTotal()) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Desktop navigation buttons --}}
            <div class="hidden sm:flex items-center justify-between mt-8 pt-6" style="border-top: 1px solid rgba(218, 193, 186, 0.15);">
                @if($step > 1)
                    <button wire:click="prevStep" wire:loading.attr="disabled" class="btn-outline group">
                        <span class="material-symbols-outlined mr-1 text-lg transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                        Back
                    </button>
                @else
                    <div></div>
                @endif

                @if($step < 5)
                    <button wire:click="nextStep" wire:loading.attr="disabled" class="btn-primary group px-8 py-3">
                        <span wire:loading.remove wire:target="nextStep">
                            Next
                            <span class="material-symbols-outlined ml-1 text-lg align-middle transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                        </span>
                        <span wire:loading wire:target="nextStep" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Validating...
                        </span>
                    </button>
                @else
                    <button wire:click="placeBooking" wire:loading.attr="disabled" class="btn-primary text-base px-8 py-3.5 group">
                        <span wire:loading.remove wire:target="placeBooking" class="flex items-center gap-2">
                            <span class="material-symbols-outlined">verified</span>
                            Confirm Booking
                        </span>
                        <span wire:loading wire:target="placeBooking" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Placing Booking...
                        </span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Desktop sidebar --}}
        <div class="hidden lg:block w-80 shrink-0">
            <div class="sticky top-24 space-y-4">
                <div class="bg-surface-container-lowest rounded-3xl ghost-border overflow-hidden">
                    <div class="px-6 py-4 cta-gradient">
                        <h3 class="text-on-primary font-headline font-bold text-lg">Order Summary</h3>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        @if($this->getSelectedName())
                            <div class="flex justify-between items-start gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs uppercase tracking-wider text-on-surface-variant/60 font-semibold">{{ $bookingType }}</p>
                                    <p class="font-semibold text-on-surface mt-0.5">{{ $this->getSelectedName() }}</p>
                                    @if($bookingType === 'service' && $selectedServiceId)
                                        @php $sidebarSvc = \App\Models\Service::find($selectedServiceId); @endphp
                                        @if($sidebarSvc && $sidebarSvc->isHourlyPriced())
                                            <p class="text-xs text-on-surface-variant mt-1 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs">schedule</span>
                                                {{ $selectedHours == floor($selectedHours) ? number_format($selectedHours, 0) : number_format($selectedHours, 1) }} {{ $selectedHours == 1 ? 'hour' : 'hours' }}
                                                &middot; ₹{{ number_format($sidebarSvc->price_per_hour) }}/hr
                                            </p>
                                        @endif
                                    @endif
                                </div>
                                <p class="font-bold text-primary shrink-0">
                                    @if($bookingType === 'package')
                                        ₹{{ number_format(\App\Models\Package::find($selectedPackageId)?->price ?? 0) }}
                                    @else
                                        ₹{{ number_format($this->getServicePrice()) }}
                                    @endif
                                </p>
                            </div>
                        @else
                            <p class="text-sm text-on-surface-variant/50 italic">No service selected yet</p>
                        @endif

                        @if(count($selectedAddOns))
                            <div style="border-top: 1px solid rgba(218, 193, 186, 0.15);" class="pt-3 space-y-2">
                                <p class="text-xs uppercase tracking-wider text-on-surface-variant/60 font-semibold">Add-ons</p>
                                @foreach($selectedAddOns as $id)
                                    @php $ao = \App\Models\AddOn::find($id); @endphp
                                    @if($ao)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-on-surface-variant">{{ $ao->name }}</span>
                                            <span class="text-on-surface">+₹{{ number_format($ao->price) }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div style="border-top: 1px solid rgba(218, 193, 186, 0.15);" class="pt-3">
                            <p class="text-xs uppercase tracking-wider text-on-surface-variant/60 font-semibold mb-1">Schedule</p>
                            @if($scheduleMode === 'instant')
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-tertiary text-sm">bolt</span>
                                    <span class="text-sm font-semibold text-tertiary">Instant — within 30 min</span>
                                </div>
                            @elseif($scheduledDate)
                                <p class="text-sm text-on-surface">{{ \Carbon\Carbon::parse($scheduledDate)->format('M j, Y') }}
                                    @if($scheduledTime) &middot; {{ \Carbon\Carbon::parse($scheduledTime)->format('g:i A') }} @endif
                                </p>
                            @else
                                <p class="text-sm text-on-surface-variant/50 italic">Not scheduled yet</p>
                            @endif
                        </div>

                        <div style="border-top: 2px solid rgba(218, 193, 186, 0.3);" class="pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">Subtotal</span>
                                <span class="text-on-surface">₹{{ number_format($this->getSubtotal() - $this->getInstantSurcharge()) }}</span>
                            </div>
                            @if($this->getInstantSurcharge() > 0)
                                <div class="flex justify-between text-sm text-tertiary">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-xs">bolt</span> Instant fee</span>
                                    <span>+₹{{ number_format($this->getInstantSurcharge()) }}</span>
                                </div>
                            @endif
                            @if($this->getDiscount() > 0)
                                <div class="flex justify-between text-sm text-primary">
                                    <span>Discount</span>
                                    <span>-₹{{ number_format($this->getDiscount()) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold pt-2" style="border-top: 1px solid rgba(218, 193, 186, 0.15);">
                                <span class="text-on-surface">Total</span>
                                <span class="text-primary">₹{{ number_format($this->getTotal()) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl ghost-border px-5 py-4">
                    <div class="flex items-center gap-3 text-on-surface-variant">
                        <span class="material-symbols-outlined text-lg text-primary">shield</span>
                        <p class="text-xs leading-relaxed">100% secure booking. No upfront payment required. Pay after your session.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== Mobile Bottom Bar ==================== --}}
    <div class="fixed bottom-0 inset-x-0 sm:hidden z-40">
        {{-- Expandable mobile summary --}}
        <div
            x-show="showMobileSummary"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-full opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-full opacity-0"
            class="bg-surface-container-lowest rounded-t-3xl shadow-[0_-8px_30px_rgba(0,0,0,0.1)] px-5 pt-5 pb-2 max-h-[60vh] overflow-y-auto"
        >
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-headline font-bold text-primary text-base">Order Summary</h3>
                <button @click="showMobileSummary = false" class="text-on-surface-variant"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="space-y-3 text-sm">
                @if($this->getSelectedName())
                    <div class="flex justify-between">
                        <div>
                            <p class="text-xs text-on-surface-variant/60 uppercase font-semibold">{{ $bookingType }}</p>
                            <p class="font-medium text-on-surface">{{ $this->getSelectedName() }}</p>
                            @if($bookingType === 'service' && $selectedServiceId)
                                @php $mobileSvc = \App\Models\Service::find($selectedServiceId); @endphp
                                @if($mobileSvc && $mobileSvc->isHourlyPriced())
                                    <p class="text-xs text-on-surface-variant">{{ $selectedHours == floor($selectedHours) ? number_format($selectedHours, 0) : number_format($selectedHours, 1) }}h &middot; ₹{{ number_format($mobileSvc->price_per_hour) }}/hr</p>
                                @endif
                            @endif
                        </div>
                        <span class="font-bold text-primary">
                            @if($bookingType === 'package') ₹{{ number_format(\App\Models\Package::find($selectedPackageId)?->price ?? 0) }}
                            @else ₹{{ number_format($this->getServicePrice()) }}
                            @endif
                        </span>
                    </div>
                @endif
                @if(count($selectedAddOns))
                    @foreach($selectedAddOns as $id)
                        @php $ao = \App\Models\AddOn::find($id); @endphp
                        @if($ao)
                            <div class="flex justify-between text-on-surface-variant">
                                <span>{{ $ao->name }}</span>
                                <span>+₹{{ number_format($ao->price) }}</span>
                            </div>
                        @endif
                    @endforeach
                @endif
                <div class="flex items-center gap-2 text-on-surface-variant pt-1" style="border-top: 1px solid rgba(218, 193, 186, 0.15);">
                    @if($scheduleMode === 'instant')
                        <span class="material-symbols-outlined text-sm text-tertiary">bolt</span>
                        <span class="text-tertiary font-medium">Instant — within 30 min</span>
                    @elseif($scheduledDate)
                        <span class="material-symbols-outlined text-sm">event</span>
                        {{ \Carbon\Carbon::parse($scheduledDate)->format('M j') }}@if($scheduledTime), {{ \Carbon\Carbon::parse($scheduledTime)->format('g:i A') }}@endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Main bottom bar --}}
        <div class="bg-surface-container-lowest/95 backdrop-blur-xl px-4 py-3 shadow-[0_-4px_20px_rgba(0,0,0,0.08)]" style="border-top: 1px solid rgba(218, 193, 186, 0.2);">
            <div class="flex items-center justify-between max-w-lg mx-auto gap-3">
                {{-- Back button --}}
                @if($step > 1)
                    <button wire:click="prevStep" wire:loading.attr="disabled" class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center shrink-0 text-on-surface-variant hover:bg-surface-container-high transition-colors active:scale-95">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>
                @endif

                {{-- Price (tappable to expand summary) --}}
                <button @click="showMobileSummary = !showMobileSummary" class="flex items-center gap-1.5 min-w-0">
                    <div>
                        <p class="text-[10px] text-on-surface-variant leading-tight">Total</p>
                        <p class="text-lg font-bold text-primary leading-tight">₹{{ number_format($this->getTotal()) }}</p>
                    </div>
                    <span class="material-symbols-outlined text-on-surface-variant/50 text-sm transition-transform duration-200" :class="showMobileSummary ? 'rotate-180' : ''">expand_less</span>
                </button>

                {{-- Next / Confirm --}}
                @if($step < 5)
                    <button wire:click="nextStep" wire:loading.attr="disabled" class="btn-primary px-6 py-2.5 text-sm shrink-0">
                        <span wire:loading.remove wire:target="nextStep" class="flex items-center gap-1">
                            Next <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </span>
                        <span wire:loading wire:target="nextStep" class="flex items-center gap-1.5">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            ...
                        </span>
                    </button>
                @else
                    <button wire:click="placeBooking" wire:loading.attr="disabled" class="btn-primary px-5 py-2.5 text-sm shrink-0">
                        <span wire:loading.remove wire:target="placeBooking" class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-lg">verified</span> Confirm
                        </span>
                        <span wire:loading wire:target="placeBooking" class="flex items-center gap-1.5">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Booking...
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom spacer for mobile bar --}}
    <div class="h-20 sm:hidden"></div>
</div>
