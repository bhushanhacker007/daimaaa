<x-app-layout>
    <x-slot:title>{{ __('Privacy Policy') }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-24 bg-surface">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <h1 class="text-4xl font-headline font-bold text-primary mb-8">{{ __('Privacy Policy') }}</h1>
            <div class="prose prose-lg max-w-none text-on-surface-variant space-y-6">
                <p>Last updated: {{ date('F Y') }}</p>
                <p>At Daimaa ("we", "our", "us"), we are committed to protecting the privacy of our users. This Privacy Policy explains how we collect, use, and safeguard your information when you use our platform.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Information We Collect</h2>
                <p>We collect personal information that you voluntarily provide, including: name, email address, phone number, home address (for service delivery), date of birth, due date (for mothers), and payment information. For Daimaa caregivers, we additionally collect KYC documents, professional experience details, and availability information.</p>
                <h2 class="text-xl font-headline font-bold text-primary">How We Use Your Information</h2>
                <p>Your information is used to: facilitate service bookings and delivery, verify caregiver identities and qualifications, process payments and refunds, communicate booking confirmations and updates, improve our services, and comply with legal obligations.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Data Protection</h2>
                <p>We implement appropriate security measures to protect your personal information. Sensitive data such as KYC documents and payment details are encrypted. We do not sell or share your personal information with third parties except as necessary to provide our services.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Your Rights</h2>
                <p>You have the right to access, correct, or delete your personal data. You can update your profile information at any time through your dashboard, or contact us at care@daimaaa.com for assistance.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Contact</h2>
                <p>For any privacy-related concerns, please contact us at care@daimaaa.com.</p>
            </div>
        </div>
    </section>
</x-app-layout>
