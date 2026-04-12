<div x-data="{ slideOpen: @entangle('showDetail') }">

    {{-- Toast --}}
    @if(session('toast'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-4 right-4 z-[999] px-5 py-3 bg-primary text-on-primary rounded-xl shadow-lg font-semibold text-sm animate-slide-in-right">
            {{ session('toast') }}
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl p-5 border border-outline/10">
            <div class="text-3xl font-black text-on-surface">{{ $totalCount }}</div>
            <div class="text-sm text-on-surface-variant font-medium mt-1">Total Daimaas</div>
        </div>
        <div class="bg-primary/5 rounded-2xl p-5 border border-primary/20">
            <div class="text-3xl font-black text-primary">{{ $verifiedCount }}</div>
            <div class="text-sm text-on-surface-variant font-medium mt-1">Verified</div>
        </div>
        <div class="bg-tertiary/5 rounded-2xl p-5 border border-tertiary/20">
            <div class="text-3xl font-black text-tertiary">{{ $pendingCount }}</div>
            <div class="text-sm text-on-surface-variant font-medium mt-1">Pending Review</div>
        </div>
        <div class="bg-primary-fixed/20 rounded-2xl p-5 border border-primary/10">
            <div class="text-3xl font-black text-primary flex items-center gap-2">
                {{ $onlineCount }}
                <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-60"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span></span>
            </div>
            <div class="text-sm text-on-surface-variant font-medium mt-1">Online Now</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">search</span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, phone, email, pincode..."
                class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface">
        </div>
        <select wire:model.live="statusFilter" class="px-4 py-3 rounded-xl border border-outline/30 bg-surface text-on-surface font-medium">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="rejected">Rejected</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    {{-- Daimaa Table --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-surface-container/40">
                        <th class="px-4 py-3 text-left text-xs font-bold text-on-surface-variant uppercase tracking-wider">Daimaa</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-on-surface-variant uppercase tracking-wider hidden sm:table-cell">Phone</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-on-surface-variant uppercase tracking-wider hidden md:table-cell">Exp</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-on-surface-variant uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-on-surface-variant uppercase tracking-wider hidden lg:table-cell">KYC</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-on-surface-variant uppercase tracking-wider hidden lg:table-cell">Online</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-on-surface-variant uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline/10">
                    @forelse($daimaas as $d)
                        @php $p = $d->daimaaProfile; @endphp
                        <tr class="hover:bg-surface-container/20 transition-colors cursor-pointer" wire:click="viewDaimaa({{ $d->id }})">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm shrink-0">
                                        {{ strtoupper(substr($d->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-on-surface text-sm">{{ $d->name }}</div>
                                        <div class="text-xs text-on-surface-variant sm:hidden">{{ $d->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-on-surface hidden sm:table-cell">{{ $d->phone }}</td>
                            <td class="px-4 py-3 text-center text-sm text-on-surface hidden md:table-cell">{{ $p?->years_of_experience ?? '—' }} yr</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $sc = match($p?->status) {
                                        'verified' => 'bg-primary/10 text-primary',
                                        'pending' => 'bg-tertiary/10 text-tertiary',
                                        'rejected' => 'bg-error/10 text-error',
                                        'suspended' => 'bg-error/10 text-error',
                                        default => 'bg-surface-container text-on-surface-variant',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $sc }}">{{ ucfirst($p?->status ?? 'none') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center hidden lg:table-cell">
                                @if($p)
                                    <div class="w-full bg-surface-container rounded-full h-2 max-w-[80px] mx-auto">
                                        <div class="bg-primary h-2 rounded-full" style="width: {{ $p->kycProgress() }}%"></div>
                                    </div>
                                    <span class="text-xs text-on-surface-variant">{{ $p->kycProgress() }}%</span>
                                @else
                                    <span class="text-xs text-on-surface-variant">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center hidden lg:table-cell">
                                @if($p?->is_online)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-primary">
                                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> Online
                                    </span>
                                @else
                                    <span class="text-xs text-on-surface-variant">Offline</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button class="p-2 rounded-lg hover:bg-surface-container transition-colors text-on-surface-variant">
                                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">person_off</span>
                                No Daimaas found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-outline/10">{{ $daimaas->links() }}</div>
    </div>

    {{-- ===== DETAIL SLIDE-OVER ===== --}}
    <div x-show="slideOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-full sm:w-[520px] bg-surface z-50 shadow-2xl overflow-y-auto" x-cloak>

        @if($this->selectedDaimaa)
            @php
                $dm = $this->selectedDaimaa;
                $prof = $dm->daimaaProfile;
            @endphp

            {{-- Header --}}
            <div class="sticky top-0 z-10 bg-surface border-b border-outline/10 px-5 py-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-on-surface">Daimaa Profile</h2>
                <button wire:click="closeDetail" class="p-2 rounded-lg hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-5 space-y-5">
                {{-- Identity Card --}}
                <div class="bg-surface-container/40 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-2xl font-black shrink-0">
                        {{ strtoupper(substr($dm->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xl font-bold text-on-surface truncate">{{ $dm->name }}</div>
                        <div class="text-sm text-on-surface-variant">{{ $dm->phone }} &middot; {{ $dm->email }}</div>
                        @if($prof)
                            @php
                                $statusColors = ['verified' => 'bg-primary/10 text-primary', 'pending' => 'bg-tertiary/10 text-tertiary', 'rejected' => 'bg-error/10 text-error', 'suspended' => 'bg-error/10 text-error'];
                            @endphp
                            <span class="inline-block mt-1 px-3 py-1 rounded-lg text-xs font-bold {{ $statusColors[$prof->status] ?? 'bg-surface-container text-on-surface-variant' }}">
                                {{ ucfirst($prof->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                @if($prof)
                    {{-- Personal Info --}}
                    <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline/10">
                        <h3 class="font-bold text-on-surface mb-3 text-sm uppercase tracking-wider">Personal Info</h3>
                        <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm">
                            <div class="text-on-surface-variant">DOB</div><div class="text-on-surface font-medium">{{ $prof->date_of_birth?->format('d M Y') ?? '—' }}</div>
                            <div class="text-on-surface-variant">Gender</div><div class="text-on-surface font-medium capitalize">{{ $prof->gender ?? '—' }}</div>
                            <div class="text-on-surface-variant">Marital</div><div class="text-on-surface font-medium capitalize">{{ $prof->marital_status ?? '—' }}</div>
                            <div class="text-on-surface-variant">Education</div><div class="text-on-surface font-medium capitalize">{{ str_replace('_', ' ', $prof->education ?? '—') }}</div>
                            <div class="text-on-surface-variant">Blood</div><div class="text-on-surface font-medium">{{ $prof->blood_group ?? '—' }}</div>
                            <div class="text-on-surface-variant">Experience</div><div class="text-on-surface font-medium">{{ $prof->years_of_experience }} years</div>
                            @if($prof->languages_spoken)
                                <div class="text-on-surface-variant">Languages</div><div class="text-on-surface font-medium">{{ implode(', ', $prof->languages_spoken) }}</div>
                            @endif
                            @if($prof->emergency_contact_name)
                                <div class="text-on-surface-variant">Emergency</div><div class="text-on-surface font-medium">{{ $prof->emergency_contact_name }} ({{ $prof->emergency_contact_phone }})</div>
                            @endif
                        </div>
                    </div>

                    {{-- KYC Verification Status --}}
                    <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline/10">
                        <h3 class="font-bold text-on-surface mb-3 text-sm uppercase tracking-wider">KYC Verification</h3>
                        <div class="space-y-3">
                            {{-- Aadhaar --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg {{ $prof->aadhaar_verified_at ? 'bg-primary/10 text-primary' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center text-xs font-bold">A</span>
                                    <div>
                                        <div class="text-sm font-semibold text-on-surface">Aadhaar</div>
                                        <div class="text-xs text-on-surface-variant">{{ $prof->maskedAadhaar() }}</div>
                                    </div>
                                </div>
                                @if($prof->aadhaar_verified_at)
                                    <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg">Verified {{ $prof->aadhaar_verified_at->format('d M') }}</span>
                                @else
                                    <span class="px-2.5 py-1 bg-tertiary/10 text-tertiary text-xs font-bold rounded-lg">Pending</span>
                                @endif
                            </div>

                            {{-- PAN --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg {{ $prof->pan_verified_at ? 'bg-primary/10 text-primary' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center text-xs font-bold">P</span>
                                    <div>
                                        <div class="text-sm font-semibold text-on-surface">PAN Card</div>
                                        <div class="text-xs text-on-surface-variant">{{ $prof->maskedPan() }}</div>
                                    </div>
                                </div>
                                @if($prof->pan_verified_at)
                                    <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg">Verified {{ $prof->pan_verified_at->format('d M') }}</span>
                                @else
                                    <span class="px-2.5 py-1 bg-tertiary/10 text-tertiary text-xs font-bold rounded-lg">Pending</span>
                                @endif
                            </div>

                            {{-- Bank --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg {{ $prof->bank_verified_at ? 'bg-primary/10 text-primary' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center text-xs font-bold">B</span>
                                    <div>
                                        <div class="text-sm font-semibold text-on-surface">Bank Account</div>
                                        <div class="text-xs text-on-surface-variant">{{ $prof->maskedBankAccount() }} &middot; {{ $prof->bank_ifsc }}</div>
                                    </div>
                                </div>
                                @if($prof->bank_verified_at)
                                    <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg">Verified</span>
                                @else
                                    <span class="px-2.5 py-1 bg-tertiary/10 text-tertiary text-xs font-bold rounded-lg">Pending</span>
                                @endif
                            </div>
                        </div>

                        {{-- KYC Progress --}}
                        <div class="mt-4">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-on-surface-variant font-medium">Overall KYC Progress</span>
                                <span class="font-bold text-primary">{{ $prof->kycProgress() }}%</span>
                            </div>
                            <div class="w-full bg-surface-container rounded-full h-2.5">
                                <div class="bg-primary h-2.5 rounded-full transition-all" style="width: {{ $prof->kycProgress() }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents Gallery --}}
                    @if($prof->documents->count())
                        <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline/10">
                            <h3 class="font-bold text-on-surface mb-3 text-sm uppercase tracking-wider">Documents</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($prof->documents as $doc)
                                    <div class="bg-surface rounded-xl p-3 border border-outline/10">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-on-surface-variant uppercase">{{ str_replace('_', ' ', $doc->type) }}</span>
                                            @php
                                                $docColor = match($doc->status) {
                                                    'approved' => 'bg-primary/10 text-primary',
                                                    'rejected' => 'bg-error/10 text-error',
                                                    default => 'bg-tertiary/10 text-tertiary',
                                                };
                                            @endphp
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $docColor }}">{{ ucfirst($doc->status) }}</span>
                                        </div>
                                        <div class="text-xs text-on-surface-variant truncate mb-2">{{ $doc->original_name }}</div>
                                        @if($doc->status === 'pending')
                                            <div class="flex gap-1.5">
                                                <button wire:click="approveDocument({{ $doc->id }})" class="flex-1 text-xs py-1.5 rounded-lg bg-primary/10 text-primary font-bold hover:bg-primary/20 transition-colors">Approve</button>
                                                <button wire:click="rejectDocument({{ $doc->id }})" class="flex-1 text-xs py-1.5 rounded-lg bg-error/10 text-error font-bold hover:bg-error/20 transition-colors">Reject</button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Police Verification Timeline --}}
                    <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline/10">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-on-surface text-sm uppercase tracking-wider">Police Verification</h3>
                            <button wire:click="openPoliceModal" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-bold hover:bg-primary/20 transition-colors">
                                + Add Record
                            </button>
                        </div>

                        @if($prof->policeVerifications->count())
                            <div class="space-y-3">
                                @foreach($prof->policeVerifications->sortByDesc('created_at') as $pv)
                                    <div class="bg-surface rounded-xl p-3 border border-outline/10">
                                        <div class="flex items-center justify-between mb-2">
                                            @php
                                                $pvColor = match(true) {
                                                    $pv->isExpired() => 'bg-error/10 text-error',
                                                    $pv->status === 'cleared' => 'bg-primary/10 text-primary',
                                                    $pv->status === 'failed' => 'bg-error/10 text-error',
                                                    default => 'bg-tertiary/10 text-tertiary',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $pvColor }}">{{ $pv->statusLabel() }}</span>
                                            <span class="text-xs text-on-surface-variant">{{ $pv->created_at->format('d M Y') }}</span>
                                        </div>
                                        @if($pv->reference_number)
                                            <div class="text-xs text-on-surface-variant">Ref: {{ $pv->reference_number }}</div>
                                        @endif
                                        @if($pv->agency_name)
                                            <div class="text-xs text-on-surface-variant">Agency: {{ $pv->agency_name }}</div>
                                        @endif
                                        @if($pv->expiry_date)
                                            <div class="text-xs {{ $pv->isExpired() ? 'text-error font-bold' : 'text-on-surface-variant' }}">
                                                Expires: {{ $pv->expiry_date->format('d M Y') }}
                                                @if($pv->isExpired()) (EXPIRED) @endif
                                            </div>
                                        @endif
                                        @if($pv->notes)
                                            <div class="text-xs text-on-surface-variant mt-1 italic">{{ $pv->notes }}</div>
                                        @endif

                                        @if(!in_array($pv->status, ['cleared', 'failed']))
                                            <div class="flex gap-2 mt-2">
                                                @if($pv->status === 'initiated')
                                                    <button wire:click="updatePoliceStatus({{ $pv->id }}, 'in_progress')" class="text-xs px-3 py-1.5 rounded-lg bg-tertiary/10 text-tertiary font-bold">Mark In-Progress</button>
                                                @endif
                                                <button wire:click="updatePoliceStatus({{ $pv->id }}, 'cleared')" class="text-xs px-3 py-1.5 rounded-lg bg-primary/10 text-primary font-bold">Mark Cleared</button>
                                                <button wire:click="updatePoliceStatus({{ $pv->id }}, 'failed')" class="text-xs px-3 py-1.5 rounded-lg bg-error/10 text-error font-bold">Mark Failed</button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-on-surface-variant text-center py-4">No police verification records.</p>
                        @endif
                    </div>

                    {{-- Scores --}}
                    <div class="bg-surface-container-lowest rounded-2xl p-4 border border-outline/10">
                        <h3 class="font-bold text-on-surface mb-3 text-sm uppercase tracking-wider">Performance</h3>
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div>
                                <div class="text-2xl font-black text-on-surface">{{ number_format($prof->reliability_score ?? 0, 1) }}</div>
                                <div class="text-xs text-on-surface-variant">Reliability</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-on-surface">{{ $prof->total_assignments ?? 0 }}</div>
                                <div class="text-xs text-on-surface-variant">Assignments</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-on-surface">{{ $prof->declined_assignments ?? 0 }}</div>
                                <div class="text-xs text-on-surface-variant">Declined</div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-3">
                        @if($prof->status !== 'verified')
                            <button wire:click="verifyDaimaa" class="flex-1 px-4 py-3 rounded-xl bg-primary text-on-primary font-bold text-sm hover:opacity-90 transition-all">
                                Verify Daimaa
                            </button>
                        @endif
                        @if($prof->status !== 'suspended')
                            <button wire:click="openSuspendModal" class="flex-1 px-4 py-3 rounded-xl bg-error/10 text-error font-bold text-sm border border-error/20 hover:bg-error/20 transition-all">
                                Suspend
                            </button>
                        @endif
                        @if($prof->status !== 'rejected')
                            <button wire:click="openRejectModal" class="flex-1 px-4 py-3 rounded-xl bg-error/10 text-error font-bold text-sm border border-error/20 hover:bg-error/20 transition-all">
                                Reject
                            </button>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl opacity-30">person_off</span>
                        <p class="mt-2">No profile data found for this Daimaa.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Overlay for slide-over --}}
    <div x-show="slideOpen" x-transition.opacity class="fixed inset-0 bg-black/30 z-40" @click="$wire.closeDetail()" x-cloak></div>

    {{-- ===== POLICE VERIFICATION MODAL ===== --}}
    @if($showPoliceModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 p-4">
            <div class="bg-surface rounded-2xl w-full max-w-md p-6 shadow-xl" @click.away="$wire.set('showPoliceModal', false)">
                <h3 class="text-lg font-bold text-on-surface mb-4">Add Police Verification</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Status</label>
                        <select wire:model="pvStatus" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface text-on-surface">
                            <option value="initiated">Initiated</option>
                            <option value="in_progress">In Progress</option>
                            <option value="cleared">Cleared</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Reference Number</label>
                        <input type="text" wire:model="pvReferenceNumber" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface text-on-surface" placeholder="PV/2026/12345">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Agency</label>
                        <input type="text" wire:model="pvAgencyName" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface text-on-surface" placeholder="Local Police Station">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Expiry Date</label>
                        <input type="date" wire:model="pvExpiryDate" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface text-on-surface">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Notes</label>
                        <textarea wire:model="pvNotes" rows="2" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface text-on-surface" placeholder="Additional notes..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1">Report PDF</label>
                        <input type="file" wire:model="pvReport" accept=".pdf,image/*" class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('showPoliceModal', false)" class="flex-1 px-4 py-3 rounded-xl border border-outline/30 font-bold text-on-surface-variant">Cancel</button>
                    <button wire:click="savePoliceVerification" class="flex-1 px-4 py-3 rounded-xl bg-primary text-on-primary font-bold hover:opacity-90">Save</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== SUSPEND MODAL ===== --}}
    @if($showSuspendModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 p-4">
            <div class="bg-surface rounded-2xl w-full max-w-sm p-6 shadow-xl">
                <h3 class="text-lg font-bold text-error mb-2">Suspend Daimaa?</h3>
                <p class="text-sm text-on-surface-variant mb-4">This will prevent the Daimaa from receiving new bookings.</p>
                <textarea wire:model="actionReason" rows="2" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface text-on-surface mb-4" placeholder="Reason (optional)..."></textarea>
                <div class="flex gap-3">
                    <button wire:click="$set('showSuspendModal', false)" class="flex-1 px-4 py-3 rounded-xl border border-outline/30 font-bold text-on-surface-variant">Cancel</button>
                    <button wire:click="confirmSuspend" class="flex-1 px-4 py-3 rounded-xl bg-error text-on-error font-bold">Suspend</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== REJECT MODAL ===== --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 p-4">
            <div class="bg-surface rounded-2xl w-full max-w-sm p-6 shadow-xl">
                <h3 class="text-lg font-bold text-error mb-2">Reject Daimaa?</h3>
                <p class="text-sm text-on-surface-variant mb-4">The Daimaa will need to re-apply after rejection.</p>
                <textarea wire:model="actionReason" rows="2" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface text-on-surface mb-4" placeholder="Reason (optional)..."></textarea>
                <div class="flex gap-3">
                    <button wire:click="$set('showRejectModal', false)" class="flex-1 px-4 py-3 rounded-xl border border-outline/30 font-bold text-on-surface-variant">Cancel</button>
                    <button wire:click="confirmReject" class="flex-1 px-4 py-3 rounded-xl bg-error text-on-error font-bold">Reject</button>
                </div>
            </div>
        </div>
    @endif
</div>
