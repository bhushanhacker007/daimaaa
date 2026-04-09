<x-app-layout>
    <x-slot:title>{{ __('Daimaa') }} — {{ __('Traditional Indian post-pregnancy care for mothers and newborns, delivered to your home by experienced, verified Daimaa caregivers.') }}</x-slot:title>

    {{-- Hero Section with WebGL --}}
    <section class="relative min-h-screen flex items-center pt-16 overflow-hidden bg-surface">
        {{-- Parallax decorative blobs --}}
        <div data-parallax="0.15" class="parallax-blob w-[300px] h-[300px] md:w-[500px] md:h-[500px] bg-primary-fixed/30 -top-20 -right-40"></div>
        <div data-parallax="0.25" class="parallax-blob w-[200px] h-[200px] md:w-[350px] md:h-[350px] bg-tertiary-fixed/20 bottom-20 -left-20"></div>
        <div data-parallax="0.1" class="parallax-blob w-[150px] h-[150px] md:w-[200px] md:h-[200px] bg-secondary-fixed/15 top-1/3 right-1/4"></div>

        <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-12 lg:py-20 z-10">
            {{-- Mobile: logo at top, then text. Desktop: side-by-side --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">

                {{-- Mobile logo (shows above text on small screens) --}}
                <div class="flex flex-col items-center gap-4 lg:hidden" data-reveal="scale" data-reveal-delay="0">
                    <img src="/images/logo.png" alt="{{ __('Daimaa') }}" class="w-48 h-48 sm:w-60 sm:h-60 object-contain drop-shadow-xl animate-float-slow">
                    {{-- Badges row below logo --}}
                    <div class="flex items-center gap-3">
                        <div class="bg-surface-container-lowest rounded-full px-3 py-1.5 ambient-shadow flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-tertiary text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="text-xs font-bold text-on-surface">{{ __('4.9 Rating') }}</span>
                        </div>
                        <div class="bg-surface-container-lowest rounded-full px-3 py-1.5 ambient-shadow flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-primary text-sm">verified_user</span>
                            <span class="text-xs font-bold text-on-surface">{{ __('500+ Verified') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Text content --}}
                <div class="space-y-6 lg:space-y-8 text-center lg:text-left" data-reveal data-reveal-delay="0">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-fixed/40 rounded-full">
                        <span class="material-symbols-outlined text-primary text-sm">verified</span>
                        <span class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('Trusted by 12,000+ Families') }}</span>
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-7xl font-headline font-bold leading-[1.08] tracking-tight">
                        <span class="text-primary">{{ __('The Sacred Choice') }}</span><br>
                        <span class="text-gradient-primary">{{ __('of Nurture') }}</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-on-surface-variant leading-relaxed max-w-lg mx-auto lg:mx-0">
                        {{ __('Traditional Indian post-pregnancy care for mothers and newborns, delivered to your home by experienced, verified Daimaa caregivers.') }}
                    </p>
                    <div class="flex flex-wrap gap-3 sm:gap-4 justify-center lg:justify-start">
                        <a href="{{ route('services') }}" class="btn-primary text-base sm:text-lg px-6 sm:px-8 py-3 sm:py-4 group">
                            {{ __('Explore Services') }}
                            <span class="material-symbols-outlined ml-2 transition-transform group-hover:translate-x-1">arrow_forward</span>
                        </a>
                        <a href="{{ route('how-it-works') }}" class="btn-outline text-base sm:text-lg px-6 sm:px-8 py-3 sm:py-4">{{ __('How It Works') }}</a>
                    </div>
                    {{-- Pincode checker --}}
                    <div class="pt-2 lg:pt-4" x-data="{ pincode: '', result: null, checking: false }">
                        <p class="text-sm text-on-surface-variant mb-2">{{ __('Check if we serve your area:') }}</p>
                        <div class="flex gap-2 max-w-sm mx-auto lg:mx-0">
                            <input type="text" x-model="pincode" placeholder="{{ __('Enter your pincode') }}" maxlength="6" inputmode="numeric" class="input-field flex-1">
                            <button @click="checking = true; fetch('/api/check-pincode/' + pincode).then(r => r.json()).then(d => { result = d; checking = false; }).catch(() => { result = { available: pincode.length === 6 }; checking = false; })" class="btn-secondary px-6 whitespace-nowrap" :disabled="checking">
                                <span x-show="!checking">{{ __('Check') }}</span>
                                <span x-show="checking" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                            </button>
                        </div>
                        <p x-show="result" x-cloak class="mt-2 text-sm" :class="result?.available ? 'text-primary' : 'text-error'">
                            <span x-show="result?.available">{{ __('We serve your area! Book now.') }}</span>
                            <span x-show="!result?.available">{{ __("We're expanding soon to your area. Join our waitlist!") }}</span>
                        </p>
                    </div>
                </div>

                {{-- Desktop logo with WebGL (hidden on mobile since we show it above) --}}
                <div class="relative hidden lg:flex items-center justify-center min-h-[500px]">
                    <div id="hero-webgl" class="hero-webgl-canvas rounded-[2rem]"></div>
                    <div class="relative z-10 pointer-events-none">
                        <img src="/images/logo.png" alt="{{ __('Daimaa') }}" class="w-[420px] h-[420px] object-contain drop-shadow-2xl animate-float-slow">
                    </div>
                    <div class="absolute -bottom-4 left-8 bg-surface-container-lowest rounded-2xl p-4 ambient-shadow flex items-center gap-3 z-20" data-reveal="scale" data-reveal-delay="400">
                        <div class="h-12 w-12 bg-primary-fixed rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">shield</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-surface">{{ __('500+ Verified Daimaas') }}</p>
                            <p class="text-xs text-on-surface-variant">{{ __('Background checked & certified') }}</p>
                        </div>
                    </div>
                    <div class="absolute -top-2 right-12 bg-surface-container-lowest rounded-2xl p-3 ambient-shadow flex items-center gap-2 z-20" data-reveal="scale" data-reveal-delay="600">
                        <span class="material-symbols-outlined text-tertiary text-lg" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="text-sm font-bold text-on-surface">{{ __('4.9 Rating') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust Bar with animated counters --}}
    <section class="bg-surface-container-low py-14 relative overflow-hidden">
        <div data-parallax="0.08" class="parallax-blob w-[300px] h-[300px] bg-primary-fixed/10 -top-10 left-1/4"></div>
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div data-reveal data-reveal-delay="0">
                    <p class="text-4xl lg:text-5xl font-headline font-bold text-primary" data-count-to="500" data-count-suffix="+">0</p>
                    <p class="text-sm text-on-surface-variant mt-2">{{ __('Verified Daimaas') }}</p>
                </div>
                <div data-reveal data-reveal-delay="100">
                    <p class="text-4xl lg:text-5xl font-headline font-bold text-tertiary" data-count-to="12000" data-count-suffix="+">0</p>
                    <p class="text-sm text-on-surface-variant mt-2">{{ __('Families Served') }}</p>
                </div>
                <div data-reveal data-reveal-delay="200">
                    <p class="text-4xl lg:text-5xl font-headline font-bold text-secondary" data-count-to="4" data-count-suffix="">0</p>
                    <p class="text-sm text-on-surface-variant mt-2">{{ __('Cities Active') }}</p>
                </div>
                <div data-reveal data-reveal-delay="300">
                    <p class="text-4xl lg:text-5xl font-headline font-bold text-primary" data-count-to="98" data-count-suffix="%">0</p>
                    <p class="text-sm text-on-surface-variant mt-2">{{ __('Satisfaction Rate') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Services Overview --}}
    <section class="py-24 bg-surface relative overflow-hidden">
        <div data-parallax="0.12" class="parallax-blob w-[400px] h-[400px] bg-tertiary-fixed/10 -bottom-20 -right-20"></div>
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16 max-w-2xl mx-auto" data-reveal>
                <span class="material-symbols-outlined text-tertiary text-3xl mb-4">flare</span>
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary mb-4">{{ __('Our Services') }}</h2>
                <p class="text-lg text-on-surface-variant">{{ __('Traditional care, modern comfort. Each service is rooted in generations of Indian maternal wisdom.') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $i => $service)
                <a href="{{ route('services.detail', $service->slug) }}" class="group bg-surface-container-lowest rounded-3xl p-8 ghost-border hover:shadow-ambient-lg hover:-translate-y-1 transition-all duration-500" data-reveal data-reveal-delay="{{ $i * 100 }}">
                    <div class="h-14 w-14 bg-primary-fixed/40 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors duration-300">
                        <span class="material-symbols-outlined text-primary text-2xl group-hover:text-on-primary">{{ $service->icon ?? 'spa' }}</span>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-primary mb-2">{{ $service->name }}</h3>
                    <p class="text-on-surface-variant text-sm mb-4 leading-relaxed">{{ $service->short_description }}</p>
                    <div class="flex items-center justify-between pt-4" style="border-top: 1px solid rgba(218, 193, 186, 0.15);">
                        <span class="text-lg font-bold text-primary">₹{{ number_format($service->base_price) }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-on-surface-variant">{{ $service->duration_minutes }} {{ __('min') }}</span>
                            <span class="material-symbols-outlined text-primary text-sm opacity-0 group-hover:opacity-100 transition-opacity group-hover:translate-x-0.5 duration-300">arrow_forward</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-12" data-reveal>
                <a href="{{ route('services') }}" class="btn-outline group">
                    {{ __('View All Services & Packages') }}
                    <span class="material-symbols-outlined ml-2 text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-24 bg-surface-container-low relative overflow-hidden">
        <div data-parallax="0.15" class="parallax-blob w-[350px] h-[350px] bg-primary-fixed/10 top-10 -left-20"></div>
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16" data-reveal>
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary mb-4">{{ __('How It Works') }}</h2>
                <p class="text-lg text-on-surface-variant">{{ __('Book your Daimaa in 4 simple steps') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                @foreach([
                    ['icon' => 'search', 'title' => __('Choose Service'), 'desc' => __('Browse our services and select a package that fits your needs.')],
                    ['icon' => 'calendar_month', 'title' => __('Schedule Visit'), 'desc' => __('Pick a date and time that works for you. We come to your home.')],
                    ['icon' => 'verified_user', 'title' => __('Meet Your Daimaa'), 'desc' => __('A verified, experienced Daimaa is assigned to your booking.')],
                    ['icon' => 'spa', 'title' => __('Enjoy Care'), 'desc' => __('Relax as traditional care is delivered with warmth and expertise.')],
                ] as $i => $step)
                <div class="text-center" data-reveal data-reveal-delay="{{ $i * 150 }}">
                    <div class="relative inline-block mb-6">
                        <div class="h-24 w-24 bg-surface-container-lowest rounded-full flex items-center justify-center ambient-shadow mx-auto transition-transform hover:scale-105 duration-300">
                            <span class="material-symbols-outlined text-primary text-3xl">{{ $step['icon'] }}</span>
                        </div>
                        <span class="absolute -top-2 -right-2 h-9 w-9 cta-gradient text-on-primary rounded-full flex items-center justify-center text-sm font-bold shadow-lg">{{ $i + 1 }}</span>
                    </div>
                    <h3 class="text-lg font-headline font-bold text-primary mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured Packages with parallax --}}
    <section class="py-24 bg-surface relative overflow-hidden">
        <div data-parallax="0.2" class="parallax-blob w-[500px] h-[500px] bg-primary-fixed/10 -bottom-40 right-0"></div>
        <div data-parallax="0.1" class="parallax-blob w-[250px] h-[250px] bg-tertiary-fixed/15 top-20 -left-10"></div>
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16" data-reveal>
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary mb-4">{{ __('Care Packages') }}</h2>
                <p class="text-lg text-on-surface-variant">{{ __('Bundled sessions for complete peace of mind') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($packages as $package)
                <div class="bg-surface-container-lowest rounded-3xl p-8 flex flex-col ghost-border hover:shadow-ambient-lg transition-all duration-500 {{ $loop->index === 1 ? 'ring-2 ring-primary relative md:-translate-y-4' : '' }}" data-reveal data-reveal-delay="{{ $loop->index * 150 }}">
                    @if($loop->index === 1)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-5 py-1.5 cta-gradient text-on-primary text-xs font-bold rounded-full shadow-lg">{{ __('Most Popular') }}</div>
                    @endif
                    <h3 class="text-2xl font-headline font-bold text-primary mb-2">{{ $package->name }}</h3>
                    <p class="text-sm text-on-surface-variant mb-6 flex-1 leading-relaxed">{{ $package->description }}</p>
                    <div class="mb-6">
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
                            <span class="material-symbols-outlined text-primary text-lg" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            {{ $svc->name }} &times; {{ $svc->pivot->session_count }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="{{ $loop->index === 1 ? 'btn-primary' : 'btn-outline' }} w-full text-center group">
                        {{ __('Book This Package') }}
                        <span class="material-symbols-outlined ml-1 text-lg transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials with parallax --}}
    <section class="py-24 bg-surface-container-low relative overflow-hidden">
        <div data-parallax="0.12" class="parallax-blob w-[400px] h-[400px] bg-secondary-fixed/10 top-0 right-1/4"></div>
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16" data-reveal>
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary mb-4">{{ __('Voices of Trust') }}</h2>
                <p class="text-lg text-on-surface-variant">{{ __('Hear from families who experienced the Daimaa difference') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($testimonials as $i => $testimonial)
                <div class="bg-surface-container-lowest rounded-3xl p-6 ghost-border hover:shadow-ambient transition-all duration-300" data-reveal data-reveal-delay="{{ $i * 100 }}">
                    <div class="flex gap-1 mb-4">
                        @for($s = 0; $s < $testimonial->rating; $s++)
                        <span class="material-symbols-outlined text-tertiary text-lg" style="font-variation-settings: 'FILL' 1">star</span>
                        @endfor
                    </div>
                    <p class="text-sm text-on-surface-variant leading-relaxed mb-6 italic">"{{ $testimonial->content }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-primary-fixed rounded-full flex items-center justify-center text-sm font-bold text-primary">
                            {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">{{ $testimonial->name }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $testimonial->city }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="py-24 bg-surface">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16" data-reveal>
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary mb-4">{{ __('Common Questions') }}</h2>
                <p class="text-lg text-on-surface-variant">{{ __('Everything you need to know about Daimaa care') }}</p>
            </div>
            <div class="space-y-3" x-data="{ open: null }">
                @foreach($faqs as $index => $faq)
                <div class="bg-surface-container-lowest rounded-2xl overflow-hidden ghost-border" data-reveal data-reveal-delay="{{ $index * 80 }}">
                    <button @click="open = open === {{ $index }} ? null : {{ $index }}" class="w-full px-6 py-5 flex items-center justify-between text-left group">
                        <span class="font-semibold text-on-surface pr-4 group-hover:text-primary transition-colors">{{ $faq->question }}</span>
                        <span class="material-symbols-outlined text-primary shrink-0 transition-transform duration-300" :class="open === {{ $index }} ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open === {{ $index }}" x-collapse x-cloak>
                        <div class="px-6 pb-5 text-sm text-on-surface-variant leading-relaxed">
                            {{ $faq->answer }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8" data-reveal>
                <a href="{{ route('faq') }}" class="text-sm font-semibold text-primary hover:text-primary-container transition-colors">{{ __('View all FAQs') }} &rarr;</a>
            </div>
        </div>
    </section>

    {{-- Become a Daimaa CTA --}}
    <section class="py-24 bg-surface-container-high relative overflow-hidden">
        <div data-parallax="0.18" class="parallax-blob w-[400px] h-[400px] bg-primary-fixed/15 -top-20 -right-20"></div>
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div data-reveal="left">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBU_wPjBhJnU-BWxL-55ElqksATgRvmk2zGEsvX8VQFGsfw8JX6_qxI7zgSZjyikNf3YWFfSWuMz1mAOpz85smLt8gEj7x55-EDCZ1b3UOn8y31e2r4H-dDtbnCvb3D1k85qvwlLocsy2zZIyxU1Ye9P1fGsTjxLnGnFmCV4IEGu7J2R7mu09kEPZvh8Evu687RLe03c3AlASf-VX2KfJG7_aHqaww6HmoBlJEfDObZqhGnQiUK4WDp2flIhsX4YBLBkH6P3Npq3e6k" alt="{{ __('Apply to Join') }}" class="w-full aspect-[4/3] object-cover rounded-3xl rounded-br-[6rem]">
                </div>
                <div class="space-y-8" data-reveal="right">
                    <span class="inline-block px-4 py-1 bg-tertiary-fixed/30 text-tertiary text-xs font-bold uppercase tracking-wider rounded-full">{{ __('For Caregivers') }}</span>
                    <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary leading-tight">{{ __('A Legacy of Care,') }}<br>{{ __('A Future of Impact.') }}</h2>
                    <p class="text-lg text-on-surface-variant leading-relaxed">
                        {{ __("Join India's most respected collective of traditional maternal caregivers. We value your wisdom, support your growth, and connect you with families who need you.") }}
                    </p>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-2xl font-headline font-bold text-primary">{{ __('Verified') }}</p>
                            <p class="text-xs text-on-surface-variant mt-1">{{ __('Profile & KYC') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-headline font-bold text-tertiary">{{ __('Fair Pay') }}</p>
                            <p class="text-xs text-on-surface-variant mt-1">{{ __('Competitive rates') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-headline font-bold text-secondary">{{ __('Growth') }}</p>
                            <p class="text-xs text-on-surface-variant mt-1">{{ __('Training & support') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('register') }}" class="btn-primary text-lg px-8 py-4 inline-flex group">
                        {{ __('Apply to Join') }}
                        <span class="material-symbols-outlined ml-2 transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- City Availability --}}
    <section class="py-24 bg-surface relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center relative">
            <div data-reveal>
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-primary mb-4">{{ __('We Serve In') }}</h2>
                <p class="text-lg text-on-surface-variant mb-12">{{ __('Currently active in select cities, expanding soon') }}</p>
            </div>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach(['Mumbai', 'Pune', 'New Delhi', 'Bangalore'] as $i => $city)
                <div class="px-8 py-4 bg-surface-container-lowest rounded-2xl ghost-border hover:shadow-ambient hover:-translate-y-1 transition-all duration-300 flex items-center gap-3" data-reveal="scale" data-reveal-delay="{{ $i * 100 }}">
                    <span class="material-symbols-outlined text-primary">location_on</span>
                    <span class="font-semibold text-on-surface">{{ $city }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @push('scripts')
    @vite('resources/js/hero-scene-boot.js')
    @endpush
</x-app-layout>
