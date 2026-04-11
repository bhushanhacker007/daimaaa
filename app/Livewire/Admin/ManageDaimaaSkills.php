<?php

namespace App\Livewire\Admin;

use App\Models\DaimaaServiceQualification;
use App\Models\Service;
use App\Models\User;
use Livewire\Component;

class ManageDaimaaSkills extends Component
{
    /** daimaaId => [serviceId => bool] */
    public array $qualifications = [];

    public function mount(): void
    {
        $this->loadQualifications();
    }

    protected function loadQualifications(): void
    {
        $daimaas = User::where('role', 'daimaa')
            ->whereHas('daimaaProfile')
            ->pluck('id');

        $services = Service::where('is_active', true)->pluck('id');

        $existing = DaimaaServiceQualification::whereIn('daimaa_id', $daimaas)
            ->pluck('service_id', 'daimaa_id')
            ->toArray();

        $existingMap = [];
        DaimaaServiceQualification::whereIn('daimaa_id', $daimaas)->get()->each(function ($q) use (&$existingMap) {
            $existingMap[$q->daimaa_id][$q->service_id] = $q->is_qualified;
        });

        $this->qualifications = [];
        foreach ($daimaas as $did) {
            foreach ($services as $sid) {
                $this->qualifications[$did][$sid] = $existingMap[$did][$sid] ?? false;
            }
        }
    }

    public function toggle(int $daimaaId, int $serviceId): void
    {
        $current = $this->qualifications[$daimaaId][$serviceId] ?? false;
        $newValue = !$current;

        DaimaaServiceQualification::updateOrCreate(
            ['daimaa_id' => $daimaaId, 'service_id' => $serviceId],
            ['is_qualified' => $newValue]
        );

        $this->qualifications[$daimaaId][$serviceId] = $newValue;
    }

    public function qualifyAll(int $daimaaId): void
    {
        $services = Service::where('is_active', true)->pluck('id');

        foreach ($services as $sid) {
            DaimaaServiceQualification::updateOrCreate(
                ['daimaa_id' => $daimaaId, 'service_id' => $sid],
                ['is_qualified' => true]
            );
            $this->qualifications[$daimaaId][$sid] = true;
        }
    }

    public function render()
    {
        $daimaas = User::where('role', 'daimaa')
            ->whereHas('daimaaProfile')
            ->with('daimaaProfile')
            ->orderBy('name')
            ->get();

        $services = Service::where('is_active', true)->orderBy('sort_order')->get();

        return view('livewire.admin.manage-daimaa-skills', [
            'daimaas' => $daimaas,
            'services' => $services,
        ]);
    }
}
