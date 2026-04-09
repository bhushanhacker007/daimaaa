<?php

namespace App\Livewire\Admin;

use App\Models\Pincode;
use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class ManagePincodes extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public string $pincode = '';
    public int $cityId = 0;
    public bool $isServiceable = true;

    public function save()
    {
        $this->validate([
            'pincode' => 'required|string|size:6|unique:pincodes,pincode',
            'cityId' => 'required|exists:cities,id',
        ]);
        Pincode::create(['pincode' => $this->pincode, 'city_id' => $this->cityId, 'is_serviceable' => $this->isServiceable]);
        $this->showForm = false;
        $this->reset(['pincode', 'cityId']);
        $this->isServiceable = true;
    }

    public function toggleServiceable(int $id)
    {
        $p = Pincode::findOrFail($id);
        $p->update(['is_serviceable' => !$p->is_serviceable]);
    }

    public function delete(int $id) { Pincode::findOrFail($id)->delete(); }

    public function render()
    {
        $pincodes = Pincode::with('city')
            ->when($this->search, fn ($q) => $q->where('pincode', 'like', "%{$this->search}%"))
            ->orderBy('pincode')
            ->paginate(20);

        return view('livewire.admin.manage-pincodes', ['pincodes' => $pincodes, 'cities' => City::orderBy('name')->get()]);
    }
}
