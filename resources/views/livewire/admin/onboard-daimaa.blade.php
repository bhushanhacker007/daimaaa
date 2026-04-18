@php
    $stepLabels = ['Account', 'Personal', 'Professional', 'KYC & Bank', 'Availability', 'Review'];
    $stepIcons = ['person_add', 'badge', 'workspace_premium', 'verified_user', 'event_available', 'task_alt'];
@endphp

<div class="max-w-5xl mx-auto" x-data="{ step: @entangle('step') }">

    {{-- ===== Success State ===== --}}
    @if($success)
        <div class="bg-surface-container-lowest border border-primary/20 rounded-3xl p-8 sm:p-10 shadow-md text-center max-w-2xl mx-auto">
            <div class="w-20 h-20 mx-auto rounded-full bg-primary/10 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-primary" style="font-size: 48px;">check_circle</span>
            </div>
            <h2 class="text-3xl font-headline font-bold text-on-surface mb-2">Daimaa onboarded successfully</h2>
            <p class="text-on-surface-variant mb-6">The new Daimaa account has been created. Share the credentials securely with them.</p>

            <div class="bg-surface-container/40 rounded-2xl p-5 text-left mb-6 space-y-3" x-data="{ copied: false }">
                <div>
                    <div class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Email</div>
                    <div class="font-mono text-on-surface">{{ $createdEmail }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Temporary Password</div>
                    <div class="flex items-center gap-2">
                        <code class="font-mono text-on-surface bg-surface px-3 py-2 rounded-lg border border-outline/30 text-sm flex-1 break-all">{{ $createdPassword }}</code>
                        <button type="button"
                            @click="navigator.clipboard.writeText('{{ $createdPassword }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="px-3 py-2 rounded-lg bg-primary text-on-primary text-sm font-semibold hover:opacity-90 transition-opacity inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base" x-show="!copied">content_copy</span>
                            <span class="material-symbols-outlined text-base" x-show="copied" x-cloak>check</span>
                            <span x-text="copied ? 'Copied' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('admin.daimaas') }}" class="px-6 py-3 rounded-2xl border-2 border-primary text-primary font-bold hover:bg-primary/5 transition-all inline-flex items-center gap-2 justify-center">
                    <span class="material-symbols-outlined">groups</span>
                    View All Daimaas
                </a>
                <button wire:click="startAnother" class="px-6 py-3 rounded-2xl bg-primary text-on-primary font-bold shadow-lg hover:opacity-90 transition-all inline-flex items-center gap-2 justify-center">
                    <span class="material-symbols-outlined">person_add</span>
                    Onboard Another
                </button>
            </div>
        </div>
    @else

    {{-- ===== Stepper ===== --}}
    <div class="mb-8 bg-surface-container-lowest rounded-2xl p-4 sm:p-5 border border-outline/10">
        <div class="flex items-center justify-between gap-1 sm:gap-2 overflow-x-auto">
            @foreach($stepLabels as $i => $label)
                @php $num = $i + 1; $isActive = $step === $num; $isDone = $step > $num; @endphp
                <button type="button" wire:click="goToStep({{ $num }})"
                    class="flex flex-col items-center gap-1.5 flex-1 min-w-[64px] py-1 group transition-all
                        {{ $isDone ? 'cursor-pointer' : ($isActive ? 'cursor-default' : 'cursor-not-allowed opacity-70') }}">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all
                        {{ $isDone ? 'bg-primary text-on-primary' : ($isActive ? 'bg-primary text-on-primary ring-4 ring-primary/30' : 'bg-surface-container text-on-surface-variant') }}">
                        @if($isDone)
                            <span class="material-symbols-outlined text-lg">check</span>
                        @else
                            <span class="material-symbols-outlined text-lg">{{ $stepIcons[$i] }}</span>
                        @endif
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold text-center leading-tight {{ $isActive || $isDone ? 'text-primary' : 'text-on-surface-variant' }}">{{ $label }}</span>
                </button>
                @if($i < count($stepLabels) - 1)
                    <div class="flex-1 max-w-[40px] sm:max-w-none h-1 rounded-full mb-5 {{ $step > $num ? 'bg-primary' : 'bg-surface-container' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ===== STEP 1: Account ===== --}}
    @if($step === 1)
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-md border border-outline/10">
        <div class="flex items-start gap-3 mb-6">
            <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined">person_add</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-bold text-on-surface">Account Details</h2>
                <p class="text-on-surface-variant text-sm">The Daimaa will use these credentials to sign in.</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Full Name <span class="text-error">*</span></label>
                    <input type="text" wire:model="name" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface" placeholder="e.g. Lakshmi Devi">
                    @error('name') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Phone Number <span class="text-error">*</span></label>
                    <input type="tel" wire:model="phone" maxlength="15" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface" placeholder="9876543210">
                    @error('phone') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Email Address <span class="text-error">*</span></label>
                <input type="email" wire:model="email" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface" placeholder="lakshmi@example.com">
                @error('email') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="bg-surface-container/30 rounded-2xl p-4 border border-outline/10">
                <label class="flex items-center gap-3 cursor-pointer mb-3">
                    <input type="checkbox" wire:model.live="autoGeneratePassword" class="w-5 h-5 rounded text-primary focus:ring-primary border-outline/40">
                    <span class="text-sm font-semibold text-on-surface">Auto-generate a secure password</span>
                </label>
                @if(!$autoGeneratePassword)
                    <input type="text" wire:model="password" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface font-mono" placeholder="Minimum 8 characters">
                    @error('password') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                @else
                    <p class="text-xs text-on-surface-variant flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">info</span>
                        We'll generate a 12-character password and show it after submission.
                    </p>
                @endif
            </div>

            <div class="bg-tertiary/5 rounded-2xl p-4 border border-tertiary/20">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="preVerify" class="w-5 h-5 rounded text-tertiary focus:ring-tertiary border-outline/40 mt-0.5">
                    <div>
                        <span class="text-sm font-semibold text-on-surface block">Mark as pre-verified</span>
                        <span class="text-xs text-on-surface-variant">If checked, profile status is set to <strong>verified</strong> immediately and uploaded documents are auto-approved. Only do this when KYC has been confirmed offline.</span>
                    </div>
                </label>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== STEP 2: Personal ===== --}}
    @if($step === 2)
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-md border border-outline/10">
        <div class="flex items-start gap-3 mb-6">
            <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined">badge</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-bold text-on-surface">Personal Information</h2>
                <p class="text-on-surface-variant text-sm">Demographics, languages, emergency contact, and home address.</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Date of Birth <span class="text-error">*</span></label>
                    <input type="date" wire:model="dateOfBirth" max="{{ now()->subYears(18)->format('Y-m-d') }}" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface">
                    @error('dateOfBirth') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Gender <span class="text-error">*</span></label>
                    <select wire:model="gender" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface">
                        <option value="">Select</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Marital Status</label>
                    <select wire:model="maritalStatus" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface">
                        <option value="">Select</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="divorced">Divorced</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Languages Spoken</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($allLanguages as $lang)
                        <label class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border cursor-pointer text-sm transition-all
                            {{ in_array($lang, $languagesSpoken) ? 'bg-primary/10 border-primary text-primary font-semibold' : 'bg-surface border-outline/30 text-on-surface-variant hover:border-outline/60' }}">
                            <input type="checkbox" wire:model.live="languagesSpoken" value="{{ $lang }}" class="sr-only">
                            {{ $lang }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Blood Group</label>
                    <select wire:model="bloodGroup" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface">
                        <option value="">Select</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}">{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1.5">Emergency Contact Name</label>
                        <input type="text" wire:model="emergencyContactName" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface" placeholder="Contact person">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1.5">Emergency Phone</label>
                        <input type="tel" wire:model="emergencyContactPhone" maxlength="15" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface" placeholder="9876543210">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-outline/10">
                <h3 class="text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-3">Home Address</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1.5">Address Line</label>
                        <input type="text" wire:model="addressLine" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface" placeholder="House no, street, locality">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-on-surface mb-1.5">City</label>
                            <select wire:model="cityId" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface">
                                <option value="">Select city</option>
                                @foreach($cities as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}, {{ $c->state }}</option>
                                @endforeach
                            </select>
                            @error('cityId') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface mb-1.5">Pincode</label>
                            <input type="text" wire:model="pincode" maxlength="6" inputmode="numeric" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface" placeholder="400001">
                            @error('pincode') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== STEP 3: Professional ===== --}}
    @if($step === 3)
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-md border border-outline/10">
        <div class="flex items-start gap-3 mb-6">
            <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined">workspace_premium</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-bold text-on-surface">Professional Profile</h2>
                <p class="text-on-surface-variant text-sm">Experience, service coverage, and qualified services.</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Years of Experience <span class="text-error">*</span></label>
                    <input type="number" wire:model="yearsOfExperience" min="0" max="60" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface">
                    @error('yearsOfExperience') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Education Level</label>
                    <select wire:model="education" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface">
                        <option value="">Select</option>
                        <option value="none">No Formal Education</option>
                        <option value="primary">Primary School</option>
                        <option value="secondary">Secondary (10th)</option>
                        <option value="higher_secondary">Higher Secondary (12th)</option>
                        <option value="graduate">Graduate</option>
                        <option value="post_graduate">Post Graduate</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Bio <span class="text-error">*</span></label>
                <textarea wire:model="bio" rows="4" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface" placeholder="Describe their experience caring for mothers and babies, training, specialties..."></textarea>
                <div class="flex items-center justify-between mt-1">
                    @error('bio') <span class="text-error text-xs">{{ $message }}</span> @else <span class="text-xs text-on-surface-variant">Min 20 characters. Visible on their public profile.</span> @enderror
                    <span class="text-xs text-on-surface-variant">{{ strlen($bio) }} / 2000</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Service Area Pincodes <span class="text-error">*</span></label>
                <input type="text" wire:model="serviceAreaPincodes" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface font-mono text-sm" placeholder="400001, 400002, 400003">
                <p class="text-xs text-on-surface-variant mt-1">6-digit pincodes separated by commas. The Daimaa will be matched to bookings within these areas.</p>
                @error('serviceAreaPincodes') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-outline/10">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-on-surface-variant uppercase tracking-wider">Qualified Services <span class="text-error normal-case">*</span></h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">Select every service this Daimaa is trained to perform.</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <button type="button" wire:click="selectAllServices" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary font-semibold hover:bg-primary/20 transition-colors">Select all</button>
                        <button type="button" wire:click="clearServices" class="px-3 py-1.5 rounded-lg bg-surface-container text-on-surface-variant font-semibold hover:bg-surface-container/70 transition-colors">Clear</button>
                    </div>
                </div>

                @php $grouped = $services->groupBy(fn($s) => optional($s->category)->name ?? 'Other'); @endphp
                <div class="space-y-4">
                    @foreach($grouped as $cat => $items)
                        <div>
                            <div class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2 flex items-center gap-2">
                                <span class="w-1 h-3 rounded-full bg-primary/40"></span>
                                {{ $cat }}
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($items as $service)
                                    @php $checked = in_array($service->id, $selectedServices); @endphp
                                    <button type="button" wire:click="toggleService({{ $service->id }})"
                                        class="flex items-start gap-3 p-3 rounded-xl border text-left transition-all
                                            {{ $checked ? 'bg-primary/5 border-primary' : 'bg-surface border-outline/20 hover:border-outline/40' }}">
                                        <div class="w-5 h-5 rounded-md border-2 mt-0.5 flex items-center justify-center shrink-0 transition-all
                                            {{ $checked ? 'bg-primary border-primary' : 'border-outline/40' }}">
                                            @if($checked)
                                                <span class="material-symbols-outlined text-on-primary" style="font-size:14px;">check</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-on-surface text-sm">{{ $service->name }}</div>
                                            @if($service->short_description)
                                                <div class="text-xs text-on-surface-variant line-clamp-1 mt-0.5">{{ $service->short_description }}</div>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('selectedServices') <span class="text-error text-xs mt-2 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    @endif

    {{-- ===== STEP 4: KYC & Bank ===== --}}
    @if($step === 4)
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-md border border-outline/10">
        <div class="flex items-start gap-3 mb-6">
            <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined">verified_user</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-bold text-on-surface">KYC & Bank Details</h2>
                <p class="text-on-surface-variant text-sm">Optional but recommended. The Daimaa can finish these later from their dashboard.</p>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Aadhaar --}}
            <div class="bg-surface rounded-2xl p-5 border border-outline/20">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">A</span>
                        Aadhaar
                    </h3>
                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                        <input type="checkbox" wire:model="aadhaarVerified" class="w-4 h-4 rounded text-primary focus:ring-primary border-outline/40">
                        <span class="font-semibold text-on-surface-variant">Mark as verified</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <input type="text" wire:model="aadhaarNumber" maxlength="12" inputmode="numeric"
                            placeholder="12-digit Aadhaar number"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm font-mono">
                        @error('aadhaarNumber') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="aadhaarName" placeholder="Name as on Aadhaar"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Aadhaar Front (image)</label>
                        <input type="file" wire:model="aadhaarFrontDoc" accept="image/*"
                            class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold file:text-xs">
                        @error('aadhaarFrontDoc') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Aadhaar Back (image)</label>
                        <input type="file" wire:model="aadhaarBackDoc" accept="image/*"
                            class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold file:text-xs">
                        @error('aadhaarBackDoc') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- PAN --}}
            <div class="bg-surface rounded-2xl p-5 border border-outline/20">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-tertiary/10 flex items-center justify-center text-tertiary text-xs font-bold">P</span>
                        PAN
                    </h3>
                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                        <input type="checkbox" wire:model="panVerified" class="w-4 h-4 rounded text-tertiary focus:ring-tertiary border-outline/40">
                        <span class="font-semibold text-on-surface-variant">Mark as verified</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <input type="text" wire:model="panNumber" maxlength="10" placeholder="ABCDE1234F"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm font-mono uppercase">
                        @error('panNumber') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="panName" placeholder="Name as on PAN"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1">PAN Card (image or PDF)</label>
                    <input type="file" wire:model="panCardDoc" accept="image/*,.pdf"
                        class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-tertiary/10 file:text-tertiary file:font-semibold file:text-xs">
                    @error('panCardDoc') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Photo + Certificates --}}
            <div class="bg-surface rounded-2xl p-5 border border-outline/20">
                <h3 class="text-base font-bold text-on-surface mb-3">Photo & Certificates</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Profile Photo</label>
                        <input type="file" wire:model="photoDoc" accept="image/*"
                            class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold file:text-xs">
                        @if($photoDoc)
                            <img src="{{ $photoDoc->temporaryUrl() }}" class="w-20 h-20 rounded-xl mt-2 object-cover border-2 border-primary/30" alt="Preview">
                        @endif
                        @error('photoDoc') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Experience Certificate</label>
                        <input type="file" wire:model="certificateDoc" accept="image/*,.pdf"
                            class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold file:text-xs">
                        @error('certificateDoc') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Police Verification</label>
                        <input type="file" wire:model="policeVerificationDoc" accept="image/*,.pdf"
                            class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold file:text-xs">
                        @error('policeVerificationDoc') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Bank --}}
            <div class="bg-surface rounded-2xl p-5 border border-outline/20">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">account_balance</span>
                        Bank & Payout Details
                    </h3>
                    <label class="inline-flex items-center gap-2 text-xs cursor-pointer">
                        <input type="checkbox" wire:model="bankVerified" class="w-4 h-4 rounded text-primary focus:ring-primary border-outline/40">
                        <span class="font-semibold text-on-surface-variant">Mark as verified</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Account Number</label>
                        <input type="text" wire:model="bankAccountNumber"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm font-mono"
                            placeholder="1234567890123">
                        @error('bankAccountNumber') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Confirm Account Number</label>
                        <input type="text" wire:model="bankAccountNumberConfirm"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm font-mono"
                            placeholder="Re-enter account number">
                        @error('bankAccountNumberConfirm') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">IFSC Code</label>
                        <input type="text" wire:model="bankIfsc" maxlength="11"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm font-mono uppercase"
                            placeholder="SBIN0001234">
                        @error('bankIfsc') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Bank Name</label>
                        <input type="text" wire:model="bankName"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm"
                            placeholder="State Bank of India">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Account Holder Name</label>
                        <input type="text" wire:model="bankAccountHolder"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm"
                            placeholder="Defaults to Daimaa name">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">UPI ID (optional)</label>
                        <input type="text" wire:model="upiId"
                            class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-sm font-mono"
                            placeholder="name@upi">
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== STEP 5: Availability ===== --}}
    @if($step === 5)
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-md border border-outline/10">
        <div class="flex items-start gap-3 mb-6">
            <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined">event_available</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-bold text-on-surface">Weekly Availability</h2>
                <p class="text-on-surface-variant text-sm">Default working hours. The Daimaa can refine these later from their schedule page.</p>
            </div>
        </div>

        <div class="space-y-2">
            @php $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
            @foreach($availability as $i => $slot)
                <div class="flex flex-wrap items-center gap-3 py-3 px-4 rounded-xl transition-colors
                    {{ $slot['enabled'] ? 'bg-primary/5 border border-primary/20' : 'bg-surface border border-outline/10' }}">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="availability.{{ $i }}.enabled" class="sr-only peer">
                        <div class="w-12 h-7 bg-outline/30 peer-focus:ring-2 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                    <span class="w-24 font-bold text-on-surface text-sm">{{ $dayNames[$i] }}</span>
                    @if($slot['enabled'])
                        <input type="time" wire:model="availability.{{ $i }}.start" class="px-3 py-2 rounded-lg border border-outline/30 bg-surface text-on-surface text-sm">
                        <span class="text-on-surface-variant text-sm">to</span>
                        <input type="time" wire:model="availability.{{ $i }}.end" class="px-3 py-2 rounded-lg border border-outline/30 bg-surface text-on-surface text-sm">
                    @else
                        <span class="text-on-surface-variant text-sm italic">Not available</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ===== STEP 6: Review ===== --}}
    @if($step === 6)
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-md border border-outline/10">
        <div class="flex items-start gap-3 mb-6">
            <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined">task_alt</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-bold text-on-surface">Review & Create Account</h2>
                <p class="text-on-surface-variant text-sm">Confirm everything looks right before creating the Daimaa account.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-surface rounded-2xl p-4 border border-outline/10">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-on-surface text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">person_add</span>
                        Account
                    </h3>
                    <button type="button" wire:click="goToStep(1)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                </div>
                <dl class="grid grid-cols-3 gap-y-1.5 text-sm">
                    <dt class="text-on-surface-variant col-span-1">Name</dt><dd class="col-span-2 text-on-surface font-medium truncate">{{ $name ?: '—' }}</dd>
                    <dt class="text-on-surface-variant col-span-1">Email</dt><dd class="col-span-2 text-on-surface font-medium truncate">{{ $email ?: '—' }}</dd>
                    <dt class="text-on-surface-variant col-span-1">Phone</dt><dd class="col-span-2 text-on-surface font-medium">{{ $phone ?: '—' }}</dd>
                    <dt class="text-on-surface-variant col-span-1">Status</dt>
                    <dd class="col-span-2">
                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-bold {{ $preVerify ? 'bg-primary/10 text-primary' : 'bg-tertiary/10 text-tertiary' }}">
                            {{ $preVerify ? 'Pre-verified' : 'Pending review' }}
                        </span>
                    </dd>
                </dl>
            </div>

            <div class="bg-surface rounded-2xl p-4 border border-outline/10">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-on-surface text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">badge</span>
                        Personal
                    </h3>
                    <button type="button" wire:click="goToStep(2)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                </div>
                <dl class="grid grid-cols-3 gap-y-1.5 text-sm">
                    <dt class="text-on-surface-variant">DOB</dt><dd class="col-span-2 text-on-surface font-medium">{{ $dateOfBirth ?: '—' }}</dd>
                    <dt class="text-on-surface-variant">Gender</dt><dd class="col-span-2 text-on-surface font-medium capitalize">{{ $gender ?: '—' }}</dd>
                    <dt class="text-on-surface-variant">Languages</dt><dd class="col-span-2 text-on-surface font-medium">{{ count($languagesSpoken) ? implode(', ', $languagesSpoken) : '—' }}</dd>
                    <dt class="text-on-surface-variant">Pincode</dt><dd class="col-span-2 text-on-surface font-medium">{{ $pincode ?: '—' }}</dd>
                </dl>
            </div>

            <div class="bg-surface rounded-2xl p-4 border border-outline/10">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-on-surface text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">workspace_premium</span>
                        Professional
                    </h3>
                    <button type="button" wire:click="goToStep(3)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                </div>
                <dl class="grid grid-cols-3 gap-y-1.5 text-sm">
                    <dt class="text-on-surface-variant">Experience</dt><dd class="col-span-2 text-on-surface font-medium">{{ $yearsOfExperience }} years</dd>
                    <dt class="text-on-surface-variant">Education</dt><dd class="col-span-2 text-on-surface font-medium capitalize">{{ str_replace('_', ' ', $education) ?: '—' }}</dd>
                    <dt class="text-on-surface-variant">Services</dt><dd class="col-span-2 text-on-surface font-medium">{{ count($selectedServices) }} selected</dd>
                    <dt class="text-on-surface-variant">Pincodes</dt><dd class="col-span-2 text-on-surface font-medium font-mono text-xs truncate">{{ $serviceAreaPincodes ?: '—' }}</dd>
                </dl>
            </div>

            <div class="bg-surface rounded-2xl p-4 border border-outline/10">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-on-surface text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">verified_user</span>
                        KYC & Bank
                    </h3>
                    <button type="button" wire:click="goToStep(4)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $aadhaarVerified ? 'bg-primary/10 text-primary' : ($aadhaarNumber ? 'bg-tertiary/10 text-tertiary' : 'bg-surface-container text-on-surface-variant') }}">
                        Aadhaar: {{ $aadhaarVerified ? 'Verified' : ($aadhaarNumber ? 'Entered' : 'Skipped') }}
                    </span>
                    <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $panVerified ? 'bg-primary/10 text-primary' : ($panNumber ? 'bg-tertiary/10 text-tertiary' : 'bg-surface-container text-on-surface-variant') }}">
                        PAN: {{ $panVerified ? 'Verified' : ($panNumber ? 'Entered' : 'Skipped') }}
                    </span>
                    <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $bankVerified ? 'bg-primary/10 text-primary' : ($bankAccountNumber ? 'bg-tertiary/10 text-tertiary' : 'bg-surface-container text-on-surface-variant') }}">
                        Bank: {{ $bankVerified ? 'Verified' : ($bankAccountNumber ? 'Entered' : 'Skipped') }}
                    </span>
                    <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $photoDoc ? 'bg-primary/10 text-primary' : 'bg-surface-container text-on-surface-variant' }}">
                        Photo: {{ $photoDoc ? 'Uploaded' : 'None' }}
                    </span>
                </div>
            </div>

            <div class="bg-surface rounded-2xl p-4 border border-outline/10 md:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-on-surface text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">event_available</span>
                        Weekly Availability
                    </h3>
                    <button type="button" wire:click="goToStep(5)" class="text-xs text-primary font-semibold hover:underline">Edit</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    @php $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; @endphp
                    @foreach($availability as $i => $slot)
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $slot['enabled'] ? 'bg-primary/10 text-primary' : 'bg-surface-container text-on-surface-variant line-through' }}">
                            {{ $dayNames[$i] }} {{ $slot['enabled'] ? $slot['start'].'–'.$slot['end'] : '' }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 bg-tertiary/5 rounded-2xl p-4 border border-tertiary/20 flex items-start gap-3">
            <span class="material-symbols-outlined text-tertiary shrink-0">info</span>
            <p class="text-sm text-on-surface-variant">
                A new user account will be created with role <strong>daimaa</strong>. Sensitive fields (Aadhaar, PAN, bank account) are stored encrypted at rest. After submission you'll see the temporary password to share with the Daimaa.
            </p>
        </div>
    </div>
    @endif

    {{-- ===== Navigation ===== --}}
    <div class="flex items-center justify-between mt-8 gap-3">
        @if($step > 1)
            <button wire:click="prevStep" type="button"
                class="px-5 sm:px-6 py-3 rounded-2xl border-2 border-primary text-primary font-bold hover:bg-primary/5 transition-all inline-flex items-center gap-2">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="hidden sm:inline">Back</span>
            </button>
        @else
            <a href="{{ route('admin.daimaas') }}"
                class="px-5 sm:px-6 py-3 rounded-2xl border border-outline/30 text-on-surface-variant font-semibold hover:bg-surface-container transition-all inline-flex items-center gap-2">
                <span class="material-symbols-outlined">close</span>
                <span class="hidden sm:inline">Cancel</span>
            </a>
        @endif

        <div class="text-xs text-on-surface-variant hidden sm:block">Step {{ $step }} of {{ $totalSteps }}</div>

        @if($step < $totalSteps)
            <button wire:click="nextStep" type="button"
                class="px-6 sm:px-8 py-3 rounded-2xl bg-primary text-on-primary font-bold shadow-lg hover:opacity-90 transition-all inline-flex items-center gap-2">
                <span>Continue</span>
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
        @else
            <button wire:click="submit" type="button" wire:loading.attr="disabled" wire:target="submit"
                class="px-6 sm:px-8 py-3 rounded-2xl bg-primary text-on-primary font-bold shadow-lg hover:opacity-90 disabled:opacity-50 transition-all inline-flex items-center gap-2">
                <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    Create Daimaa
                </span>
                <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                    <span class="material-symbols-outlined animate-spin">progress_activity</span>
                    Creating...
                </span>
            </button>
        @endif
    </div>

    @endif
</div>
