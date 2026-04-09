<?php

namespace App\Livewire\Customer;

use App\Models\Address;
use Livewire\Component;

class ManageAddresses extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $label = 'Home';
    public string $addressLine1 = '';
    public string $addressLine2 = '';
    public string $landmark = '';
    public string $pincode = '';

    protected $rules = [
        'label' => 'required|string|max:50',
        'addressLine1' => 'required|string|max:255',
        'pincode' => 'required|string|size:6',
    ];

    public function save()
    {
        $this->validate();
        $data = [
            'label' => $this->label,
            'address_line_1' => $this->addressLine1,
            'address_line_2' => $this->addressLine2,
            'landmark' => $this->landmark,
            'city_id' => 1,
            'pincode' => $this->pincode,
        ];

        if ($this->editingId) {
            Address::where('user_id', auth()->id())->findOrFail($this->editingId)->update($data);
        } else {
            auth()->user()->addresses()->create($data);
        }

        $this->resetForm();
    }

    public function edit(int $id)
    {
        $address = Address::where('user_id', auth()->id())->findOrFail($id);
        $this->editingId = $id;
        $this->label = $address->label;
        $this->addressLine1 = $address->address_line_1;
        $this->addressLine2 = $address->address_line_2 ?? '';
        $this->landmark = $address->landmark ?? '';
        $this->pincode = $address->pincode;
        $this->showForm = true;
    }

    public function delete(int $id)
    {
        Address::where('user_id', auth()->id())->findOrFail($id)->delete();
    }

    public function resetForm()
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->label = 'Home';
        $this->addressLine1 = '';
        $this->addressLine2 = '';
        $this->landmark = '';
        $this->pincode = '';
    }

    public function render()
    {
        return view('livewire.customer.manage-addresses', [
            'addresses' => auth()->user()->addresses()->with('city')->get(),
        ]);
    }
}
