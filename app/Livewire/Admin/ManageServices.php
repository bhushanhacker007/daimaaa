<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ManageServices extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $slug = '';
    public string $shortDescription = '';
    public string $description = '';
    public int $categoryId = 0;
    public int $durationMinutes = 60;
    public string $basePrice = '';
    public string $pricePerHour = '';
    public string $minHours = '1.0';
    public string $maxHours = '8.0';
    public string $hourIncrement = '0.5';
    public string $icon = 'spa';
    public bool $instantAvailable = false;
    public string $instantSurcharge = '0';
    public bool $isActive = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:services,slug,' . $this->editingId,
            'categoryId' => 'required|exists:service_categories,id',
            'durationMinutes' => 'required|integer|min:15',
            'basePrice' => 'required|numeric|min:0',
            'pricePerHour' => 'nullable|numeric|min:0',
            'minHours' => 'nullable|numeric|min:0.5|max:24',
            'maxHours' => 'nullable|numeric|min:0.5|max:24',
        ];
    }

    public function updatedName($value)
    {
        if (!$this->editingId) {
            $this->slug = \Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->shortDescription,
            'description' => $this->description,
            'category_id' => $this->categoryId,
            'duration_minutes' => $this->durationMinutes,
            'base_price' => $this->basePrice,
            'price_per_hour' => $this->pricePerHour !== '' ? $this->pricePerHour : null,
            'min_hours' => $this->pricePerHour !== '' ? $this->minHours : 1.0,
            'max_hours' => $this->pricePerHour !== '' ? $this->maxHours : 8.0,
            'hour_increment' => $this->pricePerHour !== '' ? $this->hourIncrement : 0.5,
            'icon' => $this->icon,
            'instant_available' => $this->instantAvailable,
            'instant_surcharge' => $this->instantAvailable ? ($this->instantSurcharge ?: 0) : 0,
            'is_active' => $this->isActive,
        ];

        if ($this->editingId) {
            Service::findOrFail($this->editingId)->update($data);
        } else {
            Service::create($data);
        }

        $this->resetForm();
    }

    public function edit(int $id)
    {
        $s = Service::findOrFail($id);
        $this->editingId = $id;
        $this->name = $s->name;
        $this->slug = $s->slug;
        $this->shortDescription = $s->short_description ?? '';
        $this->description = $s->description ?? '';
        $this->categoryId = $s->category_id;
        $this->durationMinutes = $s->duration_minutes;
        $this->basePrice = (string) $s->base_price;
        $this->pricePerHour = $s->price_per_hour ? (string) $s->price_per_hour : '';
        $this->minHours = (string) ($s->min_hours ?? '1.0');
        $this->maxHours = (string) ($s->max_hours ?? '8.0');
        $this->hourIncrement = (string) ($s->hour_increment ?? '0.5');
        $this->icon = $s->icon ?? 'spa';
        $this->instantAvailable = $s->instant_available ?? false;
        $this->instantSurcharge = (string) ($s->instant_surcharge ?? '0');
        $this->isActive = $s->is_active;
        $this->showForm = true;
    }

    public function toggleActive(int $id)
    {
        $s = Service::findOrFail($id);
        $s->update(['is_active' => !$s->is_active]);
    }

    public function delete(int $id)
    {
        Service::findOrFail($id)->delete();
    }

    public function resetForm()
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->reset(['name', 'slug', 'shortDescription', 'description', 'durationMinutes', 'basePrice', 'pricePerHour', 'minHours', 'maxHours', 'hourIncrement', 'icon', 'instantAvailable', 'instantSurcharge', 'isActive', 'categoryId']);
        $this->isActive = true;
        $this->durationMinutes = 60;
        $this->minHours = '1.0';
        $this->maxHours = '8.0';
        $this->hourIncrement = '0.5';
    }

    public function render()
    {
        $services = Service::with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->paginate(15);

        return view('livewire.admin.manage-services', [
            'services' => $services,
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
        ]);
    }
}
