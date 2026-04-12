<div class="max-w-2xl mx-auto" x-data="{ step: @entangle('step') }">

    {{-- Progress Bar --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            @php $stepLabels = ['Personal', 'Professional', 'Bank', 'Documents', 'Availability', 'Review']; @endphp
            @foreach($stepLabels as $i => $label)
                @php $num = $i + 1; @endphp
                <div class="flex flex-col items-center gap-1 flex-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold transition-all
                        {{ $step > $num ? 'bg-primary text-on-primary' : ($step === $num ? 'bg-primary text-on-primary ring-4 ring-primary/30' : 'bg-surface-container text-on-surface-variant') }}">
                        @if($step > $num)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @else
                            {{ $num }}
                        @endif
                    </div>
                    <span class="text-xs font-medium {{ $step >= $num ? 'text-primary' : 'text-on-surface-variant' }}">{{ $label }}</span>
                </div>
                @if($i < count($stepLabels) - 1)
                    <div class="flex-1 h-1 rounded-full mx-1 mt-[-18px] {{ $step > $num ? 'bg-primary' : 'bg-surface-container' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- STEP 1: Personal Info --}}
    @if($step === 1)
    <div class="bg-surface-container/40 rounded-3xl p-6 sm:p-8 shadow-md">
        <h2 class="text-2xl font-bold text-on-surface mb-1">Personal Information</h2>
        <p class="text-on-surface-variant mb-6">Tell us about yourself</p>

        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Phone Number *</label>
                    <input type="tel" wire:model="phone" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="9876543210">
                    @error('phone') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Date of Birth *</label>
                    <input type="date" wire:model="dateOfBirth" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg">
                    @error('dateOfBirth') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Gender *</label>
                    <select wire:model="gender" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg">
                        <option value="">Select</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Marital Status</label>
                    <select wire:model="maritalStatus" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg">
                        <option value="">Select</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="divorced">Divorced</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Blood Group</label>
                    <select wire:model="bloodGroup" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg">
                        <option value="">Select</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}">{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Languages Spoken</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['Hindi', 'Marathi', 'English', 'Tamil', 'Telugu', 'Kannada', 'Bengali', 'Gujarati'] as $lang)
                        <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-all
                            {{ in_array($lang, $languagesSpoken) ? 'bg-primary/10 border-primary text-primary font-semibold' : 'bg-surface border-outline/30 text-on-surface-variant' }}">
                            <input type="checkbox" wire:model="languagesSpoken" value="{{ $lang }}" class="sr-only">
                            {{ $lang }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Emergency Contact Name</label>
                    <input type="text" wire:model="emergencyContactName" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="Contact person name">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Emergency Contact Phone</label>
                    <input type="tel" wire:model="emergencyContactPhone" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="9876543210">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1">Address</label>
                <input type="text" wire:model="addressLine" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="Full address">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">City</label>
                    <input type="text" wire:model="city" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="City">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Pincode *</label>
                    <input type="text" wire:model="pincode" maxlength="6" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="400001">
                    @error('pincode') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- STEP 2: Professional --}}
    @if($step === 2)
    <div class="bg-surface-container/40 rounded-3xl p-6 sm:p-8 shadow-md">
        <h2 class="text-2xl font-bold text-on-surface mb-1">Professional Details</h2>
        <p class="text-on-surface-variant mb-6">Your work experience & skills</p>

        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Years of Experience *</label>
                    <input type="number" wire:model="yearsOfExperience" min="0" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg">
                    @error('yearsOfExperience') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1">Education Level</label>
                    <select wire:model="education" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg">
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
                <label class="block text-sm font-semibold text-on-surface mb-1">About Yourself / Bio *</label>
                <textarea wire:model="bio" rows="4" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="Describe your experience caring for mothers and babies..."></textarea>
                @error('bio') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1">Service Area Pincodes</label>
                <input type="text" wire:model="serviceAreaPincodes" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="400001, 400002, 400003">
                <p class="text-xs text-on-surface-variant mt-1">Separate multiple pincodes with commas</p>
            </div>
        </div>
    </div>
    @endif

    {{-- STEP 3: Bank & Payment --}}
    @if($step === 3)
    <div class="bg-surface-container/40 rounded-3xl p-6 sm:p-8 shadow-md">
        <h2 class="text-2xl font-bold text-on-surface mb-1">Bank & Payment Details</h2>
        <p class="text-on-surface-variant mb-6">For receiving your salary payments</p>

        <div class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1">Bank Account Number *</label>
                <input type="text" wire:model="bankAccountNumber" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="Enter account number">
                @error('bankAccountNumber') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1">Confirm Account Number *</label>
                <input type="text" wire:model="bankAccountNumberConfirm" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="Re-enter account number">
                @error('bankAccountNumberConfirm') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1">IFSC Code *</label>
                <input type="text" wire:model="bankIfsc" maxlength="11" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg uppercase" placeholder="SBIN0001234">
                @error('bankIfsc') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1">UPI ID (Optional)</label>
                <input type="text" wire:model="upiId" class="w-full px-4 py-3 rounded-xl border border-outline/30 bg-surface focus:ring-2 focus:ring-primary text-on-surface text-lg" placeholder="name@upi">
            </div>

            {{-- Verify Bank Button --}}
            <div>
                @if($bankVerified)
                    <div class="flex items-center gap-2 px-4 py-3 bg-primary/10 rounded-xl">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-primary font-semibold">Bank Verified</span>
                        @if($bankAccountHolder)
                            <span class="text-on-surface-variant ml-2">{{ $bankAccountHolder }}</span>
                        @endif
                    </div>
                @else
                    <button wire:click="verifyBankAccount" wire:loading.attr="disabled"
                        class="w-full px-6 py-3.5 rounded-xl bg-tertiary text-on-tertiary font-bold text-lg hover:opacity-90 disabled:opacity-50 transition-all">
                        <span wire:loading.remove wire:target="verifyBankAccount">Verify Bank Account</span>
                        <span wire:loading wire:target="verifyBankAccount">Verifying...</span>
                    </button>
                    @if($bankVerifyMessage)
                        <p class="text-sm mt-2 {{ $bankVerified ? 'text-primary' : 'text-error' }}">{{ $bankVerifyMessage }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- STEP 4: KYC Documents --}}
    @if($step === 4)
    <div class="bg-surface-container/40 rounded-3xl p-6 sm:p-8 shadow-md">
        <h2 class="text-2xl font-bold text-on-surface mb-1">KYC Documents</h2>
        <p class="text-on-surface-variant mb-6">Identity verification & document uploads</p>

        <div class="space-y-6">
            {{-- Aadhaar Section --}}
            <div class="bg-surface rounded-2xl p-5 border border-outline/20">
                <h3 class="text-lg font-bold text-on-surface mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-sm font-bold">A</span>
                    Aadhaar Verification
                    @if($aadhaarVerified)
                        <span class="ml-auto px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Verified</span>
                    @endif
                </h3>

                <div class="space-y-3">
                    <div class="flex gap-3">
                        <input type="text" wire:model="aadhaarNumber" maxlength="12" placeholder="12-digit Aadhaar number"
                            class="flex-1 px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-lg"
                            {{ $aadhaarVerified ? 'disabled' : '' }}>
                        @if(!$aadhaarOtpSent && !$aadhaarVerified)
                            <button wire:click="sendAadhaarOtp" wire:loading.attr="disabled" class="px-5 py-3 rounded-xl bg-primary text-on-primary font-bold whitespace-nowrap hover:opacity-90 transition-all">
                                <span wire:loading.remove wire:target="sendAadhaarOtp">Send OTP</span>
                                <span wire:loading wire:target="sendAadhaarOtp">Sending...</span>
                            </button>
                        @endif
                    </div>

                    @if($aadhaarOtpSent && !$aadhaarVerified)
                        <div class="flex gap-3">
                            <input type="text" wire:model="aadhaarOtp" maxlength="6" placeholder="Enter 6-digit OTP"
                                class="flex-1 px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-lg">
                            <button wire:click="verifyAadhaarOtp" wire:loading.attr="disabled" class="px-5 py-3 rounded-xl bg-primary text-on-primary font-bold whitespace-nowrap hover:opacity-90 transition-all">
                                <span wire:loading.remove wire:target="verifyAadhaarOtp">Verify</span>
                                <span wire:loading wire:target="verifyAadhaarOtp">Verifying...</span>
                            </button>
                        </div>
                    @endif

                    @if($aadhaarMessage)
                        <p class="text-sm {{ $aadhaarVerified ? 'text-primary' : 'text-error' }}">{{ $aadhaarMessage }}</p>
                    @endif
                    @if($aadhaarName)
                        <p class="text-sm text-on-surface-variant">Name: <strong>{{ $aadhaarName }}</strong></p>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Aadhaar Front</label>
                            <input type="file" wire:model="aadhaarFrontDoc" accept="image/*,.pdf" class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Aadhaar Back</label>
                            <input type="file" wire:model="aadhaarBackDoc" accept="image/*,.pdf" class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                        </div>
                    </div>
                </div>
            </div>

            {{-- PAN Section --}}
            <div class="bg-surface rounded-2xl p-5 border border-outline/20">
                <h3 class="text-lg font-bold text-on-surface mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-tertiary/10 flex items-center justify-center text-tertiary text-sm font-bold">P</span>
                    PAN Verification
                    @if($panVerified)
                        <span class="ml-auto px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Verified</span>
                    @endif
                </h3>

                <div class="space-y-3">
                    <div class="flex gap-3">
                        <input type="text" wire:model="panNumber" maxlength="10" placeholder="ABCDE1234F"
                            class="flex-1 px-4 py-3 rounded-xl border border-outline/30 bg-surface-container/30 focus:ring-2 focus:ring-primary text-on-surface text-lg uppercase"
                            {{ $panVerified ? 'disabled' : '' }}>
                        @if(!$panVerified)
                            <button wire:click="verifyPan" wire:loading.attr="disabled" class="px-5 py-3 rounded-xl bg-tertiary text-on-tertiary font-bold whitespace-nowrap hover:opacity-90 transition-all">
                                <span wire:loading.remove wire:target="verifyPan">Verify PAN</span>
                                <span wire:loading wire:target="verifyPan">Verifying...</span>
                            </button>
                        @endif
                    </div>

                    @if($panMessage)
                        <p class="text-sm {{ $panVerified ? 'text-primary' : 'text-error' }}">{{ $panMessage }}</p>
                    @endif
                    @if($panName)
                        <p class="text-sm text-on-surface-variant">Name: <strong>{{ $panName }}</strong></p>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">PAN Card Upload</label>
                        <input type="file" wire:model="panCardDoc" accept="image/*,.pdf" class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-tertiary/10 file:text-tertiary file:font-semibold">
                    </div>
                </div>
            </div>

            {{-- Photo & Certificate --}}
            <div class="bg-surface rounded-2xl p-5 border border-outline/20">
                <h3 class="text-lg font-bold text-on-surface mb-3">Photo & Certificates</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Passport Photo *</label>
                        <input type="file" wire:model="photoDoc" accept="image/*" class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                        @error('photoDoc') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        @if($photoDoc)
                            <img src="{{ $photoDoc->temporaryUrl() }}" class="w-20 h-20 rounded-xl mt-2 object-cover border-2 border-primary/30" alt="Preview">
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Experience Certificate</label>
                        <input type="file" wire:model="certificateDoc" accept="image/*,.pdf" class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-on-surface-variant mb-1">Police Verification Certificate (optional)</label>
                        <input type="file" wire:model="policeVerificationDoc" accept="image/*,.pdf" class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-semibold">
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- STEP 5: Availability --}}
    @if($step === 5)
    <div class="bg-surface-container/40 rounded-3xl p-6 sm:p-8 shadow-md">
        <h2 class="text-2xl font-bold text-on-surface mb-1">Availability Schedule</h2>
        <p class="text-on-surface-variant mb-6">When are you available for work?</p>

        <div class="space-y-3">
            @php $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
            @foreach($availability as $i => $slot)
                <div class="flex items-center gap-3 py-3 px-4 rounded-xl {{ $slot['enabled'] ? 'bg-primary/5 border border-primary/20' : 'bg-surface border border-outline/10' }}">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="availability.{{ $i }}.enabled" class="sr-only peer">
                        <div class="w-12 h-7 bg-outline/30 peer-focus:ring-2 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                    <span class="w-24 font-bold text-on-surface text-sm">{{ $dayNames[$i] }}</span>
                    @if($slot['enabled'])
                        <input type="time" wire:model="availability.{{ $i }}.start" class="px-3 py-2 rounded-lg border border-outline/30 bg-surface text-on-surface text-sm">
                        <span class="text-on-surface-variant">to</span>
                        <input type="time" wire:model="availability.{{ $i }}.end" class="px-3 py-2 rounded-lg border border-outline/30 bg-surface text-on-surface text-sm">
                    @else
                        <span class="text-on-surface-variant text-sm">Not available</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- STEP 6: Review & Submit --}}
    @if($step === 6)
    <div class="bg-surface-container/40 rounded-3xl p-6 sm:p-8 shadow-md">
        <h2 class="text-2xl font-bold text-on-surface mb-1">Review & Submit</h2>
        <p class="text-on-surface-variant mb-6">Please review all details before submitting</p>

        <div class="space-y-4">
            {{-- Personal --}}
            <div class="bg-surface rounded-2xl p-4 border border-outline/10">
                <h3 class="font-bold text-on-surface mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">1</span>
                    Personal Info
                </h3>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="text-on-surface-variant">Phone</div><div class="text-on-surface font-medium">{{ $phone }}</div>
                    <div class="text-on-surface-variant">DOB</div><div class="text-on-surface font-medium">{{ $dateOfBirth ?: '—' }}</div>
                    <div class="text-on-surface-variant">Gender</div><div class="text-on-surface font-medium capitalize">{{ $gender ?: '—' }}</div>
                    <div class="text-on-surface-variant">City</div><div class="text-on-surface font-medium">{{ $city ?: '—' }}</div>
                    <div class="text-on-surface-variant">Pincode</div><div class="text-on-surface font-medium">{{ $pincode }}</div>
                    @if(count($languagesSpoken))
                        <div class="text-on-surface-variant">Languages</div><div class="text-on-surface font-medium">{{ implode(', ', $languagesSpoken) }}</div>
                    @endif
                </div>
            </div>

            {{-- Professional --}}
            <div class="bg-surface rounded-2xl p-4 border border-outline/10">
                <h3 class="font-bold text-on-surface mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">2</span>
                    Professional
                </h3>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="text-on-surface-variant">Experience</div><div class="text-on-surface font-medium">{{ $yearsOfExperience }} years</div>
                    <div class="text-on-surface-variant">Education</div><div class="text-on-surface font-medium capitalize">{{ str_replace('_', ' ', $education) ?: '—' }}</div>
                </div>
            </div>

            {{-- Bank --}}
            <div class="bg-surface rounded-2xl p-4 border border-outline/10">
                <h3 class="font-bold text-on-surface mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">3</span>
                    Bank Details
                    @if($bankVerified)
                        <span class="ml-auto text-xs px-2 py-0.5 bg-primary/10 text-primary rounded-full font-bold">Verified</span>
                    @endif
                </h3>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="text-on-surface-variant">Account</div><div class="text-on-surface font-medium">XXXX{{ substr($bankAccountNumber, -4) }}</div>
                    <div class="text-on-surface-variant">IFSC</div><div class="text-on-surface font-medium">{{ $bankIfsc }}</div>
                    @if($upiId)
                        <div class="text-on-surface-variant">UPI</div><div class="text-on-surface font-medium">{{ $upiId }}</div>
                    @endif
                </div>
            </div>

            {{-- KYC --}}
            <div class="bg-surface rounded-2xl p-4 border border-outline/10">
                <h3 class="font-bold text-on-surface mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">4</span>
                    KYC Status
                </h3>
                <div class="flex flex-wrap gap-3">
                    <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $aadhaarVerified ? 'bg-primary/10 text-primary' : 'bg-error-container/40 text-error' }}">
                        Aadhaar: {{ $aadhaarVerified ? 'Verified' : 'Pending' }}
                    </span>
                    <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $panVerified ? 'bg-primary/10 text-primary' : 'bg-error-container/40 text-error' }}">
                        PAN: {{ $panVerified ? 'Verified' : 'Pending' }}
                    </span>
                    <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $photoDoc ? 'bg-primary/10 text-primary' : 'bg-error-container/40 text-error' }}">
                        Photo: {{ $photoDoc ? 'Uploaded' : 'Missing' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-tertiary/10 rounded-xl p-4 border border-tertiary/20">
            <p class="text-sm text-on-surface-variant">By submitting, you confirm that all information provided is accurate. Your profile will be reviewed by our team.</p>
        </div>
    </div>
    @endif

    {{-- Navigation Buttons --}}
    <div class="flex justify-between mt-8">
        @if($step > 1)
            <button wire:click="prevStep" class="px-6 py-3.5 rounded-2xl border-2 border-primary text-primary font-bold text-lg hover:bg-primary/5 transition-all">
                Back
            </button>
        @else
            <div></div>
        @endif

        @if($step < $totalSteps)
            <button wire:click="nextStep" class="px-8 py-3.5 rounded-2xl bg-primary text-on-primary font-bold text-lg shadow-lg hover:opacity-90 transition-all">
                Continue
            </button>
        @else
            <button wire:click="submit" wire:loading.attr="disabled" class="px-8 py-3.5 rounded-2xl bg-primary text-on-primary font-bold text-lg shadow-lg hover:opacity-90 disabled:opacity-50 transition-all">
                <span wire:loading.remove wire:target="submit">Submit Registration</span>
                <span wire:loading wire:target="submit">Submitting...</span>
            </button>
        @endif
    </div>
</div>
