<div
    class="max-w-6xl mx-auto"
    x-data
    x-init="$wire.on('stepChanged', () => { $el.closest('main')?.scrollTo({ top: 0, behavior: 'smooth' }); window.scrollTo({ top: 0, behavior: 'smooth' }); })"
>
    {{-- Step indicator --}}
    <nav class="mb-8">
        <ol class="flex items-center w-full max-w-2xl mx-auto">
            @foreach([
                ['icon' => 'spa', 'label' => 'Service'],
                ['icon' => 'add_box', 'label' => 'Add-ons'],
                ['icon' => 'location_on', 'label' => 'Address'],
                ['icon' => 'event', 'label' => 'Schedule'],
                ['icon' => 'verified', 'label' => 'Confirm'],
            ] as $i => $s)
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
                        'text-[11px] font-semibold tracking-wide uppercase hidden sm:block transition-colors',
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

    {{-- Two-column layout: main content + order summary --}}
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            {{-- Step 1: Select Service / Package --}}
            @if($step === 1)
            <div class="space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-2xl font-headline font-bold text-primary">Choose Your Care</h2>
                    <p class="text-on-surface-variant mt-1">Select a care package or pick an individual service</p>
                </div>

                {{-- Type toggle --}}
                <div class="inline-flex bg-surface-container rounded-full p-1 gap-1">
                    <button
                        wire:click="$set('bookingType', 'package')"
                        @class([
                            'px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300',
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
                            'px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300',
                            'cta-gradient text-on-primary shadow-md' => $bookingType === 'service',
                            'text-on-surface-variant hover:text-on-surface' => $bookingType !== 'service',
                        ])
                    >
                        <span class="material-symbols-outlined text-base align-text-bottom mr-1">medical_services</span>
                        Individual
                    </button>
                </div>

                @if($bookingType === 'package')
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @forelse($packages as $pkg)
                            <button
                                wire:click="selectPackage({{ $pkg->id }})"
                                @class([
                                    'relative text-left rounded-3xl p-6 transition-all duration-300 group',
                                    'bg-primary-fixed ring-2 ring-primary shadow-lg shadow-primary/10 scale-[1.02]' => $selectedPackageId === $pkg->id,
                                    'bg-surface-container-lowest ghost-border hover:shadow-ambient hover:-translate-y-0.5' => $selectedPackageId !== $pkg->id,
                                ])
                            >
                                @if($pkg->is_featured)
                                    <span class="absolute -top-3 right-6 bg-tertiary text-on-tertiary text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full">Popular</span>
                                @endif

                                @if($selectedPackageId === $pkg->id)
                                    <span class="absolute top-4 right-4 material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                @endif

                                <h3 class="text-lg font-headline font-bold text-primary pr-8">{{ $pkg->name }}</h3>
                                <p class="text-sm text-on-surface-variant mt-1.5 mb-4 line-clamp-2">{{ $pkg->description }}</p>

                                <div class="flex items-baseline gap-2 mb-3">
                                    <span class="text-3xl font-bold text-primary">₹{{ number_format($pkg->price) }}</span>
                                    @if($pkg->discount_percent > 0)
                                        <span class="text-xs bg-tertiary-fixed text-on-tertiary-fixed px-2 py-0.5 rounded-full font-semibold">{{ (int) $pkg->discount_percent }}% off</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-4 text-xs text-on-surface-variant">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">event_repeat</span>
                                        {{ $pkg->total_sessions }} sessions
                                    </span>
                                    @if($pkg->services->count())
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">checklist</span>
                                            {{ $pkg->services->count() }} services
                                        </span>
                                    @endif
                                </div>

                                @if($pkg->services->count())
                                    <div class="mt-4 pt-3 space-y-1.5" style="border-top: 1px solid rgba(218, 193, 186, 0.15);">
                                        @foreach($pkg->services->take(3) as $svc)
                                            <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                                                <span class="material-symbols-outlined text-xs text-primary/60">check</span>
                                                {{ $svc->name }}
                                                @if($svc->pivot->session_count > 1)
                                                    <span class="text-on-surface-variant/50">&times;{{ $svc->pivot->session_count }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($pkg->services->count() > 3)
                                            <p class="text-[11px] text-primary/50 pl-5">+{{ $pkg->services->count() - 3 }} more</p>
                                        @endif
                                    </div>
                                @endif
                            </button>
                        @empty
                            <div class="col-span-full text-center py-12 text-on-surface-variant">
                                <span class="material-symbols-outlined text-5xl mb-3 block opacity-30">inventory_2</span>
                                <p>No packages available at the moment.</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($services as $svc)
                            <button
                                wire:click="selectService({{ $svc->id }})"
                                @class([
                                    'text-left rounded-2xl p-5 transition-all duration-300 group',
                                    'bg-primary-fixed ring-2 ring-primary shadow-lg shadow-primary/10' => $selectedServiceId === $svc->id,
                                    'bg-surface-container-lowest ghost-border hover:shadow-ambient hover:-translate-y-0.5' => $selectedServiceId !== $svc->id,
                                ])
                            >
                                <div class="flex items-start gap-4">
                                    <div @class([
                                        'w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 transition-colors',
                                        'bg-primary text-on-primary' => $selectedServiceId === $svc->id,
                                        'bg-primary-fixed text-primary' => $selectedServiceId !== $svc->id,
                                    ])>
                                        <span class="material-symbols-outlined">{{ $svc->icon ?? 'spa' }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <h3 class="font-semibold text-on-surface">{{ $svc->name }}</h3>
                                            @if($selectedServiceId === $svc->id)
                                                <span class="material-symbols-outlined text-primary shrink-0" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                            @endif
                                        </div>
                                        @if($svc->short_description)
                                            <p class="text-sm text-on-surface-variant mt-0.5 line-clamp-2">{{ $svc->short_description }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 mt-2.5">
                                            <span class="text-lg font-bold text-primary">₹{{ number_format($svc->base_price) }}</span>
                                            <span class="text-xs text-on-surface-variant/60 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs">timer</span>
                                                {{ $svc->duration_minutes }} min
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </button>
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

            {{-- Step 2: Add-ons --}}
            @if($step === 2)
            <div class="space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-2xl font-headline font-bold text-primary">Enhance Your Care</h2>
                    <p class="text-on-surface-variant mt-1">Optional add-ons to make your session extra special</p>
                </div>

                @if($addOns->count())
                    <div class="space-y-3">
                        @foreach($addOns as $addOn)
                            <button
                                wire:click="toggleAddOn({{ $addOn->id }})"
                                @class([
                                    'w-full text-left rounded-2xl p-5 flex items-center gap-4 transition-all duration-300',
                                    'bg-primary-fixed ring-2 ring-primary shadow-md' => in_array($addOn->id, $selectedAddOns),
                                    'bg-surface-container-lowest ghost-border hover:shadow-ambient' => !in_array($addOn->id, $selectedAddOns),
                                ])
                            >
                                <div @class([
                                    'w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-all duration-200',
                                    'bg-primary text-on-primary' => in_array($addOn->id, $selectedAddOns),
                                    'bg-surface-container-high' => !in_array($addOn->id, $selectedAddOns),
                                ])>
                                    @if(in_array($addOn->id, $selectedAddOns))
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-on-surface">{{ $addOn->name }}</h3>
                                    @if($addOn->description)
                                        <p class="text-sm text-on-surface-variant mt-0.5">{{ $addOn->description }}</p>
                                    @endif
                                </div>
                                <span class="text-lg font-bold text-primary shrink-0">+₹{{ number_format($addOn->price) }}</span>
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
                    <p class="text-sm text-on-surface-variant/60 text-center italic">You can skip this step if you don't need add-ons</p>
                @endif
            </div>
            @endif

            {{-- Step 3: Address --}}
            @if($step === 3)
            <div class="space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-2xl font-headline font-bold text-primary">Service Address</h2>
                    <p class="text-on-surface-variant mt-1">Where should our caregiver come?</p>
                </div>

                @if($addresses->count() && !$newAddress)
                    <div class="space-y-3">
                        @foreach($addresses as $addr)
                            <button
                                wire:click="$set('selectedAddressId', {{ $addr->id }})"
                                @class([
                                    'w-full text-left rounded-2xl p-5 transition-all duration-300',
                                    'bg-primary-fixed ring-2 ring-primary shadow-md' => $selectedAddressId === $addr->id,
                                    'bg-surface-container-lowest ghost-border hover:shadow-ambient' => $selectedAddressId !== $addr->id,
                                ])
                            >
                                <div class="flex items-start gap-4">
                                    <div @class([
                                        'w-10 h-10 rounded-xl flex items-center justify-center shrink-0',
                                        'bg-primary text-on-primary' => $selectedAddressId === $addr->id,
                                        'bg-surface-container text-on-surface-variant' => $selectedAddressId !== $addr->id,
                                    ])>
                                        <span class="material-symbols-outlined text-lg">
                                            {{ $addr->label === 'Office' ? 'business' : ($addr->label === 'Other' ? 'pin_drop' : 'home') }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-on-surface">{{ $addr->label }}</span>
                                            @if($addr->is_default)
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary-fixed px-2 py-0.5 rounded-full">Default</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-on-surface-variant mt-1">{{ $addr->address_line_1 }}</p>
                                        @if($addr->address_line_2)
                                            <p class="text-sm text-on-surface-variant">{{ $addr->address_line_2 }}</p>
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
                    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 ghost-border space-y-5">
                        @if(!$addresses->count())
                            <div class="flex items-center gap-3 bg-tertiary-fixed/50 text-on-tertiary-fixed rounded-2xl px-4 py-3 mb-2">
                                <span class="material-symbols-outlined text-lg">info</span>
                                <p class="text-sm">You don't have any saved addresses yet. Please add one below.</p>
                            </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-on-surface mb-2 block">Address Label</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Home', 'Office', 'Other'] as $label)
                                    <button
                                        wire:click="$set('addressLabel', '{{ $label }}')"
                                        @class([
                                            'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200',
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
                            <label for="addr1" class="text-sm font-medium text-on-surface mb-1 block">
                                Address Line 1 <span class="text-error">*</span>
                            </label>
                            <input id="addr1" type="text" wire:model.blur="addressLine1" class="input-field" placeholder="Flat / House No., Building Name">
                            @error('addressLine1') <p class="text-sm text-error flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-xs">error</span> {{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="addr2" class="text-sm font-medium text-on-surface mb-1 block">Address Line 2</label>
                            <input id="addr2" type="text" wire:model.blur="addressLine2" class="input-field" placeholder="Street, Area, Locality">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="landmark" class="text-sm font-medium text-on-surface mb-1 block">Landmark</label>
                                <input id="landmark" type="text" wire:model.blur="landmark" class="input-field" placeholder="Near...">
                            </div>
                            <div>
                                <label for="pincode" class="text-sm font-medium text-on-surface mb-1 block">
                                    Pincode <span class="text-error">*</span>
                                </label>
                                <input id="pincode" type="text" wire:model.blur="pincode" class="input-field" maxlength="6" placeholder="400001" inputmode="numeric">
                                @error('pincode') <p class="text-sm text-error flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-xs">error</span> {{ $message }}</p> @enderror
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

            {{-- Step 4: Schedule --}}
            @if($step === 4)
            <div class="space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-2xl font-headline font-bold text-primary">Schedule Your Visit</h2>
                    <p class="text-on-surface-variant mt-1">Pick a date and time that works for you</p>
                </div>

                <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 ghost-border space-y-6">
                    <div>
                        <label for="schedule-date" class="text-sm font-medium text-on-surface mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">calendar_today</span>
                            Preferred Date <span class="text-error">*</span>
                        </label>
                        <input
                            id="schedule-date"
                            type="date"
                            wire:model.live="scheduledDate"
                            class="input-field max-w-xs"
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                        >
                        @error('scheduledDate') <p class="text-sm text-error flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-xs">error</span> {{ $message }}</p> @enderror
                        @if($scheduledDate)
                            <p class="text-sm text-primary mt-2 font-medium">
                                {{ \Carbon\Carbon::parse($scheduledDate)->format('l, F j, Y') }}
                            </p>
                        @endif
                    </div>

                    <div>
                        <label class="text-sm font-medium text-on-surface mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">schedule</span>
                            Preferred Time
                        </label>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-tertiary">wb_sunny</span> Morning
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['08:00', '09:00', '10:00', '11:00'] as $time)
                                        <button
                                            wire:click="$set('scheduledTime', '{{ $time }}')"
                                            @class([
                                                'px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
                                                'cta-gradient text-on-primary shadow-md' => $scheduledTime === $time,
                                                'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' => $scheduledTime !== $time,
                                            ])
                                        >
                                            {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-tertiary">wb_twilight</span> Afternoon
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['12:00', '13:00', '14:00', '15:00'] as $time)
                                        <button
                                            wire:click="$set('scheduledTime', '{{ $time }}')"
                                            @class([
                                                'px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
                                                'cta-gradient text-on-primary shadow-md' => $scheduledTime === $time,
                                                'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' => $scheduledTime !== $time,
                                            ])
                                        >
                                            {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-tertiary">dark_mode</span> Evening
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['16:00', '17:00', '18:00'] as $time)
                                        <button
                                            wire:click="$set('scheduledTime', '{{ $time }}')"
                                            @class([
                                                'px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
                                                'cta-gradient text-on-primary shadow-md' => $scheduledTime === $time,
                                                'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' => $scheduledTime !== $time,
                                            ])
                                        >
                                            {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="text-sm font-medium text-on-surface mb-1 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">edit_note</span>
                            Special Instructions
                        </label>
                        <textarea
                            id="notes"
                            wire:model.blur="notes"
                            class="input-field"
                            rows="3"
                            placeholder="Anything we should know? Allergies, preferences, gate code..."
                        ></textarea>
                    </div>
                </div>
            </div>
            @endif

            {{-- Step 5: Review & Confirm --}}
            @if($step === 5)
            <div class="space-y-6 animate-fade-in">
                <div>
                    <h2 class="text-2xl font-headline font-bold text-primary">Review & Confirm</h2>
                    <p class="text-on-surface-variant mt-1">Make sure everything looks good before booking</p>
                </div>

                {{-- Service summary --}}
                <div class="bg-surface-container-lowest rounded-3xl ghost-border overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid rgba(218, 193, 186, 0.15);">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">spa</span> Care Selected
                        </h3>
                        <button wire:click="goToStep(1)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                    </div>
                    <div class="px-6 py-4">
                        <p class="font-semibold text-on-surface">
                            @if($bookingType === 'package' && $selectedPackageId)
                                {{ \App\Models\Package::find($selectedPackageId)?->name }}
                            @elseif($selectedServiceId)
                                {{ \App\Models\Service::find($selectedServiceId)?->name }}
                            @endif
                        </p>
                        <p class="text-sm text-on-surface-variant mt-0.5 capitalize">{{ $bookingType }}</p>
                    </div>
                </div>

                {{-- Add-ons summary --}}
                @if(count($selectedAddOns))
                <div class="bg-surface-container-lowest rounded-3xl ghost-border overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid rgba(218, 193, 186, 0.15);">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">add_box</span> Add-ons
                        </h3>
                        <button wire:click="goToStep(2)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                    </div>
                    <div class="px-6 py-4 space-y-2">
                        @foreach($selectedAddOns as $id)
                            @php $addOn = \App\Models\AddOn::find($id); @endphp
                            @if($addOn)
                                <div class="flex justify-between text-sm">
                                    <span class="text-on-surface">{{ $addOn->name }}</span>
                                    <span class="text-on-surface-variant">+₹{{ number_format($addOn->price) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Address summary --}}
                <div class="bg-surface-container-lowest rounded-3xl ghost-border overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid rgba(218, 193, 186, 0.15);">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">location_on</span> Address
                        </h3>
                        <button wire:click="goToStep(3)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                    </div>
                    <div class="px-6 py-4">
                        @if($newAddress)
                            <p class="text-sm text-on-surface">{{ $addressLine1 }}</p>
                            @if($addressLine2)<p class="text-sm text-on-surface-variant">{{ $addressLine2 }}</p>@endif
                            <p class="text-xs text-on-surface-variant mt-1">{{ $pincode }}</p>
                        @elseif($selectedAddressId)
                            @php $addr = auth()->user()->addresses()->find($selectedAddressId); @endphp
                            @if($addr)
                                <p class="text-sm font-medium text-on-surface">{{ $addr->label }}</p>
                                <p class="text-sm text-on-surface-variant">{{ $addr->address_line_1 }}</p>
                                <p class="text-xs text-on-surface-variant mt-1">{{ $addr->pincode }}</p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Schedule summary --}}
                <div class="bg-surface-container-lowest rounded-3xl ghost-border overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid rgba(218, 193, 186, 0.15);">
                        <h3 class="font-semibold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">event</span> Schedule
                        </h3>
                        <button wire:click="goToStep(4)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                    </div>
                    <div class="px-6 py-4 flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-on-surface-variant">calendar_today</span>
                            <span class="text-sm font-medium text-on-surface">{{ \Carbon\Carbon::parse($scheduledDate)->format('D, M j, Y') }}</span>
                        </div>
                        @if($scheduledTime)
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-on-surface-variant">schedule</span>
                                <span class="text-sm font-medium text-on-surface">{{ \Carbon\Carbon::parse($scheduledTime)->format('g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="bg-surface-container-lowest rounded-3xl ghost-border overflow-hidden">
                    <div class="px-6 py-5">
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
                            @if($couponMessage)
                                <p class="text-sm text-primary mt-2">{{ $couponMessage }}</p>
                            @endif
                        @else
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    wire:model="couponCode"
                                    class="input-field flex-1 uppercase"
                                    placeholder="Enter code"
                                    wire:keydown.enter="applyCoupon"
                                >
                                <button
                                    wire:click="applyCoupon"
                                    wire:loading.attr="disabled"
                                    class="btn-secondary px-6 shrink-0"
                                >
                                    <span wire:loading.remove wire:target="applyCoupon">Apply</span>
                                    <span wire:loading wire:target="applyCoupon" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        ...
                                    </span>
                                </button>
                            </div>
                            @if($couponMessage && !$couponValid)
                                <p class="text-sm text-error flex items-center gap-1 mt-2">
                                    <span class="material-symbols-outlined text-sm">error</span> {{ $couponMessage }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Navigation buttons --}}
            <div class="flex items-center justify-between mt-8 pt-6" style="border-top: 1px solid rgba(218, 193, 186, 0.15);">
                @if($step > 1)
                    <button wire:click="prevStep" wire:loading.attr="disabled" class="btn-outline group">
                        <span class="material-symbols-outlined mr-1 text-lg transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                        Back
                    </button>
                @else
                    <div></div>
                @endif

                @if($step < 5)
                    <button wire:click="nextStep" wire:loading.attr="disabled" class="btn-primary group">
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

        {{-- Order summary sidebar (desktop) --}}
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
                                </div>
                                <p class="font-bold text-primary shrink-0">
                                    ₹{{ number_format($bookingType === 'package' ? (\App\Models\Package::find($selectedPackageId)?->price ?? 0) : (\App\Models\Service::find($selectedServiceId)?->base_price ?? 0)) }}
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

                        @if($scheduledDate)
                            <div style="border-top: 1px solid rgba(218, 193, 186, 0.15);" class="pt-3">
                                <p class="text-xs uppercase tracking-wider text-on-surface-variant/60 font-semibold mb-1">Schedule</p>
                                <p class="text-sm text-on-surface">{{ \Carbon\Carbon::parse($scheduledDate)->format('M j, Y') }}
                                    @if($scheduledTime) &middot; {{ \Carbon\Carbon::parse($scheduledTime)->format('g:i A') }} @endif
                                </p>
                            </div>
                        @endif

                        <div style="border-top: 2px solid rgba(218, 193, 186, 0.3);" class="pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">Subtotal</span>
                                <span class="text-on-surface">₹{{ number_format($this->getSubtotal()) }}</span>
                            </div>
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

    {{-- Mobile sticky bottom bar --}}
    <div class="fixed bottom-0 inset-x-0 lg:hidden bg-surface-container-lowest/95 backdrop-blur-xl z-40 px-4 py-3 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]" style="border-top: 1px solid rgba(218, 193, 186, 0.2);">
        <div class="flex items-center justify-between max-w-lg mx-auto">
            <div>
                <p class="text-xs text-on-surface-variant">Total</p>
                <p class="text-xl font-bold text-primary">₹{{ number_format($this->getTotal()) }}</p>
            </div>
            @if($step < 5)
                <button wire:click="nextStep" wire:loading.attr="disabled" class="btn-primary px-8">
                    <span wire:loading.remove wire:target="nextStep">Next</span>
                    <span wire:loading wire:target="nextStep">...</span>
                    <span class="material-symbols-outlined ml-1 text-lg">arrow_forward</span>
                </button>
            @else
                <button wire:click="placeBooking" wire:loading.attr="disabled" class="btn-primary px-6">
                    <span wire:loading.remove wire:target="placeBooking">Confirm</span>
                    <span wire:loading wire:target="placeBooking">Booking...</span>
                    <span class="material-symbols-outlined ml-1 text-lg">verified</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Bottom spacer for mobile sticky bar --}}
    <div class="h-20 lg:hidden"></div>
</div>
