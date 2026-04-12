<?php

namespace App\Livewire\Daimaa;

use App\Models\AvailabilitySlot;
use App\Models\DaimaaProfile;
use App\Models\Document;
use App\Services\CashfreeService;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegistrationWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 6;

    // ── Step 1: Personal ──
    public string $phone = '';
    public ?string $dateOfBirth = null;
    public string $gender = '';
    public string $maritalStatus = '';
    public string $bloodGroup = '';
    public array $languagesSpoken = [];
    public string $emergencyContactName = '';
    public string $emergencyContactPhone = '';
    public string $city = '';
    public string $addressLine = '';
    public string $pincode = '';

    // ── Step 2: Professional ──
    public int $yearsOfExperience = 0;
    public string $bio = '';
    public string $education = '';
    public string $serviceAreaPincodes = '';

    // ── Step 3: Bank & Payment ──
    public string $bankAccountNumber = '';
    public string $bankAccountNumberConfirm = '';
    public string $bankIfsc = '';
    public string $upiId = '';
    public bool $bankVerified = false;
    public ?string $bankVerifyMessage = null;
    public ?string $bankAccountHolder = null;

    // ── Step 4: KYC Documents ──
    public string $aadhaarNumber = '';
    public bool $aadhaarVerified = false;
    public ?string $aadhaarRefId = null;
    public string $aadhaarOtp = '';
    public bool $aadhaarOtpSent = false;
    public ?string $aadhaarName = null;
    public ?string $aadhaarMessage = null;

    public string $panNumber = '';
    public bool $panVerified = false;
    public ?string $panName = null;
    public ?string $panMessage = null;

    public $aadhaarFrontDoc = null;
    public $aadhaarBackDoc = null;
    public $panCardDoc = null;
    public $photoDoc = null;
    public $certificateDoc = null;
    public $policeVerificationDoc = null;

    // ── Step 5: Availability ──
    public array $availability = [];

    public function mount()
    {
        foreach (range(0, 6) as $day) {
            $this->availability[$day] = ['enabled' => $day >= 1 && $day <= 5, 'start' => '08:00', 'end' => '18:00'];
        }
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->step = min($this->step + 1, $this->totalSteps);
    }

    public function prevStep()
    {
        $this->step = max($this->step - 1, 1);
    }

    // ── Cashfree: Aadhaar OTP ──

    public function sendAadhaarOtp()
    {
        $this->validate(['aadhaarNumber' => 'required|string|size:12']);

        $service = app(CashfreeService::class);
        $result = $service->aadhaarSendOtp($this->aadhaarNumber);

        if ($result['success']) {
            $this->aadhaarRefId = $result['ref_id'];
            $this->aadhaarOtpSent = true;
            $this->aadhaarMessage = 'OTP sent to Aadhaar-linked mobile.';
        } else {
            $this->aadhaarMessage = $result['message'] ?? 'Failed to send OTP.';
        }
    }

    public function verifyAadhaarOtp()
    {
        $this->validate(['aadhaarOtp' => 'required|string|size:6']);

        $service = app(CashfreeService::class);
        $result = $service->aadhaarVerifyOtp($this->aadhaarRefId, $this->aadhaarOtp);

        if ($result['success']) {
            $this->aadhaarVerified = true;
            $this->aadhaarName = $result['name'];
            $this->aadhaarMessage = 'Aadhaar verified successfully!';
        } else {
            $this->aadhaarMessage = $result['message'] ?? 'OTP verification failed.';
        }
    }

    // ── Cashfree: PAN ──

    public function verifyPan()
    {
        $this->validate(['panNumber' => ['required', 'string', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/']]);

        $service = app(CashfreeService::class);
        $result = $service->verifyPan($this->panNumber);

        if ($result['success'] && ($result['valid'] ?? false)) {
            $this->panVerified = true;
            $this->panName = $result['name'];
            $this->panMessage = 'PAN verified!';
        } else {
            $this->panMessage = $result['message'] ?? 'PAN verification failed.';
        }
    }

    // ── Cashfree: Bank Account ──

    public function verifyBankAccount()
    {
        $this->validate([
            'bankAccountNumber' => 'required|string|min:8',
            'bankIfsc' => ['required', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        ]);

        $service = app(CashfreeService::class);
        $result = $service->verifyBankAccount($this->bankAccountNumber, $this->bankIfsc);

        if ($result['success']) {
            $this->bankVerified = true;
            $this->bankAccountHolder = $result['account_holder'];
            $this->bankVerifyMessage = 'Bank account verified!';
        } else {
            $this->bankVerifyMessage = $result['message'] ?? 'Bank verification failed.';
        }
    }

    // ── Submit ──

    public function submit()
    {
        $user = auth()->user();
        $user->update(['role' => 'daimaa', 'phone' => $this->phone ?: $user->phone]);

        $profile = DaimaaProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'years_of_experience' => $this->yearsOfExperience,
                'bio' => $this->bio,
                'education' => $this->education,
                'status' => 'pending',
                'service_area_pincodes' => array_filter(explode(',', $this->serviceAreaPincodes)),
                'date_of_birth' => $this->dateOfBirth ?: null,
                'gender' => $this->gender ?: null,
                'marital_status' => $this->maritalStatus ?: null,
                'blood_group' => $this->bloodGroup ?: null,
                'languages_spoken' => $this->languagesSpoken,
                'emergency_contact_name' => $this->emergencyContactName ?: null,
                'emergency_contact_phone' => $this->emergencyContactPhone ?: null,
                'aadhaar_number' => $this->aadhaarNumber ?: null,
                'aadhaar_name' => $this->aadhaarName,
                'aadhaar_verified_at' => $this->aadhaarVerified ? now() : null,
                'pan_number' => $this->panNumber ?: null,
                'pan_name' => $this->panName,
                'pan_verified_at' => $this->panVerified ? now() : null,
                'bank_account_number' => $this->bankAccountNumber ?: null,
                'bank_ifsc' => $this->bankIfsc ?: null,
                'bank_account_holder' => $this->bankAccountHolder,
                'bank_verified_at' => $this->bankVerified ? now() : null,
                'upi_id' => $this->upiId ?: null,
            ]
        );

        // Upload documents
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
                    'status' => 'pending',
                ]);
            }
        }

        // Availability slots
        foreach ($this->availability as $day => $slot) {
            if ($slot['enabled']) {
                AvailabilitySlot::updateOrCreate(
                    ['daimaa_id' => $user->id, 'day_of_week' => $day],
                    ['start_time' => $slot['start'], 'end_time' => $slot['end'], 'is_available' => true]
                );
            }
        }

        return redirect()->route('daimaa.dashboard')
            ->with('success', 'Registration submitted! We will review your profile shortly.');
    }

    protected function validateStep(): void
    {
        match ($this->step) {
            1 => $this->validate([
                'phone' => 'required|string|min:10',
                'pincode' => 'required|string|size:6',
                'dateOfBirth' => 'required|date|before:-18 years',
                'gender' => 'required|in:female,male,other',
            ]),
            2 => $this->validate([
                'yearsOfExperience' => 'required|integer|min:0',
                'bio' => 'required|string|min:20',
            ]),
            3 => $this->validate([
                'bankAccountNumber' => 'required|string|min:8',
                'bankAccountNumberConfirm' => 'required|same:bankAccountNumber',
                'bankIfsc' => ['required', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            ]),
            4 => $this->validate([
                'photoDoc' => 'required|file|image|max:2048',
            ]),
            default => null,
        };
    }

    public function render()
    {
        return view('livewire.daimaa.registration-wizard');
    }
}
