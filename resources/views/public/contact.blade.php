<x-app-layout>
    <x-slot:title>{{ __('Contact') }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-24 bg-surface">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16">
                <div class="space-y-8">
                    <h1 class="text-5xl font-headline font-bold text-primary">{{ __('Get in Touch') }}</h1>
                    <p class="text-lg text-on-surface-variant">{{ __("Have questions about our services? Need help with a booking? We're here for you.") }}</p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-fixed/40 rounded-xl flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary">mail</span>
                            </div>
                            <div>
                                <p class="font-semibold text-on-surface">{{ __('Email') }}</p>
                                <p class="text-on-surface-variant">care@daimaaa.com</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-fixed/40 rounded-xl flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary">phone</span>
                            </div>
                            <div>
                                <p class="font-semibold text-on-surface">{{ __('Phone / WhatsApp') }}</p>
                                <p class="text-on-surface-variant">+91 98XXX XXXXX</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-fixed/40 rounded-xl flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary">location_on</span>
                            </div>
                            <div>
                                <p class="font-semibold text-on-surface">{{ __('Office') }}</p>
                                <p class="text-on-surface-variant">Mumbai, Maharashtra, India</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-3xl p-8 ambient-shadow">
                    <h2 class="text-2xl font-headline font-bold text-primary mb-6">{{ __('Send a Message') }}</h2>
                    <form class="space-y-5">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-on-surface mb-1 block">{{ __('Full Name') }}</label>
                            <input type="text" class="input-field" placeholder="{{ __('Your name') }}">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-on-surface mb-1 block">{{ __('Email') }}</label>
                            <input type="email" class="input-field" placeholder="you@example.com">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-on-surface mb-1 block">{{ __('Phone') }}</label>
                            <input type="tel" class="input-field" placeholder="+91 XXXXX XXXXX">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-on-surface mb-1 block">{{ __('Category') }}</label>
                            <select class="input-field">
                                <option>{{ __('General Inquiry') }}</option>
                                <option>{{ __('Booking Help') }}</option>
                                <option>{{ __('Become a Daimaa') }}</option>
                                <option>{{ __('Partnership') }}</option>
                                <option>{{ __('Feedback') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-on-surface mb-1 block">{{ __('Message') }}</label>
                            <textarea class="input-field" rows="4" placeholder="{{ __('Tell us how we can help...') }}"></textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full">{{ __('Send Message') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
