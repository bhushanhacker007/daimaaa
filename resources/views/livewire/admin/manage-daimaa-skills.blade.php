<div>
    <div class="mb-6">
        <p class="text-on-surface-variant text-sm">
            Assign which services each Daimaa is qualified to perform. The auto-dispatch system will only match Daimaas to bookings for services they are qualified for.
        </p>
    </div>

    {{-- Desktop grid view --}}
    <div class="bg-surface-container-lowest rounded-2xl ghost-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead>
                    <tr class="bg-surface-container">
                        <th class="text-left px-4 py-3 text-sm font-bold text-on-surface sticky left-0 bg-surface-container z-10 min-w-[180px]">
                            Daimaa
                        </th>
                        @foreach($services as $service)
                            <th class="text-center px-3 py-3 text-xs font-semibold text-on-surface-variant min-w-[100px]">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="material-symbols-outlined text-primary text-lg">{{ $service->icon ?? 'spa' }}</span>
                                    <span>{{ $service->name }}</span>
                                </div>
                            </th>
                        @endforeach
                        <th class="text-center px-3 py-3 text-xs font-semibold text-on-surface-variant">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($daimaas as $daimaa)
                        <tr class="hover:bg-surface-container/30 transition-colors">
                            <td class="px-4 py-3 sticky left-0 bg-surface-container-lowest z-10">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-primary">person</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-on-surface">{{ $daimaa->name }}</p>
                                        <p class="text-xs text-on-surface-variant">
                                            {{ $daimaa->daimaaProfile?->years_of_experience ?? 0 }} yrs exp
                                            &middot;
                                            <span class="{{ $daimaa->daimaaProfile?->status === 'verified' ? 'text-secondary' : 'text-error' }}">
                                                {{ ucfirst($daimaa->daimaaProfile?->status ?? 'unknown') }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </td>

                            @foreach($services as $service)
                                <td class="text-center px-3 py-3">
                                    <button wire:click="toggle({{ $daimaa->id }}, {{ $service->id }})"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto transition-all
                                            {{ ($qualifications[$daimaa->id][$service->id] ?? false)
                                                ? 'bg-secondary text-on-secondary shadow-sm'
                                                : 'bg-surface-container text-on-surface-variant/30 ghost-border hover:bg-surface-container-high' }}">
                                        <span class="material-symbols-outlined text-lg"
                                            @if($qualifications[$daimaa->id][$service->id] ?? false) style="font-variation-settings: 'FILL' 1" @endif>
                                            {{ ($qualifications[$daimaa->id][$service->id] ?? false) ? 'check_circle' : 'circle' }}
                                        </span>
                                    </button>
                                </td>
                            @endforeach

                            <td class="text-center px-3 py-3">
                                <button wire:click="qualifyAll({{ $daimaa->id }})"
                                    wire:confirm="Qualify {{ $daimaa->name }} for ALL services?"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-primary bg-primary-fixed/30 rounded-xl hover:bg-primary-fixed/50 transition-colors">
                                    <span class="material-symbols-outlined text-sm">select_all</span>
                                    All
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($daimaas->isEmpty())
        <div class="text-center py-16">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4">group_off</span>
            <p class="text-on-surface-variant">No Daimaas registered yet.</p>
        </div>
    @endif
</div>
