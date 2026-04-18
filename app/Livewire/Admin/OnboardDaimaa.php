<?php

namespace App\Livewire\Admin;

use App\Models\Address;
use App\Models\AvailabilitySlot;
use App\Models\City;
use App\Models\DaimaaProfile;
use App\Models\DaimaaServiceQualification;
use App\Models\Document;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class OnboardDaimaa extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 6;

    // ── Step 1: Account ──
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public bool $autoGeneratePassword = true;
    public string $password = '';
    public bool $preVerify = false;

    // ── Step 2: Personal ──
    public ?string $dateOfBirth = null;
    public string $gender = '';
    public string $maritalStatus = '';
    public string $bloodGroup = '';
    public array $languagesSpoken = [];
    public string $emergencyContactName = '';
    public string $emergencyContactPhone = '';
    public string $addressLine = '';
    public ?int $cityId = null;
    public string $pincode = '';

    // ── Step 3: Professional ──
    public int $yearsOfExperience = 0;
    public string $education = '';
    public string $bio = '';
    public string $serviceAreaPincodes = '';
    public array $selectedServices = [];

    // ── Step 4: KYC & Bank ──
    public string $aadhaarNumber = '';
    public string $aadhaarName = '';
    public bool $aadhaarVerified = false;

    public string $panNumber = '';
    public string $panName = '';
    public bool $panVerified = false;

    public string $bankAccountNumber = '';
    public string $bankAccountNumberConfirm = '';
    public string $bankIfsc = '';
    public string $bankName = '';
    public string $bankAccountHolder = '';
    public string $upiId = '';
    public bool $bankVerified = false;

    public $aadhaarFrontDoc = null;
    public $aadhaarBackDoc = null;
    public $panCardDoc = null;
    public $photoDoc = null;
    public $certificateDoc = null;
    public $policeVerificationDoc = null;

    // ── Step 5: Availability ──
    public array $availability = [];

    // ── Result ──
    public bool $success = false;
    public ?string $createdEmail = null;
    public ?string $createdPassword = null;
    public ?int $createdUserId = null;

    public function mount(): void
    {
        foreach (range(0, 6) as $day) {
            $this->availability[$day] = [
                'enabled' => $day >= 1 && $day <= 5,
                'start' => '08:00',
                'end' => '18:00',
            ];
        }
    }

    // ── Step navigation ──

    public function nextStep(): void
    {
        $this->validateStep();
        $this->step = min($this->step + 1, $this->totalSteps);
    }

    public function prevStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function goToStep(int $target): void
    {
        if ($target < $this->step) {
            $this->step = max($target, 1);
        }
    }

    public function updatedAutoGeneratePassword($value): void
    {
        if ($value) {
            $this->password = '';
        }
    }

    public function toggleService(int $serviceId): void
    {
        if (in_array($serviceId, $this->selectedServices)) {
            $this->selectedServices = array_values(array_diff($this->selectedServices, [$serviceId]));
        } else {
            $this->selectedServices[] = $serviceId;
        }
    }

    public function selectAllServices(): void
    {
        $this->selectedServices = Service::where('is_active', true)->pluck('id')->toArray();
    }

    public function clearServices(): void
    {
        $this->selectedServices = [];
    }

    // ── Submit ──

    public function submit(): void
    {
        $this->validateAll();

        $plainPassword = $this->autoGeneratePassword
            ? Str::password(12, true, true, false)
            : $this->password;

        DB::transaction(function () use ($plainPassword) {
            $user = User::create([
                'name' => $this->name,
                'email' => strtolower($this->email),
                'phone' => $this->phone,
                'role' => 'daimaa',
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
            ]);

            $profile = DaimaaProfile::create([
                'user_id' => $user->id,
                'years_of_experience' => $this->yearsOfExperience,
                'bio' => $this->bio ?: null,
                'status' => $this->preVerify ? 'verified' : 'pending',
                'verified_at' => $this->preVerify ? now() : null,
                'service_area_pincodes' => $this->parsePincodes($this->serviceAreaPincodes),
                'date_of_birth' => $this->dateOfBirth ?: null,
                'gender' => $this->gender ?: null,
                'marital_status' => $this->maritalStatus ?: null,
                'education' => $this->education ?: null,
                'blood_group' => $this->bloodGroup ?: null,
                'languages_spoken' => $this->languagesSpoken ?: [],
                'emergency_contact_name' => $this->emergencyContactName ?: null,
                'emergency_contact_phone' => $this->emergencyContactPhone ?: null,
                'aadhaar_number' => $this->aadhaarNumber ?: null,
                'aadhaar_name' => $this->aadhaarName ?: null,
                'aadhaar_verified_at' => $this->aadhaarVerified ? now() : null,
                'pan_number' => $this->panNumber ? strtoupper($this->panNumber) : null,
                'pan_name' => $this->panName ?: null,
                'pan_verified_at' => $this->panVerified ? now() : null,
                'bank_account_number' => $this->bankAccountNumber ?: null,
                'bank_ifsc' => $this->bankIfsc ? strtoupper($this->bankIfsc) : null,
                'bank_name' => $this->bankName ?: null,
                'bank_account_holder' => $this->bankAccountHolder ?: ($this->bankAccountNumber ? $this->name : null),
                'bank_verified_at' => $this->bankVerified ? now() : null,
                'upi_id' => $this->upiId ?: null,
            ]);

            if ($this->cityId && $this->addressLine && $this->pincode) {
                Address::create([
                    'user_id' => $user->id,
                    'label' => 'Home',
                    'address_line_1' => $this->addressLine,
                    'city_id' => $this->cityId,
                    'pincode' => $this->pincode,
                    'is_default' => true,
                ]);
            }

            foreach ($this->selectedServices as $serviceId) {
                DaimaaServiceQualification::updateOrCreate(
                    ['daimaa_id' => $user->id, 'service_id' => $serviceId],
                    ['is_qualified' => true]
                );
            }

            foreach ($this->availability as $day => $slot) {
                if (!empty($slot['enabled'])) {
                    AvailabilitySlot::updateOrCreate(
                        ['daimaa_id' => $user->id, 'day_of_week' => $day],
                        [
                            'start_time' => $slot['start'] ?: '08:00',
                            'end_time' => $slot['end'] ?: '18:00',
                            'is_available' => true,
                        ]
                    );
                }
            }

            $docMap = [
                'aadhaarFrontDoc' => 'aadhaar_front',
                'aadhaarBackDoc' => 'aadhaar_back',
                'panCardDoc' => 'pan_card',
                'photoDoc' => 'photo',
                'certificateDoc' => 'certificate',
                'policeVerificationDoc' => 'police_verification',
            ];

            foreach ($docMap as $prop => $type) {
                if ($this->{$prop}) {
                    $path = $this->{$prop}->store('kyc-documents', 'local');
                    Document::create([
                        'documentable_type' => DaimaaProfile::class,
                        'documentable_id' => $profile->id,
                        'type' => $type,
                        'file_path' => $path,
                        'original_name' => $this->{$prop}->getClientOriginalName(),
                        'status' => $this->preVerify ? 'approved' : 'pending',
                        'reviewed_by' => $this->preVerify ? auth()->id() : null,
                        'reviewed_at' => $this->preVerify ? now() : null,
                    ]);
                }
            }

            $this->createdUserId = $user->id;
        });

        $this->success = true;
        $this->createdEmail = strtolower($this->email);
        $this->createdPassword = $plainPassword;
    }

    public function startAnother(): void
    {
        $this->reset();
        $this->mount();
    }

    // ── Helpers ──

    protected function parsePincodes(string $raw): array
    {
        return collect(explode(',', $raw))
            ->map(fn ($p) => trim($p))
            ->filter(fn ($p) => $p !== '' && preg_match('/^\d{6}$/', $p))
            ->values()
            ->all();
    }

    protected function validateStep(): void
    {
        match ($this->step) {
            1 => $this->validate([
                'name' => ['required', 'string', 'min:2', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
                'phone' => ['required', 'string', 'min:10', 'max:15', Rule::unique('users', 'phone')],
                'password' => [Rule::requiredIf(! $this->autoGeneratePassword), 'nullable', 'string', 'min:8'],
            ]),
            2 => $this->validate([
                'dateOfBirth' => ['required', 'date', 'before:-18 years'],
                'gender' => ['required', 'in:female,male,other'],
                'maritalStatus' => ['nullable', 'in:single,married,widowed,divorced'],
                'bloodGroup' => ['nullable', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
                'languagesSpoken' => ['array'],
                'emergencyContactName' => ['nullable', 'string', 'max:255'],
                'emergencyContactPhone' => ['nullable', 'string', 'max:15'],
                'addressLine' => ['nullable', 'string', 'max:500'],
                'cityId' => ['nullable', 'exists:cities,id'],
                'pincode' => ['nullable', 'string', 'size:6', 'regex:/^\d{6}$/'],
            ]),
            3 => $this->validate([
                'yearsOfExperience' => ['required', 'integer', 'min:0', 'max:60'],
                'education' => ['nullable', 'in:none,primary,secondary,higher_secondary,graduate,post_graduate'],
                'bio' => ['required', 'string', 'min:20', 'max:2000'],
                'serviceAreaPincodes' => ['required', 'string'],
                'selectedServices' => ['required', 'array', 'min:1'],
                'selectedServices.*' => ['integer', 'exists:services,id'],
            ], [
                'selectedServices.required' => 'Please select at least one service this Daimaa is qualified for.',
                'selectedServices.min' => 'Please select at least one service this Daimaa is qualified for.',
                'serviceAreaPincodes.required' => 'Service area pincodes are required.',
            ]),
            4 => $this->validate([
                'aadhaarNumber' => ['nullable', 'string', 'size:12', 'regex:/^\d{12}$/'],
                'panNumber' => ['nullable', 'string', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/'],
                'bankAccountNumber' => ['nullable', 'string', 'min:8', 'max:30'],
                'bankAccountNumberConfirm' => ['nullable', 'same:bankAccountNumber'],
                'bankIfsc' => ['nullable', 'string', 'regex:/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/'],
                'aadhaarFrontDoc' => ['nullable', 'file', 'image', 'max:4096'],
                'aadhaarBackDoc' => ['nullable', 'file', 'image', 'max:4096'],
                'panCardDoc' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
                'photoDoc' => ['nullable', 'file', 'image', 'max:4096'],
                'certificateDoc' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
                'policeVerificationDoc' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            ]),
            5 => $this->validate([
                'availability' => ['required', 'array'],
            ]),
            default => null,
        };
    }

    protected function validateAll(): void
    {
        for ($s = 1; $s <= 5; $s++) {
            $orig = $this->step;
            $this->step = $s;
            $this->validateStep();
            $this->step = $orig;
        }
    }

    public function render()
    {
        return view('livewire.admin.onboard-daimaa', [
            'cities' => City::where('is_active', true)->orderBy('name')->get(),
            'services' => Service::where('is_active', true)->with('category')->orderBy('sort_order')->orderBy('name')->get(),
            'allLanguages' => ['Hindi', 'Marathi', 'English', 'Tamil', 'Telugu', 'Kannada', 'Bengali', 'Gujarati', 'Punjabi', 'Malayalam', 'Urdu'],
        ]);
    }
}
