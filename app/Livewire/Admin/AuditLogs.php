<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn ($q) => $q->where('action', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.audit-logs', ['logs' => $logs]);
    }
}
