<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Livewire\Component;
use Livewire\WithPagination;

class ManageCoupons extends Component
{
    use WithPagination;

    public bool $showForm = false;
    public ?int $editingId = null;
    public string $code = '';
    public string $type = 'percent';
    public string $value = '';
    public string $minOrderAmount = '';
    public string $maxDiscount = '';
    public ?int $maxUses = null;
    public string $validFrom = '';
    public string $validUntil = '';
    public bool $isActive = true;

    public function save()
    {
        $this->validate([
            'code' => 'required|string|unique:coupons,code,' . $this->editingId,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        $data = [
            'code' => strtoupper($this->code),
            'type' => $this->type,
            'value' => $this->value,
            'min_order_amount' => $this->minOrderAmount ?: null,
            'max_discount' => $this->maxDiscount ?: null,
            'max_uses' => $this->maxUses,
            'valid_from' => $this->validFrom ?: null,
            'valid_until' => $this->validUntil ?: null,
            'is_active' => $this->isActive,
        ];

        $this->editingId ? Coupon::findOrFail($this->editingId)->update($data) : Coupon::create($data);
        $this->resetForm();
    }

    public function edit(int $id)
    {
        $c = Coupon::findOrFail($id);
        $this->editingId = $id;
        $this->code = $c->code;
        $this->type = $c->type;
        $this->value = (string) $c->value;
        $this->minOrderAmount = (string) ($c->min_order_amount ?? '');
        $this->maxDiscount = (string) ($c->max_discount ?? '');
        $this->maxUses = $c->max_uses;
        $this->validFrom = $c->valid_from?->format('Y-m-d') ?? '';
        $this->validUntil = $c->valid_until?->format('Y-m-d') ?? '';
        $this->isActive = $c->is_active;
        $this->showForm = true;
    }

    public function delete(int $id)
    {
        Coupon::findOrFail($id)->delete();
    }

    public function resetForm()
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->reset(['code', 'type', 'value', 'minOrderAmount', 'maxDiscount', 'maxUses', 'validFrom', 'validUntil']);
        $this->isActive = true;
        $this->type = 'percent';
    }

    public function render()
    {
        return view('livewire.admin.manage-coupons', ['coupons' => Coupon::latest()->paginate(15)]);
    }
}
