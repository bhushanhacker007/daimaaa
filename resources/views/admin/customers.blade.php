<x-dashboard-layout>
    <x-slot:title>Customers — Admin</x-slot:title>
    <x-slot:heading>Manage Customers</x-slot:heading>
    <x-slot:sidebar>@include('admin._sidebar')</x-slot:sidebar>
    @if(view()->exists('livewire.admin.manage-customers'))
        <livewire:admin.manage-customers />
    @else
        <div class="bg-surface-container-lowest rounded-2xl p-8 text-center">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4">construction</span>
            <p class="text-on-surface-variant">Manage Customers — CRUD module</p>
        </div>
    @endif
</x-dashboard-layout>
