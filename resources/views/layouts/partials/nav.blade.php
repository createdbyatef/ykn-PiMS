<div class="nav-section-label">Utama</div>
@if($currentUser->isPilot())
    <a href="{{ route('pilot.trips.index') }}" class="nav-link {{ request()->routeIs('pilot.trips.*') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Tugasan Saya
    </a>
@else
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2"></i> Papan Pemuka
    </a>
@endif
<a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Kalendar Ketersediaan
</a>

@if($currentUser->isStaff() || $currentUser->isAdmin())
    <div class="nav-section-label">Tempahan</div>
    <a href="{{ route('bookings.create') }}" class="nav-link {{ request()->routeIs('bookings.create') ? 'active' : '' }}">
        <i class="bi bi-plus-square"></i> Permohonan Baharu
    </a>
    <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.index') || request()->routeIs('bookings.show') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i>
        <span class="flex-grow-1">{{ $currentUser->isAdmin() ? 'Semua Tempahan' : 'Tempahan Saya' }}</span>
        @if($pendingBadgeCount > 0)
            <span class="badge rounded-pill bg-warning text-dark">{{ $pendingBadgeCount }}</span>
        @endif
    </a>
@endif

@if($currentUser->isAdmin())
    <div class="nav-section-label">Unit Pentadbiran</div>
    <a href="{{ route('admin.vehicles.index') }}" class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
        <i class="bi bi-car-front"></i> Kenderaan
    </a>
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Pengguna &amp; Pilot
    </a>
@endif
