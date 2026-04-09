<div>
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" class="input-field flex-1" placeholder="Search services...">
        <button wire:click="$set('showForm', true)" class="btn-primary text-sm whitespace-nowrap">+ Add Service</button>
    </div>

    @if($showForm)
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-6 space-y-4">
        <h3 class="font-headline font-bold text-primary">{{ $editingId ? 'Edit' : 'New' }} Service</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Name *</label>
                <input type="text" wire:model.live="name" class="input-field">
                @error('name') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Slug</label>
                <input type="text" wire:model="slug" class="input-field">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Category *</label>
                <select wire:model="categoryId" class="input-field">
                    <option value="">Select</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('categoryId') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Duration (min) *</label>
                <input type="number" wire:model="durationMinutes" class="input-field" min="15">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Base Price (₹) *</label>
                <input type="number" wire:model="basePrice" class="input-field" step="0.01">
                @error('basePrice') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Icon</label>
                <input type="text" wire:model="icon" class="input-field" placeholder="Material icon name">
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-on-surface mb-1 block">Short Description</label>
                <input type="text" wire:model="shortDescription" class="input-field">
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-on-surface mb-1 block">Description</label>
                <textarea wire:model="description" class="input-field" rows="3"></textarea>
            </div>
            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="isActive" class="rounded text-primary focus:ring-primary">
                    <span class="text-sm font-medium text-on-surface">Active</span>
                </label>
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="save" class="btn-primary text-sm">{{ $editingId ? 'Update' : 'Create' }}</button>
            <button wire:click="resetForm" class="btn-outline text-sm">Cancel</button>
        </div>
    </div>
    @endif

    <div class="bg-surface-container-lowest rounded-2xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-surface-container text-on-surface-variant text-left">
                <th class="px-4 py-3 font-medium">Service</th>
                <th class="px-4 py-3 font-medium">Category</th>
                <th class="px-4 py-3 font-medium">Duration</th>
                <th class="px-4 py-3 font-medium">Price</th>
                <th class="px-4 py-3 font-medium">Status</th>
                <th class="px-4 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($services as $s)
                <tr class="hover:bg-surface-container/50 transition-colors">
                    <td class="px-4 py-3"><div class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-lg">{{ $s->icon ?? 'spa' }}</span><span class="font-medium text-on-surface">{{ $s->name }}</span></div></td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $s->category?->name }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $s->duration_minutes }}m</td>
                    <td class="px-4 py-3 font-semibold text-primary">₹{{ number_format($s->base_price) }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleActive({{ $s->id }})" class="px-2 py-1 rounded-full text-xs font-bold {{ $s->is_active ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</button>
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <button wire:click="edit({{ $s->id }})" class="text-xs text-primary hover:underline">Edit</button>
                        <button wire:click="delete({{ $s->id }})" wire:confirm="Delete this service?" class="text-xs text-error hover:underline">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-on-surface-variant">No services found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $services->links() }}</div>
</div>
