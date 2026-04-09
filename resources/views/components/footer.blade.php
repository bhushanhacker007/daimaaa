<footer class="bg-surface-container-highest">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            {{-- Brand --}}
            <div class="lg:col-span-1">
                <a href="/" class="flex items-center gap-2 mb-6">
                    <img src="/images/logo.png" alt="{{ __('Daimaa') }}" class="h-14 w-auto object-contain">
                    <span class="text-xl font-headline text-primary">{{ __('Daimaa') }}</span>
                </a>
                <p class="text-sm text-on-surface-variant leading-relaxed">{{ __('Traditional Indian maternal and newborn care, delivered to your home by experienced Daimaa caregivers.') }}</p>
            </div>

            {{-- For Mothers --}}
            <div>
                <h5 class="font-bold text-primary text-xs uppercase tracking-[0.2em] mb-5">{{ __('For Mothers') }}</h5>
                <ul class="space-y-3 text-sm text-on-surface-variant">
                    <li><a href="{{ route('services') }}" class="hover:text-primary transition-colors">{{ __('Our Services') }}</a></li>
                    <li><a href="{{ route('services') }}#packages" class="hover:text-primary transition-colors">{{ __('Packages') }}</a></li>
                    <li><a href="{{ route('how-it-works') }}" class="hover:text-primary transition-colors">{{ __('How It Works') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-primary transition-colors">{{ __('FAQs') }}</a></li>
                </ul>
            </div>

            {{-- For Daimaas --}}
            <div>
                <h5 class="font-bold text-primary text-xs uppercase tracking-[0.2em] mb-5">{{ __('For Daimaas') }}</h5>
                <ul class="space-y-3 text-sm text-on-surface-variant">
                    <li><a href="{{ route('register') }}" class="hover:text-primary transition-colors">{{ __('Join the Collective') }}</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-primary transition-colors">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-primary transition-colors">{{ __('Daimaa Login') }}</a></li>
                </ul>
            </div>

            {{-- Connect --}}
            <div>
                <h5 class="font-bold text-primary text-xs uppercase tracking-[0.2em] mb-5">{{ __('Connect') }}</h5>
                <ul class="space-y-3 text-sm text-on-surface-variant">
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">mail</span>
                        care@daimaaa.com
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">phone</span>
                        +91 98XXX XXXXX
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">location_on</span>
                        Mumbai, Maharashtra, India
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-on-surface-variant/60" style="border-top: 1px solid rgba(218, 193, 186, 0.2);">
            <p>&copy; {{ date('Y') }} {{ __('Daimaa') }}. {{ __('Traditional Wisdom. Professional Nurture.') }}</p>
            <div class="flex gap-6">
                <a href="{{ route('privacy') }}" class="hover:text-primary transition-colors">{{ __('Privacy Policy') }}</a>
                <a href="{{ route('terms') }}" class="hover:text-primary transition-colors">{{ __('Terms') }}</a>
                <a href="{{ route('refund-policy') }}" class="hover:text-primary transition-colors">{{ __('Refund Policy') }}</a>
            </div>
        </div>
    </div>
</footer>
