<a href="{{ route('daimaa.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('daimaa.dashboard') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">dashboard</span> Dashboard
</a>
<a href="{{ route('daimaa.bookings') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('daimaa.bookings') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">calendar_month</span> Assigned Bookings
</a>
<a href="{{ route('daimaa.schedule') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('daimaa.schedule') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">event_available</span> My Schedule
</a>
<a href="{{ route('daimaa.payouts') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('daimaa.payouts') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">payments</span> Payouts
</a>
<a href="{{ route('daimaa.profile') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('daimaa.profile') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">person</span> Profile
</a>
