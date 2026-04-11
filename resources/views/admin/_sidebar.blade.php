@php $items = [
    ['route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
    ['route' => 'admin.bookings', 'icon' => 'calendar_month', 'label' => 'Bookings'],
    ['route' => 'admin.customers', 'icon' => 'group', 'label' => 'Customers'],
    ['route' => 'admin.daimaas', 'icon' => 'diversity_2', 'label' => 'Daimaas'],
    ['route' => 'admin.daimaa-skills', 'icon' => 'workspace_premium', 'label' => 'Daimaa Skills'],
    ['route' => 'admin.kyc', 'icon' => 'verified_user', 'label' => 'KYC Review'],
    ['route' => 'admin.services', 'icon' => 'spa', 'label' => 'Services'],
    ['route' => 'admin.packages', 'icon' => 'inventory_2', 'label' => 'Packages'],
    ['route' => 'admin.add-ons', 'icon' => 'add_box', 'label' => 'Add-ons'],
    ['route' => 'admin.assignments', 'icon' => 'assignment_ind', 'label' => 'Assignments'],
    ['route' => 'admin.payments', 'icon' => 'payments', 'label' => 'Payments'],
    ['route' => 'admin.payouts', 'icon' => 'account_balance', 'label' => 'Payouts'],
    ['route' => 'admin.coupons', 'icon' => 'local_offer', 'label' => 'Coupons'],
    ['route' => 'admin.pincodes', 'icon' => 'pin_drop', 'label' => 'Pincodes'],
    ['route' => 'admin.faqs', 'icon' => 'help', 'label' => 'FAQs'],
    ['route' => 'admin.testimonials', 'icon' => 'format_quote', 'label' => 'Testimonials'],
    ['route' => 'admin.cms', 'icon' => 'article', 'label' => 'CMS Pages'],
    ['route' => 'admin.settings', 'icon' => 'settings', 'label' => 'Settings'],
    ['route' => 'admin.audit-logs', 'icon' => 'history', 'label' => 'Audit Logs'],
]; @endphp

@foreach($items as $item)
<a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm rounded-xl transition-colors {{ request()->routeIs($item['route']) ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">{{ $item['icon'] }}</span> {{ $item['label'] }}
</a>
@endforeach
