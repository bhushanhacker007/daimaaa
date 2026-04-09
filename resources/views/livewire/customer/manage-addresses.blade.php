<div>
    <div class="flex justify-between items-center mb-6">
        <p class="text-on-surface-variant">Manage your saved addresses for service delivery.</p>
        <button wire:click="$set('showForm', true)" class="btn-primary text-sm">+ Add Address</button>
    </div>

    @if($showForm)
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-6 space-y-4">
        <h3 class="font-headline font-bold text-primary">{{ $editingId ? 'Edit' : 'New' }} Address</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Label</label>
                <input type="text" wire:model="label" class="input-field" placeholder="Home, Office...">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Pincode *</label>
                <input type="text" wire:model="pincode" class="input-field" maxlength="6">
                @error('pincode') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-on-surface mb-1 block">Address Line 1 *</label>
                <input type="text" wire:model="addressLine1" class="input-field">
                @error('addressLine1') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Address Line 2</label>
                <input type="text" wire:model="addressLine2" class="input-field">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Landmark</label>
                <input type="text" wire:model="landmark" class="input-field">
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="save" class="btn-primary text-sm">{{ $editingId ? 'Update' : 'Save' }} Address</button>
            <button wire:click="resetForm" class="btn-outline text-sm">Cancel</button>
        </div>
    </div>
    @endif

    <div class="space-y-3">
        @forelse($addresses as $addr)
        <div class="bg-surface-container-lowest rounded-2xl p-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 bg-primary-fixed/40 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">{{ $addr->label === 'Office' ? 'business' : 'home' }}</span>
                </div>
                <div>
                    <p class="font-semibold text-on-surface">{{ $addr->label }}</p>
                    <p class="text-sm text-on-surface-variant">{{ $addr->address_line_1 }}, {{ $addr->pincode }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button wire:click="edit({{ $addr->id }})" class="text-sm text-primary hover:text-primary-container transition-colors">Edit</button>
                <button wire:click="delete({{ $addr->id }})" wire:confirm="Delete this address?" class="text-sm text-error hover:text-error/70 transition-colors">Delete</button>
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-surface-container-lowest rounded-2xl">
            <span class="material-symbols-outlined text-4xl text-on-surface-variant/30 mb-2">location_off</span>
            <p class="text-on-surface-variant">No addresses saved yet</p>
        </div>
        @endforelse
    </div>
</div>
