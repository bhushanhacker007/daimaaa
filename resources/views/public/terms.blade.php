<x-app-layout>
    <x-slot:title>{{ __('Terms') }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-24 bg-surface">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <h1 class="text-4xl font-headline font-bold text-primary mb-8">{{ __('Terms') }}</h1>
            <div class="prose prose-lg max-w-none text-on-surface-variant space-y-6">
                <p>Last updated: {{ date('F Y') }}</p>
                <p>By using Daimaa's platform and services, you agree to these Terms of Service. Please read them carefully.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Service Overview</h2>
                <p>Daimaa provides a platform connecting families with verified traditional maternal and newborn caregivers ("Daimaas"). We facilitate booking, scheduling, and payment for home-based care services.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Bookings & Cancellations</h2>
                <p>Bookings are subject to Daimaa availability in your service area. Cancellations made at least 24 hours before the scheduled session are eligible for a full refund. Late cancellations (less than 24 hours) may incur a cancellation fee. Rescheduling is permitted up to 12 hours before the session.</p>
                <h2 class="text-xl font-headline font-bold text-primary">User Responsibilities</h2>
                <p>Users must provide accurate personal information, maintain a safe and clean environment for the Daimaa's visit, and treat caregivers with respect and dignity. Any form of harassment or misconduct will result in immediate account suspension.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Caregiver Standards</h2>
                <p>All Daimaas undergo verification including KYC documentation, background checks, and skill assessment. However, Daimaa (the platform) acts as a facilitator and is not directly liable for the performance of individual caregivers.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Contact</h2>
                <p>For questions about these terms, please contact us at care@daimaaa.com.</p>
            </div>
        </div>
    </section>
</x-app-layout>
