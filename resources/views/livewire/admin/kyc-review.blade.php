<div>
    <div class="flex gap-2 mb-6">
        @foreach(['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
        <button wire:click="$set('filter', '{{ $key }}')" class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ $filter === $key ? 'cta-gradient text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">{{ $label }}</button>
        @endforeach
    </div>

    @forelse($profiles as $profile)
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-lg font-semibold text-on-surface">{{ $profile->user?->name }}</h3>
                <p class="text-sm text-on-surface-variant">{{ $profile->user?->email }} · {{ $profile->years_of_experience }}y experience</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ match($profile->status) {
                'pending' => 'bg-tertiary-fixed/30 text-tertiary',
                'verified' => 'bg-primary text-on-primary',
                'rejected' => 'bg-error-container text-on-error-container',
                default => 'bg-surface-container text-on-surface-variant',
            } }}">{{ ucfirst($profile->status) }}</span>
        </div>
        @if($profile->bio)
        <p class="text-sm text-on-surface-variant mb-4">{{ Str::limit($profile->bio, 200) }}</p>
        @endif
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($profile->documents as $doc)
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-surface-container text-on-surface-variant">
                <span class="material-symbols-outlined text-sm">description</span>
                {{ ucfirst($doc->type) }} — {{ ucfirst($doc->status) }}
            </span>
            @endforeach
        </div>
        @if($profile->status === 'pending')
        <div class="flex gap-2">
            <button wire:click="approve({{ $profile->id }})" class="btn-primary text-sm">
                <span class="material-symbols-outlined mr-1 text-lg">check_circle</span> Approve
            </button>
            <button wire:click="reject({{ $profile->id }})" wire:confirm="Are you sure you want to reject this profile?" class="px-4 py-2 text-sm font-medium text-error rounded-full ghost-border hover:bg-error-container transition-colors">
                <span class="material-symbols-outlined mr-1 text-lg">cancel</span> Reject
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="text-center py-16 bg-surface-container-lowest rounded-2xl">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4">verified_user</span>
        <p class="text-on-surface-variant">No {{ $filter === 'all' ? '' : $filter }} profiles.</p>
    </div>
    @endforelse

    <div class="mt-4">{{ $profiles->links() }}</div>
</div>
