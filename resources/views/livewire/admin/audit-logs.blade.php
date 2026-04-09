<div>
    <div class="mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" class="input-field max-w-md" placeholder="Search actions...">
    </div>

    <div class="bg-surface-container-lowest rounded-2xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-surface-container text-on-surface-variant text-left">
                <th class="px-4 py-3 font-medium">Time</th>
                <th class="px-4 py-3 font-medium">User</th>
                <th class="px-4 py-3 font-medium">Action</th>
                <th class="px-4 py-3 font-medium">Resource</th>
                <th class="px-4 py-3 font-medium">IP</th>
            </tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="hover:bg-surface-container/50 transition-colors">
                    <td class="px-4 py-3 text-xs text-on-surface-variant">{{ $log->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-3 text-on-surface">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-1 bg-surface-container rounded-lg text-xs font-mono">{{ $log->action }}</span></td>
                    <td class="px-4 py-3 text-on-surface-variant text-xs">{{ class_basename($log->auditable_type ?? '') }} #{{ $log->auditable_id }}</td>
                    <td class="px-4 py-3 text-on-surface-variant text-xs font-mono">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">No audit logs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</div>
