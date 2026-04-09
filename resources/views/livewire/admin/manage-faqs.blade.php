<div>
    <div class="flex justify-end mb-6">
        <button wire:click="$set('showForm', true)" class="btn-primary text-sm">+ Add FAQ</button>
    </div>

    @if($showForm)
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-6 space-y-4">
        <h3 class="font-headline font-bold text-primary">{{ $editingId ? 'Edit' : 'New' }} FAQ</h3>
        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Question *</label>
                <input type="text" wire:model="question" class="input-field">
                @error('question') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-on-surface mb-1 block">Answer *</label>
                <textarea wire:model="answer" class="input-field" rows="4"></textarea>
                @error('answer') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium text-on-surface mb-1 block">Category</label>
                    <input type="text" wire:model="category" class="input-field" placeholder="General">
                </div>
                <div>
                    <label class="text-sm font-medium text-on-surface mb-1 block">Sort Order</label>
                    <input type="number" wire:model="sortOrder" class="input-field">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="isActive" class="rounded text-primary focus:ring-primary">
                        <span class="text-sm font-medium text-on-surface">Active</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="save" class="btn-primary text-sm">{{ $editingId ? 'Update' : 'Create' }}</button>
            <button wire:click="resetForm" class="btn-outline text-sm">Cancel</button>
        </div>
    </div>
    @endif

    <div class="space-y-3">
        @forelse($faqs as $f)
        <div class="bg-surface-container-lowest rounded-2xl p-5 flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    @if($f->category)<span class="text-xs px-2 py-0.5 bg-surface-container rounded-full text-on-surface-variant">{{ $f->category }}</span>@endif
                    @if(!$f->is_active)<span class="text-xs px-2 py-0.5 bg-error-container rounded-full text-on-error-container">Inactive</span>@endif
                </div>
                <p class="font-semibold text-on-surface">{{ $f->question }}</p>
                <p class="text-sm text-on-surface-variant mt-1">{{ Str::limit($f->answer, 120) }}</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <button wire:click="edit({{ $f->id }})" class="text-xs text-primary hover:underline">Edit</button>
                <button wire:click="delete({{ $f->id }})" wire:confirm="Delete?" class="text-xs text-error hover:underline">Delete</button>
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-surface-container-lowest rounded-2xl">
            <p class="text-on-surface-variant">No FAQs.</p>
        </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $faqs->links() }}</div>
</div>
