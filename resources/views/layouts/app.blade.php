<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#a855d6">
    <title>@yield('title', 'e-Pilot YKN') — Yayasan Kebajikan Negara Malaysia</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/epilot.css') }}?v=2" rel="stylesheet">

    @stack('styles')
</head>
<body>
    @php
        $currentUser = auth()->user();
        $pendingBadgeCount = $currentUser && $currentUser->isAdmin()
            ? \App\Models\Booking::where('status', \App\Models\Booking::STATUS_PENDING)->count()
            : 0;
        $initials = collect(explode(' ', preg_replace('/^(Encik|Puan|Cik|Tuan|Dr\.?)\s+/i', '', $currentUser->name)))
            ->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    @endphp

    @php
        $navigation = function () use ($currentUser, $pendingBadgeCount) {
            ob_start();
    @endphp
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
    @php
            return ob_get_clean();
        };
        $navHtml = $navigation();
    @endphp

    {{-- Desktop Sidebar --}}
    <aside class="sidebar-desktop">
        <a href="{{ route('dashboard') }}" class="brand-box">
            <img src="{{ asset('images/ykn-emblem.png') }}" alt="Logo YKN" class="brand-logo">
            <div>
                <div class="brand-name">e-Pilot <span>YKN</span></div>
                <div class="brand-sub">Yayasan Kebajikan Negara Malaysia</div>
            </div>
        </a>

        <nav class="sidebar-nav flex-grow-1 overflow-auto py-2">
            {!! $navHtml !!}
        </nav>

        <div class="sidebar-user">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="avatar">{{ $initials }}</span>
                <div class="overflow-hidden lh-sm">
                    <div class="fw-semibold text-truncate" style="font-size: 0.8rem; color: #1f1d2b;">{{ $currentUser->name }}</div>
                    <div class="text-muted text-truncate" style="font-size: 0.7rem;">{{ $currentUser->jawatan }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-light btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Log Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile Offcanvas Sidebar --}}
    <div class="offcanvas offcanvas-start offcanvas-ykn" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/ykn-emblem.png') }}" alt="Logo YKN" class="brand-logo" style="width: 34px; height: 34px;">
                <div>
                    <div class="brand-name">e-Pilot <span>YKN</span></div>
                    <div class="brand-sub">Yayasan Kebajikan Negara Malaysia</div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <nav class="sidebar-nav flex-grow-1 py-2">
                {!! $navHtml !!}
            </nav>
            <div class="sidebar-user">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> Log Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Wrapper --}}
    <div class="main-wrapper">
        <header class="topbar d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <button class="btn btn-light btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="page-title text-truncate">@yield('page-title', 'Papan Pemuka')</h1>
                    @hasSection('page-subtitle')
                        <p class="page-subtitle d-none d-md-block">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <span class="d-none d-xl-inline text-muted" style="font-size: 0.75rem;">
                    <i class="bi bi-calendar4 me-1"></i>{{ now()->locale('ms')->translatedFormat('l, d F Y') }}
                </span>
                <span class="role-chip d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="bi bi-shield-check"></i> {{ $currentUser->roleLabel() }}
                </span>
                <span class="avatar d-lg-none">{{ $initials }}</span>
            </div>
        </header>

        <main class="container-fluid p-3 p-md-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-start gap-2 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2 border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Sila semak ralat berikut:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="app-footer py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>&copy; {{ date('Y') }} Yayasan Kebajikan Negara Malaysia. Hak cipta terpelihara.</div>
            <div>e-Pilot YKN &bull; Sistem Pengurusan Kenderaan &amp; Pilot</div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
