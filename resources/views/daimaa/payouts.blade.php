<x-dashboard-layout>
    <x-slot:title>Payouts — Daimaa</x-slot:title>
    <x-slot:heading>My Payouts</x-slot:heading>
    <x-slot:sidebar>@include('daimaa._sidebar')</x-slot:sidebar>

    @php $payouts = \App\Models\Payout::where('daimaa_id', auth()->id())->latest()->paginate(15); @endphp

    @if($payouts->count())
    <div class="bg-surface-container-lowest rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-on-surface-variant text-left">
                    <th class="px-6 py-3 font-medium">Period</th>
                    <th class="px-6 py-3 font-medium">Amount</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payouts as $payout)
                <tr class="hover:bg-surface-container/50 transition-colors">
                    <td class="px-6 py-4 text-on-surface">{{ $payout->period }}</td>
                    <td class="px-6 py-4 font-semibold text-primary">₹{{ number_format($payout->amount) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $payout->status === 'processed' ? 'bg-primary text-on-primary' : 'bg-tertiary-fixed/30 text-tertiary' }}">{{ ucfirst($payout->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $payout->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payouts->links() }}</div>
    @else
    <div class="text-center py-16 bg-surface-container-lowest rounded-2xl">
        <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-4">payments</span>
        <p class="text-on-surface-variant">No payouts yet.</p>
    </div>
    @endif
</x-dashboard-layout>
