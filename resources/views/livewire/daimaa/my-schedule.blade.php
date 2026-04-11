<div class="space-y-6">

    {{-- Flash message --}}
    @if(session('schedule-message'))
        <div class="flex items-center gap-3 px-5 py-4 rounded-2xl
            {{ $isOnline ? 'bg-primary-fixed/30 border border-primary/15' : 'bg-error-container/40 border border-error/15' }}">
            <span class="material-symbols-outlined text-2xl {{ $isOnline ? 'text-primary' : 'text-error' }}">
                {{ $isOnline ? 'check_circle' : 'info' }}
            </span>
            <p class="text-base font-semibold {{ $isOnline ? 'text-primary' : 'text-error' }}">
                {{ session('schedule-message') }}
            </p>
        </div>
    @endif

    {{-- ═══════════════════════  ONLINE / OFFLINE TOGGLE  ═══════════════════════ --}}
    <div class="rounded-3xl overflow-hidden shadow-md
        {{ $isOnline ? 'bg-gradient-to-br from-primary-fixed/40 to-primary-fixed/10 ghost-border' : 'bg-gradient-to-br from-error-container/40 to-error-container/10 border border-error/15' }}">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-8">

                {{-- Status icon --}}
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full flex items-center justify-center shrink-0
                    {{ $isOnline ? 'bg-primary/15' : 'bg-error/10' }}">
                    <span class="material-symbols-outlined {{ $isOnline ? 'text-primary' : 'text-error' }}"
                          style="font-size:48px; font-variation-settings: 'FILL' 1">
                        {{ $isOnline ? 'wifi' : 'wifi_off' }}
                    </span>
                </div>

                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-2xl sm:text-3xl font-headline font-bold {{ $isOnline ? 'text-primary' : 'text-error' }}">
                        {{ $isOnline ? 'Aap Online Hain' : 'Aap Offline Hain' }}
                    </h2>
                    <p class="text-base sm:text-lg text-on-surface-variant mt-1">
                        {{ $isOnline
                            ? 'Naye booking aapko milenge. Kaam khatam hone par offline karein.'
                            : 'Koi naya booking nahi aayega. Kaam shuru karne ke liye online karein.' }}
                    </p>
                </div>

                {{-- Big toggle button --}}
                <button wire:click="toggleOnline"
                    class="w-full sm:w-auto min-w-[200px] flex items-center justify-center gap-3 px-8 py-5 rounded-2xl text-xl font-bold shadow-lg transition-all active:scale-95
                        {{ $isOnline
                            ? 'bg-error text-on-error hover:bg-error/90'
                            : 'cta-gradient text-on-primary hover:shadow-xl' }}">
                    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1">
                        {{ $isOnline ? 'toggle_off' : 'toggle_on' }}
                    </span>
                    {{ $isOnline ? 'Offline Karein' : 'Online Karein' }}
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════  WEEKLY SCHEDULE  ═══════════════════════ --}}
    <div class="rounded-3xl bg-surface-container-lowest ghost-border shadow-sm overflow-hidden
        {{ !$isOnline ? 'opacity-60 pointer-events-none' : '' }}">

        <div class="px-6 pt-6 pb-4 border-b border-outline-variant/10">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1">calendar_month</span>
                <div>
                    <h2 class="text-xl sm:text-2xl font-headline font-bold text-on-surface">Hafta Ka Schedule</h2>
                    <p class="text-sm text-on-surface-variant mt-0.5">Har din ke liye apna samay set karein</p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-outline-variant/10">
            @foreach($days as $i => $day)
                <div class="px-5 py-5 sm:px-6 sm:py-5" wire:key="day-{{ $i }}">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                        {{-- Day name + toggle --}}
                        <div class="flex items-center gap-4 sm:w-56 shrink-0">
                            {{-- Day toggle --}}
                            <button wire:click="toggleDay({{ $i }})"
                                class="relative inline-flex h-10 w-20 shrink-0 cursor-pointer rounded-full transition-colors duration-300 focus:outline-none focus:ring-4 focus:ring-primary/20
                                    {{ $day['is_available'] ? 'bg-primary' : 'bg-outline-variant/30' }}"
                                role="switch"
                                aria-checked="{{ $day['is_available'] ? 'true' : 'false' }}">
                                <span class="inline-block h-8 w-8 rounded-full bg-white shadow-md transition-transform duration-300 mt-1
                                    {{ $day['is_available'] ? 'translate-x-[44px]' : 'translate-x-1' }}">
                                </span>
                            </button>

                            <span class="text-lg font-bold {{ $day['is_available'] ? 'text-on-surface' : 'text-on-surface-variant/40' }}">
                                {{ $day['name'] }}
                            </span>
                        </div>

                        {{-- Time pickers --}}
                        @if($day['is_available'])
                            <div class="flex items-center gap-2 sm:gap-3 flex-1" x-data>
                                <div class="flex-1 max-w-[140px]">
                                    <label class="block text-xs font-semibold text-on-surface-variant mb-1 uppercase tracking-wider">Shuru</label>
                                    <input type="time"
                                        wire:model.blur="days.{{ $i }}.start_time"
                                        class="w-full px-3 py-3 text-base font-medium bg-surface-container border-2 border-outline-variant/20 rounded-xl text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors" />
                                </div>

                                <span class="text-on-surface-variant text-2xl font-light mt-5">→</span>

                                <div class="flex-1 max-w-[140px]">
                                    <label class="block text-xs font-semibold text-on-surface-variant mb-1 uppercase tracking-wider">Khatam</label>
                                    <input type="time"
                                        wire:model.blur="days.{{ $i }}.end_time"
                                        class="w-full px-3 py-3 text-base font-medium bg-surface-container border-2 border-outline-variant/20 rounded-xl text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors" />
                                </div>

                                {{-- Copy to all days --}}
                                <button wire:click="copyToAll({{ $i }})"
                                    class="mt-5 flex items-center gap-1.5 px-3 py-3 text-sm font-semibold text-primary bg-primary-fixed/20 hover:bg-primary-fixed/40 rounded-xl transition-colors shrink-0"
                                    title="Isko sabhi dino mein lagao">
                                    <span class="material-symbols-outlined text-lg">content_copy</span>
                                    <span class="hidden sm:inline">Sabhi Dino</span>
                                </button>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-on-surface-variant/40 text-base italic">
                                <span class="material-symbols-outlined text-xl">event_busy</span>
                                Chhuti — Koi booking nahi
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════  QUICK ACTIONS  ═══════════════════════ --}}
    <div class="grid grid-cols-2 gap-4 {{ !$isOnline ? 'opacity-60 pointer-events-none' : '' }}">
        <button wire:click="$set('quickAction', 'weekdays')"
            x-data
            @click="
                {{-- enable Mon-Fri, disable Sat-Sun --}}
                @for($d = 0; $d < 7; $d++)
                    @if($d >= 1 && $d <= 5)
                        @if(!$days[$d]['is_available'])
                            $wire.toggleDay({{ $d }});
                        @endif
                    @else
                        @if($days[$d]['is_available'])
                            $wire.toggleDay({{ $d }});
                        @endif
                    @endif
                @endfor
            "
            class="flex flex-col items-center gap-2 px-4 py-5 bg-surface-container-lowest ghost-border rounded-2xl hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1">work</span>
            <span class="text-sm font-bold text-on-surface text-center">Sirf Weekdays</span>
            <span class="text-xs text-on-surface-variant">Mon—Fri ON</span>
        </button>

        <button
            x-data
            @click="
                @for($d = 0; $d < 7; $d++)
                    @if(!$days[$d]['is_available'])
                        $wire.toggleDay({{ $d }});
                    @endif
                @endfor
            "
            class="flex flex-col items-center gap-2 px-4 py-5 bg-surface-container-lowest ghost-border rounded-2xl hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1">event_available</span>
            <span class="text-sm font-bold text-on-surface text-center">Sab Din ON</span>
            <span class="text-xs text-on-surface-variant">Poora hafta</span>
        </button>
    </div>

    {{-- ═══════════════════════  INFO NOTE  ═══════════════════════ --}}
    <div class="flex items-start gap-3 px-5 py-4 bg-tertiary-container/20 border border-tertiary/10 rounded-2xl">
        <span class="material-symbols-outlined text-tertiary text-2xl mt-0.5">info</span>
        <div class="text-sm text-on-surface-variant leading-relaxed">
            <strong class="text-on-surface">Yaad rakhein:</strong>
            Jab aap <strong>Offline</strong> karein, tab koi bhi naya booking aapko assign nahi hoga.
            Aap kabhi bhi wapas <strong>Online</strong> ho sakti hain.
        </div>
    </div>
</div>
