<x-app-layout>
    <x-slot:title>{{ __('About') }} — {{ __('Daimaa') }}</x-slot:title>

    <section class="pt-24 pb-16 bg-surface">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-6">
                    <h1 class="text-5xl font-headline font-bold text-primary leading-tight">{{ __('Where Ancient Wisdom Meets Modern Purpose') }}</h1>
                    <p class="text-lg text-on-surface-variant leading-relaxed">
                        {{ __('Daimaa was born from a simple truth: the ancient Indian tradition of postpartum care is one of the most nurturing gifts a mother can receive — yet it was being lost in the pace of modern life.') }}
                    </p>
                    <p class="text-lg text-on-surface-variant leading-relaxed">
                        {{ __('We built this platform to preserve and professionalize this sacred practice. Every Daimaa in our network carries the knowledge passed down through generations — traditional massage techniques, herbal remedies, newborn bathing rituals — combined with modern hygiene and safety standards.') }}
                    </p>
                </div>
                <div>
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkYEEiC65Dc0lCcta8MVQ0fObaF7Sv8JJ9xVEpZBUoIp0NUQvsdLn-15WJu_dH6_sbS3PgptGyEu8_aW5qGj5YP0ZSLkk1BY5fqn0aOXbi3Fk-n6_n2hwWopoQg_mdbMedjKa_aGyKNiG3HM7bPTNP1xqjPoqxFcyXs75-cPR9eKp6ctbLsk1dqu9sG9pwoIX_K0w7B1SVPNeqqSiPPADWwPyfS3Txj_yr28MZjBcflPAe4tNeR2p2phlTKdVUnuIgnTZ1HqCNHzHo" alt="{{ __('About') }} {{ __('Daimaa') }}" class="w-full aspect-square object-cover rounded-3xl rounded-tl-[6rem]">
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-surface-container-low">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <h2 class="text-3xl font-headline font-bold text-primary text-center mb-12">{{ __('Our Values') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['icon' => 'shield', 'title' => __('Trust & Safety'), 'desc' => __('Every Daimaa is KYC-verified, background-checked, and assessed for expertise before joining our network.')],
                    ['icon' => 'self_improvement', 'title' => __('Traditional Wisdom'), 'desc' => __('We honor the time-tested practices of Indian maternal care — massage, herbal therapies, and nurturing rituals.')],
                    ['icon' => 'diversity_1', 'title' => __('Professional Dignity'), 'desc' => __('We provide fair compensation, structured work, and recognition to every Daimaa in our collective.')],
                ] as $value)
                <div class="bg-surface-container-lowest rounded-3xl p-8 text-center">
                    <div class="h-16 w-16 bg-primary-fixed/40 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-primary text-3xl">{{ $value['icon'] }}</span>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-primary mb-3">{{ $value['title'] }}</h3>
                    <p class="text-sm text-on-surface-variant">{{ $value['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>
