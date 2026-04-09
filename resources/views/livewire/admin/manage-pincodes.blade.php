<div>
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" class="input-field flex-1" placeholder="Search pincodes...">
        <button wire:click="$set('showForm', true)" class="btn-primary text-sm whitespace-nowrap">+ Add Pincode</button>
    </div>

    @if($showForm)
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-6">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Pincode *</label>
                <input type="text" wire:model="pincode" class="input-field" maxlength="6">
                @error('pincode') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">City *</label>
                <select wire:model="cityId" class="input-field">
                    <option value="">Select</option>
                    @foreach($cities as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button wire:click="save" class="btn-primary text-sm">Add</button>
                <button wire:click="$set('showForm', false)" class="btn-outline text-sm">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-surface-container-lowest rounded-2xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-surface-container text-on-surface-variant text-left">
                <th class="px-4 py-3 font-medium">Pincode</th>
                <th class="px-4 py-3 font-medium">City</th>
                <th class="px-4 py-3 font-medium">State</th>
                <th class="px-4 py-3 font-medium">Serviceable</th>
                <th class="px-4 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($pincodes as $p)
                <tr class="hover:bg-surface-container/50 transition-colors">
                    <td class="px-4 py-3 font-mono font-semibold text-on-surface">{{ $p->pincode }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $p->city?->name }}</td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $p->city?->state }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleServiceable({{ $p->id }})" class="px-2 py-1 rounded-full text-xs font-bold {{ $p->is_serviceable ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">{{ $p->is_serviceable ? 'Yes' : 'No' }}</button>
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="delete({{ $p->id }})" wire:confirm="Delete?" class="text-xs text-error hover:underline">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">No pincodes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $pincodes->links() }}</div>
</div>
