<x-app-layout>
    <x-slot:title>{{ __('Daimaa') }} — {{ __('Traditional Indian post-pregnancy care for mothers and newborns, delivered to your home by experienced, verified Daimaa caregivers.') }}</x-slot:title>

    {{-- Subtle film-grain overlay (fixed, pointer-events-none, z-50 — performance-safe) --}}
    <div class="film-grain" aria-hidden="true"></div>

    {{-- =========================================================
         HERO — Editorial asymmetry + Soft Arch motif + dvh fix
         ========================================================= --}}
    <section class="relative min-h-[100dvh] flex items-center pt-16 overflow-hidden bg-surface">
        {{-- Soft Arch motif borrowing from the Daimaa logo's "D" curvature --}}
        <div class="soft-arch hidden lg:block w-[680px] h-[340px] -bottom-10 right-[-180px] opacity-70" aria-hidden="true"></div>
        <div class="soft-arch hidden lg:block w-[420px] h-[210px] top-24 -left-20 rotate-180 opacity-50" aria-hidden="true"></div>

        {{-- Parallax decorative blobs --}}
        <div data-parallax="0.15" class="parallax-blob w-[300px] h-[300px] md:w-[500px] md:h-[500px] bg-primary-fixed/30 -top-20 -right-40"></div>
        <div data-parallax="0.25" class="parallax-blob w-[200px] h-[200px] md:w-[350px] md:h-[350px] bg-tertiary-fixed/20 bottom-20 -left-20"></div>
        <div data-parallax="0.1" class="parallax-blob w-[150px] h-[150px] md:w-[200px] md:h-[200px] bg-secondary-fixed/15 top-1/3 right-1/4"></div>

        <div class="relative max-w-[1400px] mx-auto w-full px-6 lg:px-12 py-12 lg:py-20 z-10">
            {{-- Asymmetric 12-col grid: text 7 / visual 5 — editorial split per soft-skill --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">

                {{-- Mobile logo (above text on small screens, with the soft-arch behind) --}}
                <div class="relative flex flex-col items-center gap-5 lg:hidden order-1" data-reveal="scale" data-reveal-delay="0">
                    <div class="absolute inset-x-0 top-6 h-40 mx-auto w-[80%] rounded-t-full bg-primary-fixed/30 blur-xl" aria-hidden="true"></div>
                    <img src="/images/logo.png" alt="{{ __('Daimaa') }}" class="relative w-48 h-48 sm:w-60 sm:h-60 object-contain drop-shadow-xl animate-float-slow">
                </div>

                {{-- Text content — col-span-7, left aligned editorial --}}
                <div class="space-y-7 lg:space-y-9 text-center lg:text-left order-2 lg:col-span-7" data-reveal data-reveal-delay="80">
                    <span class="eyebrow-primary mx-auto lg:mx-0">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
                        {{ __('Trusted by 12,000+ Families') }}
                    </span>

                    <h1 class="font-headline font-bold leading-[1.02] tracking-[-0.02em] text-[clamp(2.5rem,6vw,5.5rem)]">
                        <span class="text-primary block">{{ __('The Sacred Choice') }}</span>
                        <span class="text-gradient-primary italic font-normal block">{{ __('of Nurture') }}</span>
                    </h1>

                    <p class="text-lg sm:text-xl text-on-surface-variant leading-relaxed max-w-[58ch] mx-auto lg:mx-0">
                        {{ __('Traditional Indian post-pregnancy care for mothers and newborns, delivered to your home by experienced, verified Daimaa caregivers.') }}
                    </p>

                    {{-- Primary actions with island-icon button architecture --}}
                    <div class="flex flex-wrap gap-3 sm:gap-4 justify-center lg:justify-start">
                        <a href="{{ route('services') }}" class="btn-primary group text-sm sm:text-base lg:text-lg pl-5 sm:pl-7 pr-1.5 sm:pr-2 py-1.5 sm:py-2">
                            <span class="mr-2 sm:mr-3">{{ __('Explore Services') }}</span>
                            <span class="btn-island-icon !h-7 !w-7 sm:!h-8 sm:!w-8">
                                <span class="material-symbols-outlined text-sm sm:text-base">arrow_forward</span>
                            </span>
                        </a>
                        <a href="{{ route('how-it-works') }}" class="btn-outline text-sm sm:text-base lg:text-lg px-5 sm:px-7 lg:px-8 py-2.5 sm:py-3 lg:py-4">{{ __('How It Works') }}</a>
                    </div>

                    {{-- Pincode checker (preserved with refined styling) --}}
                    <div class="pt-2 lg:pt-4" x-data="{ pincode: '', result: null, checking: false }">
                        <p class="text-sm text-on-surface-variant mb-2">{{ __('Check if we serve your area:') }}</p>
                        <div class="flex gap-2 max-w-md mx-auto lg:mx-0">
                            <input type="text" x-model="pincode" placeholder="{{ __('Enter your pincode') }}" maxlength="6" inputmode="numeric" class="input-field flex-1">
                            <button @click="checking = true; fetch('/api/check-pincode/' + pincode).then(r => r.json()).then(d => { result = d; checking = false; }).catch(() => { result = { available: pincode.length === 6 }; checking = false; })" class="btn-secondary px-6 whitespace-nowrap" :disabled="checking">
                                <span x-show="!checking">{{ __('Check') }}</span>
                                <span x-show="checking" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                            </button>
                        </div>
                        <p x-show="result" x-cloak class="mt-2 text-sm font-medium" :class="result?.available ? 'text-primary' : 'text-error'">
                            <span x-show="result?.available" class="inline-flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                {{ __('We serve your area! Book now.') }}
                            </span>
                            <span x-show="!result?.available" class="inline-flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">schedule</span>
                                {{ __("We're expanding soon to your area. Join our waitlist!") }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Desktop visual — col-span-5, with WebGL + Soft Arch frame --}}
                <div class="relative hidden lg:flex items-center justify-center min-h-[560px] order-3 lg:col-span-5">
                    {{-- The Soft Arch frame (Modern Heritage signature) --}}
                    <div class="absolute inset-0 flex items-end justify-center pointer-events-none">
                        <div class="w-[420px] h-[420px] rounded-t-full bg-gradient-to-b from-primary-fixed/40 via-tertiary-fixed/15 to-transparent" aria-hidden="true"></div>
                    </div>
                    <div id="hero-webgl" class="hero-webgl-canvas rounded-[2.5rem]"></div>

                    {{-- Logo --}}
                    <div class="relative z-10 pointer-events-none">
                        <img src="/images/logo.png" alt="{{ __('Daimaa') }}" class="w-[440px] h-[440px] object-contain drop-shadow-2xl animate-float-slow">
                    </div>

                    {{-- Floating verification card (double-bezel style) --}}
                    <div class="absolute -bottom-2 -left-4 z-20" data-reveal="scale" data-reveal-delay="400">
                        <div class="bezel-shell">
                            <div class="bezel-core flex items-center gap-3 !p-3.5">
                                <div class="h-11 w-11 bg-primary-fixed rounded-2xl flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">shield</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface leading-tight">{{ __('500+ Verified Daimaas') }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ __('Background checked & certified') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating rating chip --}}
                    <div class="absolute top-2 -right-2 z-20" data-reveal="scale" data-reveal-delay="600">
                        <div class="bezel-shell">
                            <div class="bezel-core flex items-center gap-2 !p-3 !rounded-full">
                                <span class="material-symbols-outlined text-tertiary text-lg" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="text-sm font-bold text-on-surface">{{ __('4.9 Rating') }}</span>
                                <span class="text-xs text-on-surface-variant">/ 12k+</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll cue --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 hidden lg:flex flex-col items-center gap-2 text-on-surface-variant/60" aria-hidden="true">
            <span class="text-[10px] uppercase tracking-[0.3em]">{{ __('Discover') }}</span>
            <span class="material-symbols-outlined text-base animate-float-medium">expand_more</span>
        </div>
    </section>

    {{-- =========================================================
         TRUST STRIP — Editorial inline layout, tonal dividers
         (Replaces generic 4-col centered grid)
         ========================================================= --}}
    <section class="bg-surface-container-low py-16 lg:py-20 relative overflow-hidden">
        <div data-parallax="0.08" class="parallax-blob w-[300px] h-[300px] bg-primary-fixed/10 -top-10 left-1/4"></div>
        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8 mb-10" data-reveal>
                <div class="max-w-md">
                    <span class="eyebrow-tertiary mb-4 inline-flex">{{ __('A Quiet Revolution') }}</span>
                    <p class="text-2xl lg:text-3xl font-headline text-on-surface leading-snug">
                        {{ __('Numbers that speak softly,') }} <span class="text-primary italic">{{ __('but carry generations.') }}</span>
                    </p>
                </div>
                <p class="text-sm text-on-surface-variant max-w-xs leading-relaxed lg:text-right">
                    {{ __('Every figure here is the trust of a family who chose tradition with confidence.') }}
                </p>
            </div>

            {{-- Inline metrics with tonal separators (no 1px lines) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 lg:divide-x lg:divide-outline-variant/15">
                <div class="px-2 lg:px-8 py-4" data-reveal data-reveal-delay="0">
                    <p class="font-headline font-bold text-primary leading-none text-[clamp(2.5rem,5vw,4rem)]" data-count-to="500" data-count-suffix="+">0</p>
                    <p class="text-sm text-on-surface-variant mt-3">{{ __('Verified Daimaas') }}</p>
                </div>
                <div class="px-2 lg:px-8 py-4" data-reveal data-reveal-delay="100">
                    <p class="font-headline font-bold text-tertiary leading-none text-[clamp(2.5rem,5vw,4rem)]" data-count-to="12000" data-count-suffix="+">0</p>
                    <p class="text-sm text-on-surface-variant mt-3">{{ __('Families Served') }}</p>
                </div>
                <div class="px-2 lg:px-8 py-4" data-reveal data-reveal-delay="200">
                    <p class="font-headline font-bold text-secondary leading-none text-[clamp(2.5rem,5vw,4rem)]" data-count-to="4" data-count-suffix="">0</p>
                    <p class="text-sm text-on-surface-variant mt-3">{{ __('Cities Active') }}</p>
                </div>
                <div class="px-2 lg:px-8 py-4" data-reveal data-reveal-delay="300">
                    <p class="font-headline font-bold text-primary leading-none text-[clamp(2.5rem,5vw,4rem)]" data-count-to="98" data-count-suffix="%">0</p>
                    <p class="text-sm text-on-surface-variant mt-3">{{ __('Satisfaction Rate') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
         SERVICES — Asymmetric zig-zag Bento grid + Double-Bezel
         (Replaces generic 3-col card row, banned by taste-skill)
         ========================================================= --}}
    <section class="py-16 sm:py-24 lg:py-32 bg-surface relative overflow-hidden">
        <div data-parallax="0.12" class="parallax-blob w-[400px] h-[400px] bg-tertiary-fixed/10 -bottom-20 -right-20"></div>
        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-12">
            {{-- Editorial header — left-aligned, anti-center bias --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-14 lg:mb-20 items-end">
                <div class="lg:col-span-7" data-reveal>
                    <span class="eyebrow-tertiary mb-5 inline-flex">{{ __('Our Care Catalogue') }}</span>
                    <h2 class="text-4xl lg:text-6xl font-headline font-bold text-primary leading-[1.05] tracking-tight">
                        {{ __('Rituals of recovery,') }}<br>
                        <span class="italic font-normal text-on-surface">{{ __('crafted with tradition.') }}</span>
                    </h2>
                </div>
                <div class="lg:col-span-5 lg:pb-3" data-reveal data-reveal-delay="120">
                    <p class="text-base lg:text-lg text-on-surface-variant leading-relaxed max-w-md lg:ml-auto">
                        {{ __('Each service is rooted in generations of Indian maternal wisdom — and delivered with the precision of modern home care.') }}
                    </p>
                </div>
            </div>

            {{-- Asymmetric Bento grid: zig-zag 7/5 split --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6">
                @foreach($services as $service)
                    @php $isFeatured = $loop->index % 2 === 0; @endphp
                    <a href="{{ route('services.detail', $service->slug) }}"
                       class="group bento-card lg:col-span-{{ $isFeatured ? '7' : '5' }}"
                       data-reveal data-reveal-delay="{{ $loop->index * 80 }}">
                        <div class="bezel-shell h-full">
                            <div class="bezel-core h-full flex flex-col justify-between min-h-[220px] {{ $isFeatured ? 'lg:min-h-[280px]' : 'lg:min-h-[220px]' }} {{ $loop->index === 0 ? '!bg-primary-fixed/30' : '' }}">
                                <div>
                                    {{-- Service icon in arched frame --}}
                                    <div class="h-14 w-14 bg-primary/10 group-hover:bg-primary group-hover:text-on-primary rounded-2xl rounded-tl-[2.5rem] flex items-center justify-center mb-6 spring-transition">
                                        <span class="material-symbols-outlined text-primary text-2xl group-hover:text-on-primary spring-transition">{{ $service->icon ?? 'spa' }}</span>
                                    </div>
                                    <h3 class="text-2xl {{ $isFeatured ? 'lg:text-3xl' : 'lg:text-2xl' }} font-headline font-bold text-primary mb-3 leading-tight">{{ $service->name }}</h3>
                                    <p class="text-on-surface-variant text-sm leading-relaxed max-w-[44ch]">{{ $service->short_description }}</p>
                                </div>
                                <div class="flex items-center justify-between pt-6 mt-6">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-2xl font-bold text-primary">&#8377;{{ number_format($service->base_price) }}</span>
                                        <span class="text-xs text-on-surface-variant">/ {{ $service->duration_minutes }} {{ __('min') }}</span>
                                    </div>
                                    <span class="btn-island-icon-dark">
                                        <span class="material-symbols-outlined text-primary text-base">arrow_forward</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-14 lg:mt-16 flex justify-center" data-reveal>
                <a href="{{ route('services') }}" class="btn-outline group text-base">
                    {{ __('View All Services & Packages') }}
                    <span class="material-symbols-outlined ml-2 text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    {{-- =========================================================
         CARE JOURNEY ARCH — Signature component from DESIGN.md
         Semi-circular gold-stroke arch with 4 milestones
         ========================================================= --}}
    <section class="py-16 sm:py-24 lg:py-32 bg-surface-container-low relative overflow-hidden">
        <div data-parallax="0.1" class="parallax-blob w-[450px] h-[450px] bg-tertiary-fixed/15 top-10 -left-20"></div>
        <div data-parallax="0.18" class="parallax-blob w-[300px] h-[300px] bg-primary-fixed/15 bottom-0 -right-10"></div>

        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="text-center max-w-2xl mx-auto mb-16" data-reveal>
                <span class="eyebrow-primary mb-5 inline-flex">{{ __('The Daimaa Continuum') }}</span>
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary leading-tight">
                    {{ __('From the third trimester') }}<br>
                    <span class="italic font-normal text-on-surface">{{ __('to a confident fortieth day.') }}</span>
                </h2>
                <p class="mt-6 text-lg text-on-surface-variant leading-relaxed">
                    {{ __('A guided arch of care, traced through the most tender chapters of new motherhood.') }}
                </p>
            </div>

            @php
                $journey = [
                    [
                        'icon' => 'favorite',
                        'title' => __('Trimester 3'),
                        'phase' => __('Pregnancy'),
                        'caption' => __('Birth prep visits, gentle abdominal massage, postpartum education.')
                    ],
                    [
                        'icon' => 'child_friendly',
                        'title' => __('Days 1-7'),
                        'phase' => __('Birth Prep'),
                        'caption' => __('Newborn bathing, latching support, healing herbal compresses.')
                    ],
                    [
                        'icon' => 'self_improvement',
                        'title' => __('Weeks 2-6'),
                        'phase' => __('Postpartum'),
                        'caption' => __('Belly binding, lactation rhythm, and warming nourishment rituals.')
                    ],
                    [
                        'icon' => 'spa',
                        'title' => __('Day 40'),
                        'phase' => __('Recovery'),
                        'caption' => __('Restoration ceremony, body alignment, and the quiet return of strength.')
                    ],
                ];
            @endphp

            {{-- DESKTOP: Horizontal Care Progress Arch with grid-aligned milestone cards --}}
            <div class="hidden md:block relative mx-auto max-w-5xl" data-reveal data-reveal-delay="200">
                {{-- Arch SVG: dot positions (12.5%, 37.5%, 62.5%, 87.5%) align with 4-col grid centers below --}}
                <svg class="care-arch w-full" viewBox="0 0 1000 280" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                    {{-- Background track --}}
                    <path class="care-arch-track" d="M 125 240 Q 500 -120 875 240" />
                    {{-- Animated gold progress (drawn on reveal) --}}
                    <path class="care-arch-progress" d="M 125 240 Q 500 -120 875 240" vector-effect="non-scaling-stroke" />

                    {{-- Milestone nodes — y values computed on quadratic bezier B(t)=(1-t)²P0+2(1-t)tP1+t²P2 so dots sit exactly on the arch --}}
                    @php
                        $archNodes = [
                            ['x' => 125, 'y' => 240],
                            ['x' => 375, 'y' => 80],
                            ['x' => 625, 'y' => 80],
                            ['x' => 875, 'y' => 240],
                        ];
                    @endphp
                    @foreach($archNodes as $i => $node)
                        <g>
                            <circle class="care-arch-milestone" cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="22" />
                            <text x="{{ $node['x'] }}" y="{{ $node['y'] + 6 }}" text-anchor="middle" fill="#93452d" font-family="'Noto Serif', serif" font-size="18" font-weight="700">{{ $i + 1 }}</text>
                        </g>
                    @endforeach
                </svg>

                {{-- Milestone cards — 4-col grid aligned with arch nodes --}}
                <div class="grid grid-cols-4 gap-4 lg:gap-6 mt-2">
                    @foreach($journey as $i => $step)
                        <div class="text-center" data-reveal data-reveal-delay="{{ 400 + $i * 150 }}">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full rounded-tl-[1.25rem] bg-primary/10 text-primary mb-3">
                                <span class="material-symbols-outlined text-base">{{ $step['icon'] }}</span>
                            </span>
                            <p class="text-[10px] uppercase tracking-[0.2em] font-semibold text-tertiary mb-1">{{ $step['phase'] }}</p>
                            <p class="text-base font-headline font-bold text-primary">{{ $step['title'] }}</p>
                            <p class="text-xs text-on-surface-variant mt-2 leading-relaxed max-w-[24ch] mx-auto">{{ $step['caption'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- MOBILE: Vertical timeline with curved gold connector + bezel cards --}}
            <ol class="md:hidden relative max-w-md mx-auto" data-reveal data-reveal-delay="150">
                {{-- Vertical gold progress line --}}
                <div class="absolute left-[1.875rem] top-8 bottom-8 w-0.5 bg-gradient-to-b from-tertiary-fixed-dim via-tertiary to-tertiary-fixed-dim opacity-60 rounded-full" aria-hidden="true"></div>

                @foreach($journey as $i => $step)
                    <li class="relative pl-20 pb-8 last:pb-0" data-reveal data-reveal-delay="{{ 200 + $i * 120 }}">
                        {{-- Numbered milestone node --}}
                        <div class="absolute left-0 top-0 w-[3.75rem] flex justify-center">
                            <div class="bezel-shell !p-1 !rounded-full">
                                <div class="relative h-12 w-12 bg-surface-container-lowest rounded-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary text-lg">{{ $step['icon'] }}</span>
                                    <span class="absolute -top-1.5 -right-1.5 h-6 w-6 cta-gradient text-on-primary rounded-full flex items-center justify-center text-xs font-bold font-headline shadow-sm">{{ $i + 1 }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Content card --}}
                        <div class="bezel-shell">
                            <div class="bezel-core !p-5">
                                <p class="text-[10px] uppercase tracking-[0.22em] font-semibold text-tertiary mb-1">{{ $step['phase'] }}</p>
                                <p class="font-headline font-bold text-primary text-lg leading-tight">{{ $step['title'] }}</p>
                                <p class="text-sm text-on-surface-variant mt-2 leading-relaxed">{{ $step['caption'] }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- =========================================================
         HOW IT WORKS — Editorial timeline with connecting line
         (Replaces generic 4-col centered grid)
         ========================================================= --}}
    <section class="py-16 sm:py-24 lg:py-32 bg-surface relative overflow-hidden">
        <div data-parallax="0.15" class="parallax-blob w-[350px] h-[350px] bg-primary-fixed/10 top-10 -left-20"></div>
        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-12">
            {{-- Editorial header — split layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16 lg:mb-20 items-end">
                <div class="lg:col-span-6" data-reveal>
                    <span class="eyebrow-secondary mb-5 inline-flex">{{ __('How It Works') }}</span>
                    <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary leading-[1.05] tracking-tight">
                        {{ __('Four quiet steps') }}<br>
                        <span class="italic font-normal text-on-surface">{{ __('to a household at ease.') }}</span>
                    </h2>
                </div>
                <div class="lg:col-span-5 lg:col-start-8 lg:pb-3" data-reveal data-reveal-delay="120">
                    <p class="text-base lg:text-lg text-on-surface-variant leading-relaxed">
                        {{ __('From the first tap to the first cradle — every step is gentle, transparent, and in your hands.') }}
                    </p>
                </div>
            </div>

            {{-- Connecting tonal line (desktop only, behind cards) --}}
            <div class="relative">
                <div class="hidden lg:block absolute top-12 left-[12.5%] right-[12.5%] h-px bg-gradient-to-r from-transparent via-tertiary-fixed-dim/60 to-transparent" aria-hidden="true"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 relative">
                    @foreach([
                        ['icon' => 'search', 'title' => __('Choose Service'), 'desc' => __('Browse our services and select a package that fits your needs.')],
                        ['icon' => 'calendar_month', 'title' => __('Schedule Visit'), 'desc' => __('Pick a date and time that works for you. We come to your home.')],
                        ['icon' => 'verified_user', 'title' => __('Meet Your Daimaa'), 'desc' => __('A verified, experienced Daimaa is assigned to your booking.')],
                        ['icon' => 'spa', 'title' => __('Enjoy Care'), 'desc' => __('Relax as traditional care is delivered with warmth and expertise.')],
                    ] as $i => $step)
                    <div class="text-center" data-reveal data-reveal-delay="{{ $i * 150 }}">
                        <div class="relative inline-block mb-6">
                            {{-- Double-bezel arched icon ring --}}
                            <div class="bezel-shell !p-1.5 !rounded-full">
                                <div class="h-24 w-24 bg-surface-container-lowest rounded-full flex items-center justify-center spring-transition group-hover:scale-105">
                                    <span class="material-symbols-outlined text-primary text-3xl">{{ $step['icon'] }}</span>
                                </div>
                            </div>
                            {{-- Step number badge --}}
                            <span class="absolute -top-1 -right-1 h-9 w-9 cta-gradient text-on-primary rounded-full flex items-center justify-center text-sm font-bold shadow-lg font-headline">{{ $i + 1 }}</span>
                        </div>
                        <h3 class="text-lg font-headline font-bold text-primary mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-on-surface-variant leading-relaxed max-w-[28ch] mx-auto">{{ $step['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
         FEATURED PACKAGES — Double-Bezel + featured cascade
         ========================================================= --}}
    <section class="py-16 sm:py-24 lg:py-32 bg-surface-container-low relative overflow-hidden">
        <div data-parallax="0.2" class="parallax-blob w-[500px] h-[500px] bg-primary-fixed/10 -bottom-40 right-0"></div>
        <div data-parallax="0.1" class="parallax-blob w-[250px] h-[250px] bg-tertiary-fixed/15 top-20 -left-10"></div>

        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16 lg:mb-20 items-end">
                <div class="lg:col-span-7" data-reveal>
                    <span class="eyebrow-primary mb-5 inline-flex">{{ __('Care Packages') }}</span>
                    <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary leading-[1.05] tracking-tight">
                        {{ __('Bundled wisdom,') }}<br>
                        <span class="italic font-normal text-on-surface">{{ __('priced for peace of mind.') }}</span>
                    </h2>
                </div>
                <div class="lg:col-span-4 lg:col-start-9 lg:pb-3" data-reveal data-reveal-delay="120">
                    <p class="text-base lg:text-lg text-on-surface-variant leading-relaxed">
                        {{ __('Sessions stitched together for the rhythms of healing — no surprises, no overcharge.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-6 lg:gap-8 pt-4 md:pt-0">
                @foreach($packages as $package)
                    @php $isPopular = $loop->index === 1; @endphp
                    <div class="bento-card relative {{ $isPopular ? 'md:-translate-y-6' : '' }}" data-reveal data-reveal-delay="{{ $loop->index * 150 }}">
                        @if($isPopular)
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 z-10 px-4 py-1.5 cta-gradient text-on-primary text-[10px] sm:text-xs font-bold rounded-full shadow-lg uppercase tracking-wider whitespace-nowrap">{{ __('Most Popular') }}</div>
                        @endif

                        <div class="bezel-shell-lg h-full {{ $isPopular ? 'ring-1 ring-primary/30' : '' }}">
                            <div class="bezel-core-lg h-full flex flex-col {{ $isPopular ? '!bg-primary-fixed/20' : '' }}">
                                <h3 class="text-2xl lg:text-3xl font-headline font-bold text-primary mb-3 leading-tight">{{ $package->name }}</h3>
                                <p class="text-sm text-on-surface-variant mb-6 flex-1 leading-relaxed">{{ $package->description }}</p>

                                <div class="mb-6">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-4xl lg:text-5xl font-headline font-bold text-primary">&#8377;{{ number_format($package->price) }}</span>
                                    </div>
                                    <p class="text-xs text-on-surface-variant mt-1">{{ $package->total_sessions }} {{ __('sessions, scheduled at your pace') }}</p>
                                </div>

                                @if($package->discount_percent > 0)
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-tertiary-fixed/40 rounded-full mb-6 w-fit">
                                    <span class="material-symbols-outlined text-tertiary text-sm" style="font-variation-settings: 'FILL' 1;">local_offer</span>
                                    <span class="text-xs font-bold text-tertiary">{{ __('Save') }} {{ number_format($package->discount_percent) }}%</span>
                                </div>
                                @endif

                                <ul class="space-y-3 mb-8">
                                    @foreach($package->services as $svc)
                                    <li class="flex items-start gap-2.5 text-sm text-on-surface-variant">
                                        <span class="material-symbols-outlined text-primary text-base mt-0.5 shrink-0" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        <span>{{ $svc->name }} <span class="text-on-surface-variant/70">&times; {{ $svc->pivot->session_count }}</span></span>
                                    </li>
                                    @endforeach
                                </ul>

                                @if($isPopular)
                                <a href="{{ route('register') }}" class="btn-primary group w-full pl-6 pr-2 py-2 text-base">
                                    <span class="mr-3">{{ __('Book This Package') }}</span>
                                    <span class="btn-island-icon">
                                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                                    </span>
                                </a>
                                @else
                                <a href="{{ route('register') }}" class="btn-outline group w-full">
                                    {{ __('Book This Package') }}
                                    <span class="material-symbols-outlined ml-2 text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =========================================================
         TESTIMONIALS — Featured editorial quote + horizontal marquee
         (Replaces generic 4-col grid, banned by taste-skill)
         ========================================================= --}}
    <section class="py-16 sm:py-24 lg:py-32 bg-surface relative overflow-hidden">
        <div data-parallax="0.12" class="parallax-blob w-[400px] h-[400px] bg-secondary-fixed/10 top-0 right-1/4"></div>

        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-14 items-end">
                <div class="lg:col-span-7" data-reveal>
                    <span class="eyebrow-tertiary mb-5 inline-flex">{{ __('Voices of Trust') }}</span>
                    <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary leading-[1.05] tracking-tight">
                        {{ __('Mothers, midwives,') }}<br>
                        <span class="italic font-normal text-on-surface">{{ __('and the warmth in between.') }}</span>
                    </h2>
                </div>
                <div class="lg:col-span-4 lg:col-start-9 lg:pb-3" data-reveal data-reveal-delay="120">
                    <p class="text-base lg:text-lg text-on-surface-variant leading-relaxed">
                        {{ __('A few words from the families who let us into their most tender season.') }}
                    </p>
                </div>
            </div>

            @if($testimonials->count())
                @php $featured = $testimonials->first(); @endphp
                {{-- Featured editorial quote --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12" data-reveal>
                    <div class="lg:col-span-8">
                        <div class="bezel-shell-lg">
                            <div class="bezel-core-lg !p-10 lg:!p-14 relative">
                                <span class="absolute top-6 right-8 material-symbols-outlined text-primary/15 text-[120px] leading-none select-none" aria-hidden="true">format_quote</span>
                                <div class="flex gap-1 mb-6">
                                    @for($s = 0; $s < $featured->rating; $s++)
                                        <span class="material-symbols-outlined text-tertiary text-xl" style="font-variation-settings: 'FILL' 1">star</span>
                                    @endfor
                                </div>
                                <p class="font-headline text-2xl lg:text-3xl text-on-surface leading-snug italic mb-8 max-w-[42ch] relative z-10">
                                    &ldquo;{{ $featured->content }}&rdquo;
                                </p>
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-14 bg-primary-fixed rounded-full rounded-tl-[1.25rem] flex items-center justify-center text-lg font-bold text-primary font-headline">
                                        {{ strtoupper(substr($featured->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-on-surface">{{ $featured->name }}</p>
                                        <p class="text-sm text-on-surface-variant">{{ $featured->city }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Side stat / supporting context --}}
                    <div class="lg:col-span-4 flex flex-col justify-center gap-6">
                        <div class="bezel-shell">
                            <div class="bezel-core !p-6">
                                <p class="font-headline text-5xl font-bold text-primary leading-none">4.9<span class="text-2xl text-tertiary">/5</span></p>
                                <p class="text-sm text-on-surface-variant mt-3 leading-relaxed">{{ __('Average rating across') }} <span class="font-semibold text-on-surface">{{ __('12,000+ visits') }}</span></p>
                            </div>
                        </div>
                        <div class="bezel-shell">
                            <div class="bezel-core !p-6">
                                <p class="font-headline text-5xl font-bold text-tertiary leading-none">96<span class="text-2xl text-primary">%</span></p>
                                <p class="text-sm text-on-surface-variant mt-3 leading-relaxed">{{ __('of mothers re-book a Daimaa for the next stage of care.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horizontal marquee (anti-3-col-card layout) --}}
                @if($testimonials->count() > 1)
                <div class="marquee-mask py-4" data-reveal>
                    <div class="marquee-track">
                        @foreach($testimonials->skip(1)->concat($testimonials->skip(1)) as $testimonial)
                            <div class="bezel-shell w-[320px] sm:w-[380px] flex-shrink-0">
                                <div class="bezel-core !p-6 h-full flex flex-col">
                                    <div class="flex gap-0.5 mb-3">
                                        @for($s = 0; $s < $testimonial->rating; $s++)
                                            <span class="material-symbols-outlined text-tertiary text-base" style="font-variation-settings: 'FILL' 1">star</span>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-on-surface-variant leading-relaxed mb-5 flex-1 italic">&ldquo;{{ $testimonial->content }}&rdquo;</p>
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 bg-secondary-container rounded-full flex items-center justify-center text-sm font-bold text-on-secondary-container font-headline">
                                            {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-on-surface leading-tight">{{ $testimonial->name }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ $testimonial->city }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
        </div>
    </section>

    {{-- =========================================================
         FAQ — Editorial accordion (kept, refined typography)
         ========================================================= --}}
    <section class="py-16 sm:py-24 lg:py-32 bg-surface-container-low">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
                {{-- Sticky editorial header --}}
                <div class="lg:col-span-4 lg:sticky lg:top-24 lg:self-start" data-reveal>
                    <span class="eyebrow-secondary mb-5 inline-flex">{{ __('Common Questions') }}</span>
                    <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary leading-[1.05] tracking-tight mb-6">
                        {{ __('Curiosities,') }}<br>
                        <span class="italic font-normal text-on-surface">{{ __('answered with care.') }}</span>
                    </h2>
                    <p class="text-base text-on-surface-variant leading-relaxed mb-8">
                        {{ __('Everything you need to know — from booking your first visit to navigating the postpartum weeks.') }}
                    </p>
                    <a href="{{ route('faq') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary spring-transition hover:gap-3 hover:text-primary-container">
                        {{ __('View all FAQs') }}
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>

                {{-- Accordion column --}}
                <div class="lg:col-span-8 space-y-3" x-data="{ open: 0 }">
                    @foreach($faqs as $index => $faq)
                    <div class="bezel-shell !p-1" data-reveal data-reveal-delay="{{ $index * 60 }}">
                        <div class="bezel-core !p-0 overflow-hidden !rounded-[1.625rem]">
                            <button @click="open = open === {{ $index }} ? null : {{ $index }}" class="w-full px-6 py-5 lg:px-8 lg:py-6 flex items-center justify-between text-left group spring-transition hover:bg-primary-fixed/15">
                                <span class="font-headline text-lg font-semibold text-on-surface pr-4 group-hover:text-primary spring-transition">{{ $faq->question }}</span>
                                <span class="material-symbols-outlined text-primary shrink-0 spring-transition" :class="open === {{ $index }} ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="open === {{ $index }}" x-collapse x-cloak>
                                <div class="px-6 pb-6 lg:px-8 lg:pb-7 text-sm text-on-surface-variant leading-relaxed max-w-[68ch]">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
         BECOME A DAIMAA — Refined arched framing + double-bezel
         ========================================================= --}}
    <section class="py-16 sm:py-24 lg:py-32 bg-surface-container-high relative overflow-hidden">
        <div data-parallax="0.18" class="parallax-blob w-[400px] h-[400px] bg-primary-fixed/15 -top-20 -right-20"></div>
        <div data-parallax="0.12" class="parallax-blob w-[300px] h-[300px] bg-tertiary-fixed/10 -bottom-10 -left-10"></div>

        <div class="relative max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                <div class="lg:col-span-6" data-reveal="left">
                    {{-- Asymmetric bezel that follows the image's arched corner --}}
                    <div class="relative rounded-[2rem] sm:rounded-[2.5rem] rounded-br-[3.5rem] sm:rounded-br-[6.5rem] p-2 bg-surface-container/60 border border-tertiary-fixed-dim/20">
                        <img
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBU_wPjBhJnU-BWxL-55ElqksATgRvmk2zGEsvX8VQFGsfw8JX6_qxI7zgSZjyikNf3YWFfSWuMz1mAOpz85smLt8gEj7x55-EDCZ1b3UOn8y31e2r4H-dDtbnCvb3D1k85qvwlLocsy2zZIyxU1Ye9P1fGsTjxLnGnFmCV4IEGu7J2R7mu09kEPZvh8Evu687RLe03c3AlASf-VX2KfJG7_aHqaww6HmoBlJEfDObZqhGnQiUK4WDp2flIhsX4YBLBkH6P3Npq3e6k"
                            alt="{{ __('Apply to Join') }}"
                            class="w-full aspect-[4/5] sm:aspect-[4/3] object-cover rounded-[1.5rem] sm:rounded-[2rem] rounded-br-[3rem] sm:rounded-br-[6rem]"
                        >
                        {{-- Soft corner accent dot --}}
                        <span class="absolute top-4 left-4 h-2 w-2 rounded-full bg-tertiary/70" aria-hidden="true"></span>
                    </div>
                </div>

                <div class="lg:col-span-6 space-y-7" data-reveal="right">
                    <span class="eyebrow-tertiary inline-flex">{{ __('For Caregivers') }}</span>
                    <h2 class="text-4xl lg:text-6xl font-headline font-bold text-primary leading-[1.05] tracking-tight">
                        {{ __('A legacy of care,') }}<br>
                        <span class="italic font-normal text-on-surface">{{ __('a future of impact.') }}</span>
                    </h2>
                    <p class="text-lg text-on-surface-variant leading-relaxed max-w-xl">
                        {{ __("Join India's most respected collective of traditional maternal caregivers. We value your wisdom, support your growth, and connect you with families who need you.") }}
                    </p>

                    {{-- Three pillars in tonal cards (no 1px sectioning) --}}
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 pt-2">
                        @php
                            $pillars = [
                                ['icon' => 'verified_user', 'title' => __('Verified'), 'caption' => __('Profile & KYC'), 'color' => 'primary'],
                                ['icon' => 'payments', 'title' => __('Fair Pay'), 'caption' => __('Competitive rates'), 'color' => 'tertiary'],
                                ['icon' => 'school', 'title' => __('Growth'), 'caption' => __('Training & support'), 'color' => 'secondary'],
                            ];
                        @endphp
                        @foreach($pillars as $pillar)
                            <div class="bezel-shell !p-1">
                                <div class="bezel-core !p-4 text-center">
                                    <span class="material-symbols-outlined text-{{ $pillar['color'] }} text-xl mb-2 inline-block">{{ $pillar['icon'] }}</span>
                                    <p class="text-base sm:text-lg font-headline font-bold text-{{ $pillar['color'] }}">{{ $pillar['title'] }}</p>
                                    <p class="text-[11px] sm:text-xs text-on-surface-variant mt-1">{{ $pillar['caption'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('register') }}" class="btn-primary group text-base sm:text-lg pl-7 pr-2 py-2 inline-flex">
                        <span class="mr-3">{{ __('Apply to Join') }}</span>
                        <span class="btn-island-icon">
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================
         CITIES — Editorial typographic treatment
         ========================================================= --}}
    <section class="py-16 sm:py-24 lg:py-32 bg-surface relative overflow-hidden">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12 lg:mb-16 items-end">
                <div class="lg:col-span-7" data-reveal>
                    <span class="eyebrow-primary mb-5 inline-flex">{{ __('Where We Care') }}</span>
                    <h2 class="text-4xl lg:text-6xl font-headline font-bold text-primary leading-[1.05] tracking-tight">
                        {{ __('Four cities today,') }}<br>
                        <span class="italic font-normal text-on-surface">{{ __('your home next.') }}</span>
                    </h2>
                </div>
                <div class="lg:col-span-4 lg:col-start-9 lg:pb-3" data-reveal data-reveal-delay="120">
                    <p class="text-base lg:text-lg text-on-surface-variant leading-relaxed">
                        {{ __('Currently active across four major Indian cities, with new neighbourhoods opening every quarter.') }}
                    </p>
                </div>
            </div>

            {{-- City pills with editorial flourish --}}
            <div class="flex flex-wrap gap-3 lg:gap-4">
                @foreach(['Mumbai', 'Pune', 'New Delhi', 'Bangalore'] as $i => $city)
                    <div class="group bento-card" data-reveal="scale" data-reveal-delay="{{ $i * 100 }}">
                        <div class="bezel-shell !rounded-full !p-1.5">
                            <div class="bezel-core !rounded-full !py-3 !px-6 flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-lg" style="font-variation-settings: 'FILL' 1;">location_on</span>
                                <span class="font-headline font-semibold text-on-surface">{{ $city }}</span>
                                <span class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">{{ __('Live') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Coming-soon ghost pill --}}
                <div class="group" data-reveal="scale" data-reveal-delay="500">
                    <div class="rounded-full px-6 py-4 flex items-center gap-3 border border-dashed border-outline-variant/40 spring-transition hover:border-primary/40">
                        <span class="material-symbols-outlined text-on-surface-variant text-lg">add_location</span>
                        <span class="font-headline font-medium text-on-surface-variant">{{ __('Your city, soon') }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer-CTA inline strip --}}
            <div class="mt-16 pt-8" data-reveal>
                <div class="bezel-shell-lg">
                    <div class="bezel-core-lg flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <p class="font-headline text-2xl lg:text-3xl text-primary font-bold leading-tight">{{ __('Ready to invite the wisdom of a Daimaa home?') }}</p>
                            <p class="text-sm text-on-surface-variant mt-2">{{ __('Book in under 90 seconds. We handle the rest.') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('services') }}" class="btn-primary group text-base pl-6 pr-2 py-2">
                                <span class="mr-3">{{ __('Book a Visit') }}</span>
                                <span class="btn-island-icon">
                                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                                </span>
                            </a>
                            <a href="{{ route('contact') }}" class="btn-outline">{{ __('Talk to Us') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    @vite('resources/js/hero-scene-boot.js')
    @endpush
</x-app-layout>
