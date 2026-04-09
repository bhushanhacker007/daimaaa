<div>
    {{-- Progress --}}
    <div class="mb-8">
        <div class="flex items-center justify-between max-w-xl mx-auto">
            @foreach(['Personal', 'Professional', 'KYC Upload', 'Availability', 'Review'] as $i => $label)
            <div class="flex flex-col items-center gap-2 {{ $step >= $i + 1 ? 'text-primary' : 'text-on-surface-variant/40' }}">
                <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-bold {{ $step > $i + 1 ? 'bg-primary text-on-primary' : ($step === $i + 1 ? 'cta-gradient text-on-primary' : 'bg-surface-container text-on-surface-variant') }}">
                    @if($step > $i + 1)<span class="material-symbols-outlined text-lg">check</span>@else{{ $i + 1 }}@endif
                </div>
                <span class="text-xs font-medium hidden sm:block">{{ $label }}</span>
            </div>
            @if(!$loop->last)<div class="flex-1 h-0.5 mx-2 {{ $step > $i + 1 ? 'bg-primary' : 'bg-surface-container' }}"></div>@endif
            @endforeach
        </div>
    </div>

    @if($step === 1)
    <div class="space-y-6">
        <h2 class="text-2xl font-headline font-bold text-primary">Personal Information</h2>
        <div class="bg-surface-container-lowest rounded-2xl p-6 space-y-4">
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Mobile Number *</label>
                <input type="tel" wire:model="phone" class="input-field" placeholder="+91 XXXXX XXXXX">
                @error('phone') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">City</label>
                <input type="text" wire:model="city" class="input-field" placeholder="Mumbai">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Address</label>
                <textarea wire:model="addressLine" class="input-field" rows="2"></textarea>
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Pincode *</label>
                <input type="text" wire:model="pincode" class="input-field" maxlength="6">
                @error('pincode') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
    @endif

    @if($step === 2)
    <div class="space-y-6">
        <h2 class="text-2xl font-headline font-bold text-primary">Professional Details</h2>
        <div class="bg-surface-container-lowest rounded-2xl p-6 space-y-4">
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Years of Experience *</label>
                <input type="number" wire:model="yearsOfExperience" class="input-field" min="0">
                @error('yearsOfExperience') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">About You *</label>
                <textarea wire:model="bio" class="input-field" rows="4" placeholder="Tell us about your experience and skills..."></textarea>
                @error('bio') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Service Area Pincodes</label>
                <input type="text" wire:model="serviceAreaPincodes" class="input-field" placeholder="400001, 400002, 400050">
                <p class="text-xs text-on-surface-variant mt-1">Comma-separated pincodes you can serve</p>
            </div>
        </div>
    </div>
    @endif

    @if($step === 3)
    <div class="space-y-6">
        <h2 class="text-2xl font-headline font-bold text-primary">KYC Documents</h2>
        <p class="text-on-surface-variant">Upload your identity and verification documents. All documents are reviewed securely.</p>
        <div class="bg-surface-container-lowest rounded-2xl p-6 space-y-6">
            <div>
                <label class="text-sm font-medium text-on-surface mb-2 block">Aadhaar Card *</label>
                <input type="file" wire:model="aadhaarDoc" class="input-field" accept=".pdf,.jpg,.jpeg,.png">
                @error('aadhaarDoc') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
                @if($aadhaarDoc)<p class="text-xs text-primary mt-1">{{ $aadhaarDoc->getClientOriginalName() }} uploaded</p>@endif
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-2 block">Passport Photo *</label>
                <input type="file" wire:model="photoDoc" class="input-field" accept=".jpg,.jpeg,.png">
                @error('photoDoc') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-2 block">Experience Certificate (optional)</label>
                <input type="file" wire:model="certificateDoc" class="input-field" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
    </div>
    @endif

    @if($step === 4)
    <div class="space-y-6">
        <h2 class="text-2xl font-headline font-bold text-primary">Your Availability</h2>
        <div class="bg-surface-container-lowest rounded-2xl p-6 space-y-4">
            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $i => $day)
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 w-32">
                    <input type="checkbox" wire:model="availability.{{ $i }}.enabled" class="rounded text-primary focus:ring-primary">
                    <span class="text-sm font-medium text-on-surface">{{ $day }}</span>
                </label>
                @if($availability[$i]['enabled'] ?? false)
                <input type="time" wire:model="availability.{{ $i }}.start" class="input-field w-32 text-sm">
                <span class="text-on-surface-variant">to</span>
                <input type="time" wire:model="availability.{{ $i }}.end" class="input-field w-32 text-sm">
                @else
                <span class="text-sm text-on-surface-variant/50">Unavailable</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($step === 5)
    <div class="space-y-6">
        <h2 class="text-2xl font-headline font-bold text-primary">Review & Submit</h2>
        <div class="bg-surface-container-lowest rounded-2xl p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-on-surface-variant">Phone</p><p class="font-semibold">{{ $phone }}</p></div>
                <div><p class="text-on-surface-variant">Experience</p><p class="font-semibold">{{ $yearsOfExperience }} years</p></div>
            </div>
            <div class="text-sm"><p class="text-on-surface-variant">About</p><p>{{ $bio }}</p></div>
            <div class="text-sm"><p class="text-on-surface-variant">Documents</p><p class="font-semibold text-primary">{{ $aadhaarDoc ? '✓ Aadhaar' : '✗ Aadhaar' }} · {{ $photoDoc ? '✓ Photo' : '✗ Photo' }}</p></div>
        </div>
        <div class="bg-tertiary-fixed/20 rounded-2xl p-6">
            <h3 class="font-semibold text-tertiary mb-2">What happens next?</h3>
            <ol class="list-decimal list-inside text-sm text-on-surface-variant space-y-1">
                <li>Our team reviews your profile and documents (1-3 business days)</li>
                <li>You receive a verification status update via email</li>
                <li>Once verified, you'll start receiving booking assignments</li>
            </ol>
        </div>
    </div>
    @endif

    <div class="flex justify-between mt-8 pt-6" style="border-top: 1px solid rgba(218, 193, 186, 0.2);">
        @if($step > 1)
        <button wire:click="prevStep" class="btn-outline"><span class="material-symbols-outlined mr-1 text-lg">arrow_back</span> Back</button>
        @else<div></div>@endif

        @if($step < 5)
        <button wire:click="nextStep" class="btn-primary">Next <span class="material-symbols-outlined ml-1 text-lg">arrow_forward</span></button>
        @else
        <button wire:click="submit" class="btn-primary text-lg px-8"><span class="material-symbols-outlined mr-2">check_circle</span> Submit Application</button>
        @endif
    </div>
</div>
