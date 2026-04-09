<x-app-layout>
    <x-slot:title>{{ __('FAQs') }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-24 bg-surface">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-5xl font-headline font-bold text-primary mb-4">{{ __('Frequently Asked Questions') }}</h1>
                <p class="text-lg text-on-surface-variant">{{ __('Everything you need to know about Daimaa care services') }}</p>
            </div>

            @php $categories = $faqs->groupBy('category'); @endphp

            <div class="space-y-12" x-data="{ open: null }">
                @foreach($categories as $category => $items)
                <div>
                    <h2 class="text-xl font-headline font-bold text-primary mb-4">{{ $category ?: __('General') }}</h2>
                    <div class="space-y-3">
                        @foreach($items as $faq)
                        @php $uid = 'faq-' . $faq->id; @endphp
                        <div class="bg-surface-container-lowest rounded-2xl overflow-hidden">
                            <button @click="open = open === '{{ $uid }}' ? null : '{{ $uid }}'" class="w-full px-6 py-5 flex items-center justify-between text-left">
                                <span class="font-semibold text-on-surface pr-4">{{ $faq->question }}</span>
                                <span class="material-symbols-outlined text-primary shrink-0 transition-transform duration-300" :class="open === '{{ $uid }}' ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="open === '{{ $uid }}'" x-collapse x-cloak>
                                <div class="px-6 pb-5 text-sm text-on-surface-variant leading-relaxed">{{ $faq->answer }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>
