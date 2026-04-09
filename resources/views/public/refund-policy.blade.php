<x-app-layout>
    <x-slot:title>{{ __('Refund Policy') }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-24 bg-surface">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <h1 class="text-4xl font-headline font-bold text-primary mb-8">{{ __('Refund Policy') }}</h1>
            <div class="prose prose-lg max-w-none text-on-surface-variant space-y-6">
                <p>Last updated: {{ date('F Y') }}</p>
                <p>We want you to be completely satisfied with Daimaa's services. If you're not, here's how our refund process works:</p>
                <h2 class="text-xl font-headline font-bold text-primary">Cancellation Refunds</h2>
                <p><strong>24+ hours before session:</strong> Full refund to original payment method.</p>
                <p><strong>12-24 hours before session:</strong> 50% refund.</p>
                <p><strong>Less than 12 hours:</strong> No refund (session is considered confirmed).</p>
                <h2 class="text-xl font-headline font-bold text-primary">Service Quality Issues</h2>
                <p>If you are unsatisfied with the quality of a session, please report it within 24 hours of the session. We will review your complaint and may offer a complimentary re-session or partial refund.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Package Refunds</h2>
                <p>For packages, unused sessions can be refunded at the per-session rate minus any package discount applied. Partially used packages are refunded proportionally.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Processing Time</h2>
                <p>Refunds are processed within 5-7 business days. The amount will be credited to the original payment method.</p>
                <h2 class="text-xl font-headline font-bold text-primary">Contact</h2>
                <p>For refund requests, please email care@daimaaa.com with your booking number.</p>
            </div>
        </div>
    </section>
</x-app-layout>
