<div>
    <div class="flex justify-end mb-6">
        <button wire:click="$set('showForm', true)" class="btn-primary text-sm">+ Add Coupon</button>
    </div>

    @if($showForm)
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-6 space-y-4">
        <h3 class="font-headline font-bold text-primary">{{ $editingId ? 'Edit' : 'New' }} Coupon</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Code *</label>
                <input type="text" wire:model="code" class="input-field uppercase">
                @error('code') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Type</label>
                <select wire:model="type" class="input-field">
                    <option value="percent">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Value *</label>
                <input type="number" wire:model="value" class="input-field" step="0.01">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Min Order</label>
                <input type="number" wire:model="minOrderAmount" class="input-field" step="0.01">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Max Discount</label>
                <input type="number" wire:model="maxDiscount" class="input-field" step="0.01">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Max Uses</label>
                <input type="number" wire:model="maxUses" class="input-field">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Valid From</label>
                <input type="date" wire:model="validFrom" class="input-field">
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Valid Until</label>
                <input type="date" wire:model="validUntil" class="input-field">
            </div>
            <div class="flex items-end">
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
                <th class="px-4 py-3 font-medium">Code</th>
                <th class="px-4 py-3 font-medium">Type</th>
                <th class="px-4 py-3 font-medium">Value</th>
                <th class="px-4 py-3 font-medium">Used</th>
                <th class="px-4 py-3 font-medium">Valid Until</th>
                <th class="px-4 py-3 font-medium">Status</th>
                <th class="px-4 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($coupons as $c)
                <tr class="hover:bg-surface-container/50 transition-colors">
                    <td class="px-4 py-3 font-mono font-bold text-primary">{{ $c->code }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ ucfirst($c->type) }}</td>
                    <td class="px-4 py-3 text-on-surface">{{ $c->type === 'percent' ? $c->value . '%' : '₹' . number_format($c->value) }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $c->used_count }}/{{ $c->max_uses ?? '∞' }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $c->valid_until?->format('M d, Y') ?? '—' }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-bold {{ $c->isValid() ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">{{ $c->isValid() ? 'Active' : 'Expired' }}</span></td>
                    <td class="px-4 py-3 flex gap-2">
                        <button wire:click="edit({{ $c->id }})" class="text-xs text-primary hover:underline">Edit</button>
                        <button wire:click="delete({{ $c->id }})" wire:confirm="Delete?" class="text-xs text-error hover:underline">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">No coupons.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $coupons->links() }}</div>
</div>
