<x-dashboard-layout>
    <x-slot:title>Booking Details — Daimaa</x-slot:title>
    <x-slot:heading>Booking Details</x-slot:heading>
    <x-slot:sidebar>@include('customer._sidebar')</x-slot:sidebar>
    <livewire:customer.booking-detail :bookingId="$id" />
</x-dashboard-layout>
