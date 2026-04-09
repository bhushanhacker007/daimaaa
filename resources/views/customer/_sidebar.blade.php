<a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.dashboard') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">dashboard</span> Dashboard
</a>
<a href="{{ route('customer.book') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.book') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">add_circle</span> New Booking
</a>
<a href="{{ route('customer.bookings') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.bookings*') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">calendar_month</span> My Bookings
</a>
<a href="{{ route('customer.addresses') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.addresses') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">location_on</span> Addresses
</a>
<a href="{{ route('customer.reviews') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('customer.reviews') ? 'bg-primary-fixed/40 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container' }}">
    <span class="material-symbols-outlined text-xl">rate_review</span> My Reviews
</a>
<a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl text-on-surface-variant hover:bg-surface-container">
    <span class="material-symbols-outlined text-xl">person</span> Profile
</a>
