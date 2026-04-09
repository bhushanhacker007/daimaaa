<?php

namespace App\Livewire\Daimaa;

use App\Models\DaimaaProfile;
use App\Models\Document;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegistrationWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1: Personal
    public string $phone = '';
    public string $city = '';
    public string $addressLine = '';
    public string $pincode = '';

    // Step 2: Professional
    public int $yearsOfExperience = 0;
    public string $bio = '';
    public array $servicesOffered = [];
    public string $serviceAreaPincodes = '';

    // Step 3: KYC
    public $aadhaarDoc = null;
    public $photoDoc = null;
    public $certificateDoc = null;

    // Step 4: Availability
    public array $availability = [];

    public function mount()
    {
        foreach (range(0, 6) as $day) {
            $this->availability[$day] = ['enabled' => $day < 6, 'start' => '08:00', 'end' => '18:00'];
        }
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->step = min($this->step + 1, 5);
    }

    public function prevStep()
    {
        $this->step = max($this->step - 1, 1);
    }

    public function submit()
    {
        $user = auth()->user();
        $user->update(['role' => 'daimaa', 'phone' => $this->phone ?: $user->phone]);

        $profile = DaimaaProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'years_of_experience' => $this->yearsOfExperience,
                'bio' => $this->bio,
                'status' => 'pending',
                'service_area_pincodes' => array_filter(explode(',', $this->serviceAreaPincodes)),
            ]
        );

        foreach (['aadhaarDoc' => 'aadhaar', 'photoDoc' => 'photo', 'certificateDoc' => 'certificate'] as $prop => $type) {
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

        foreach ($this->availability as $day => $slot) {
            if ($slot['enabled']) {
                $user->load('daimaaProfile');
                \App\Models\AvailabilitySlot::updateOrCreate(
                    ['daimaa_id' => $user->id, 'day_of_week' => $day],
                    ['start_time' => $slot['start'], 'end_time' => $slot['end'], 'is_available' => true]
                );
            }
        }

        return redirect()->route('daimaa.dashboard')->with('success', 'Registration submitted! We will review your profile shortly.');
    }

    protected function validateStep(): void
    {
        match ($this->step) {
            1 => $this->validate(['phone' => 'required|string|min:10', 'pincode' => 'required|string|size:6']),
            2 => $this->validate(['yearsOfExperience' => 'required|integer|min:0', 'bio' => 'required|string|min:20']),
            3 => $this->validate(['aadhaarDoc' => 'required|file|max:5120', 'photoDoc' => 'required|file|image|max:2048']),
            default => null,
        };
    }

    public function render()
    {
        return view('livewire.daimaa.registration-wizard');
    }
}
