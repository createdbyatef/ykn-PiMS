<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#a855d6">
    <title>@yield('title', 'e-Pilot YKN') — {{ __('Yayasan Kebajikan Negara Malaysia') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/epilot.css') }}?v=14" rel="stylesheet">

    @include('partials.splash-head', ['forceSplash' => session('show_splash')])
    @include('partials.skeleton-head')

    <script>
        (function () {
            try {
                if (localStorage.getItem('ykn_sidebar_collapsed') === '1') {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    @stack('styles')
</head>
<body>
    @include('partials.splash')
    <div id="yknTopProgress" class="ykn-top-progress" aria-hidden="true"></div>
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
            <div class="nav-section-label"><span>{{ __('Utama') }}</span></div>
            @if($currentUser->isPilot())
                <a href="{{ route('pilot.trips.index') }}" class="nav-link {{ request()->routeIs('pilot.trips.*') ? 'active' : '' }}" title="{{ __('Tugasan Saya') }}" data-skeleton="dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-label">{{ __('Tugasan Saya') }}</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="{{ __('Papan Pemuka') }}" data-skeleton="dashboard">
                    <i class="bi bi-grid-1x2"></i>
                    <span class="nav-label">{{ __('Papan Pemuka') }}</span>
                </a>
            @endif
            <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}" title="{{ __('Kalendar Ketersediaan') }}" data-skeleton="calendar">
                <i class="bi bi-calendar3"></i>
                <span class="nav-label">{{ __('Kalendar') }}</span>
            </a>

            @if($currentUser->isStaff() || $currentUser->isAdmin())
                <div class="nav-section-label"><span>{{ __('Tempahan') }}</span></div>
                <a href="{{ route('bookings.create') }}" class="nav-link {{ request()->routeIs('bookings.create') ? 'active' : '' }}" title="{{ __('Permohonan Baharu') }}" data-skeleton="form">
                    <i class="bi bi-plus-circle"></i>
                    <span class="nav-label">{{ __('Mohon Baru') }}</span>
                </a>
                <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.index') || request()->routeIs('bookings.show') ? 'active' : '' }}" title="{{ $currentUser->isAdmin() ? __('Semua Tempahan') : __('Tempahan Saya') }}" data-skeleton="table">
                    <i class="bi bi-journal-text"></i>
                    <span class="nav-label">{{ $currentUser->isAdmin() ? __('Tempahan') : __('Rekod Saya') }}</span>
                </a>
            @endif

            @if($currentUser->isAdmin())
                <div class="nav-section-label"><span>{{ __('Pentadbiran') }}</span></div>
                <a href="{{ route('admin.vehicles.index') }}" class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}" title="{{ __('Kenderaan') }}" data-skeleton="table">
                    <i class="bi bi-car-front"></i>
                    <span class="nav-label">{{ __('Kenderaan') }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" title="{{ __('Pengguna & Pilot') }}" data-skeleton="table">
                    <i class="bi bi-people"></i>
                    <span class="nav-label">{{ __('Pengguna & Pilot') }}</span>
                </a>
            @endif
    @php
            return ob_get_clean();
        };
        $navHtml = $navigation();
    @endphp

    {{-- Desktop Sidebar (FinSet Soft-Lavender Shell + Floating `<` Edge Toggle) --}}
    <aside class="sidebar-desktop" id="desktopSidebar">
        <div class="brand-box-wrap">
            <a href="{{ route('dashboard') }}" class="brand-box" title="e-Pilot YKN — {{ __('Yayasan Kebajikan Negara Malaysia') }}">
                <img src="{{ asset('images/ykn-emblem.png') }}" alt="Logo YKN" class="brand-logo">
                <div class="brand-text">
                    <div class="brand-name">e-Pilot <span>YKN</span></div>
                    <div class="brand-sub">{{ __('Yayasan Kebajikan Negara') }}</div>
                </div>
            </a>
            <button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="{{ __('Kecilkan / Besarkan Sidebar') }}" aria-label="Toggle Sidebar">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            {!! $navHtml !!}
        </nav>

        {{-- Interactive YKN Corporate Identity Card (Visi | Misi | Teras — Zero Repetition, Zero Scroll) --}}
        <div class="sidebar-widgets mt-auto">
            <div class="ykn-identity-card">
                <div class="ykn-identity-top">
                    <span class="ykn-est-badge">YKN &middot; 1981</span>
                    <div class="ykn-tab-pills" role="tablist" aria-label="{{ __('Identiti Korporat YKN') }}">
                        <button type="button" class="ykn-tab-btn active" data-ykn-tab="visi">{{ __('Visi') }}</button>
                        <button type="button" class="ykn-tab-btn" data-ykn-tab="misi">{{ __('Misi') }}</button>
                        <button type="button" class="ykn-tab-btn" data-ykn-tab="nilai">{{ __('Nilai') }}</button>
                    </div>
                </div>

                <div class="ykn-identity-body">
                    <div class="ykn-tab-pane" data-ykn-pane="visi">
                        <div class="ykn-vision-quote">&ldquo;{{ __('Tabung Kebajikan Terulung di Malaysia.') }}&rdquo;</div>
                        <div class="ykn-teras-row">
                            <span class="ykn-teras-chip"><i class="bi bi-heart-fill me-1 text-warning"></i>{{ __('Kebajikan') }}</span>
                            <span class="ykn-teras-chip">{{ __('Pendidikan') }}</span>
                            <span class="ykn-teras-chip">{{ __('Kesihatan') }}</span>
                        </div>
                    </div>

                    <div class="ykn-tab-pane d-none" data-ykn-pane="misi">
                        <p class="ykn-mission-text">
                            {{ __('Menjalin kerjasama strategik menjana dana dan perkhidmatan demi meningkatkan taraf hidup keluarga serta kanak-kanak yang memerlukan.') }}
                        </p>
                    </div>

                    <div class="ykn-tab-pane d-none" data-ykn-pane="nilai">
                        <div class="ykn-value-item"><i class="bi bi-shield-check"></i><span>{{ __('Ketelusan & Akauntabiliti') }}</span></div>
                        <div class="ykn-value-item"><i class="bi bi-heart-pulse"></i><span>{{ __('Keprihatinan & Kehalusan Budi') }}</span></div>
                        <div class="ykn-value-item"><i class="bi bi-people-fill"></i><span>{{ __('Kerjasama & Saling Membantu') }}</span></div>
                    </div>
                </div>

                <div class="ykn-identity-footer">
                    <span style="font-size: 0.61rem; color: rgba(255,255,255,0.72);">KPWKM</span>
                    <button type="button" class="ykn-more-btn" data-bs-toggle="modal" data-bs-target="#yknProfileModal">
                        {{ __('Profil YKN') }} <i class="bi bi-arrow-up-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="sidebar-bottom">
            <div class="sidebar-user-card" title="{{ $currentUser->name }} — {{ $currentUser->roleLabel() }}">
                <div class="sidebar-user-left">
                    <span class="avatar" style="width: 30px; height: 30px; font-size: 0.67rem;">{{ $initials }}</span>
                    <div class="sidebar-user-meta lh-sm">
                        <div class="fw-semibold text-truncate" style="font-size: 0.73rem; color: var(--ykn-text);">{{ $currentUser->name }}</div>
                        <div class="text-muted text-truncate" style="font-size: 0.63rem;">{{ $currentUser->roleLabel() }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="sidebar-icon-logout" title="{{ __('Log Keluar') }}" aria-label="{{ __('Log Keluar') }}">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>

            <div class="sidebar-collapsed-actions d-none">
                <span class="avatar" style="width: 34px; height: 34px; font-size: 0.7rem;" title="{{ $currentUser->name }}">{{ $initials }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-icon-logout" title="{{ __('Log Keluar') }}" aria-label="{{ __('Log Keluar') }}">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile Offcanvas Sidebar --}}
    <div class="offcanvas offcanvas-start offcanvas-ykn" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/ykn-emblem.png') }}" alt="Logo YKN" class="brand-logo" style="width: 34px; height: 34px;">
                <div>
                    <div class="brand-name">e-Pilot <span>YKN</span></div>
                    <div class="brand-sub">{{ __('Yayasan Kebajikan Negara Malaysia') }}</div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Tutup') }}"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <nav class="sidebar-nav flex-grow-1 py-2">
                {!! $navHtml !!}
            </nav>
            <div class="sidebar-bottom border-top">
                <div class="d-flex justify-content-center mb-2">
                    <div class="lang-switch-pill" role="group" aria-label="Language switch">
                        <a href="{{ route('locale.switch', 'ms') }}" class="lang-switch-opt {{ app()->getLocale() === 'ms' ? 'active' : '' }}">BM</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="lang-switch-opt {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="logout-label">{{ __('Log Keluar') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Wrapper --}}
    <div class="main-wrapper">
        <header class="topbar d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <button class="topbar-circle-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="{{ __('Menu') }}">
                    <i class="bi bi-list"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="page-title text-truncate">@yield('page-title', __('Papan Pemuka'))</h1>
                    @hasSection('page-subtitle')
                        <p class="page-subtitle d-none d-md-block text-truncate">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <div class="lang-switch-pill" role="group" aria-label="Language switch">
                    <a href="{{ route('locale.switch', 'ms') }}" class="lang-switch-opt {{ app()->getLocale() === 'ms' ? 'active' : '' }}" title="Bahasa Melayu">BM</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="lang-switch-opt {{ app()->getLocale() === 'en' ? 'active' : '' }}" title="English">EN</a>
                </div>
                <a href="{{ route('calendar.index') }}" class="topbar-circle-btn d-none d-sm-inline-flex" title="{{ __('Kalendar Ketersediaan') }}">
                    <i class="bi bi-calendar3"></i>
                </a>
                @if($currentUser->isAdmin())
                    <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="topbar-circle-btn" title="{{ __('Permohonan Menunggu Kelulusan') }}">
                        <i class="bi bi-bell"></i>
                        @if($pendingBadgeCount > 0)
                            <span class="topbar-circle-badge">{{ $pendingBadgeCount }}</span>
                        @endif
                    </a>
                @endif
            </div>
        </header>

        <main class="container-fluid px-3 px-md-4 pt-3 pb-4 flex-grow-1">
            @include('partials.skeleton', ['variant' => trim($__env->yieldContent('skeleton', 'dashboard'))])

            <div class="page-content-stage" id="yknPageContent">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-start gap-2 border-0 shadow-sm mb-3" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2 border-0 shadow-sm mb-3" role="alert">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ __('Sila semak ralat berikut:') }}</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="app-footer py-2 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>&copy; {{ date('Y') }} {{ __('Yayasan Kebajikan Negara Malaysia') }}. {{ __('Hak cipta terpelihara.') }}</div>
            <div>e-Pilot YKN &bull; {{ __('Sistem Pengurusan Kenderaan & Pilot') }}</div>
        </footer>
    </div>

    {{-- YKN Corporate Profile Modal (Visi, Misi & Teras Utama) --}}
    <div class="modal fade" id="yknProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow overflow-hidden">
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #3c316e 0%, #6f4ea8 55%, #a24dd4 100%);">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white rounded-3 p-1 d-inline-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                <img src="{{ asset('images/ykn-emblem.png') }}" alt="Logo YKN" style="width: 36px; height: 36px; object-fit: contain;">
                            </div>
                            <div>
                                <div class="fw-bold" style="font-size: 0.98rem;">{{ __('Yayasan Kebajikan Negara Malaysia') }}</div>
                                <div style="font-size: 0.72rem; color: rgba(255,255,255,0.82);">{{ __('Ditubuhkan pada 5 Disember 1981 · KPWKM') }}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
                    </div>
                </div>

                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 mb-3 border" style="background: #f8f5fd; border-color: #e6dcf5 !important;">
                        <div class="text-uppercase fw-bold text-orchid mb-1" style="font-size: 0.65rem; letter-spacing: 0.06em;">
                            <i class="bi bi-eye me-1"></i> {{ __('Visi YKN') }}
                        </div>
                        <div class="fw-bold" style="font-size: 0.92rem; color: var(--ykn-text);">
                            &ldquo;{{ __('Tabung Kebajikan Terulung di Malaysia.') }}&rdquo;
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mb-3 border" style="background: #ffffff;">
                        <div class="text-uppercase fw-bold text-violet mb-1" style="font-size: 0.65rem; letter-spacing: 0.06em;">
                            <i class="bi bi-bullseye me-1"></i> {{ __('Misi YKN') }}
                        </div>
                        <p class="mb-0 text-muted" style="font-size: 0.79rem; line-height: 1.5;">
                            {{ __('Menjalin kerjasama strategik dengan pelbagai pihak dalam usaha menjana dana kewangan juga perkhidmatan untuk meningkatkan taraf hidup keluarga dan kanak-kanak yang memerlukan melalui perkhidmatan kebajikan, kesihatan dan pendidikan.') }}
                        </p>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="p-2 rounded-3 text-center border h-100" style="background: #faf8fe;">
                                <i class="bi bi-heart-fill text-orchid d-block mb-1"></i>
                                <div class="fw-semibold" style="font-size: 0.73rem;">{{ __('Kebajikan') }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded-3 text-center border h-100" style="background: #faf8fe;">
                                <i class="bi bi-book-half text-violet d-block mb-1"></i>
                                <div class="fw-semibold" style="font-size: 0.73rem;">{{ __('Pendidikan') }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded-3 text-center border h-100" style="background: #faf8fe;">
                                <i class="bi bi-heart-pulse-fill text-danger d-block mb-1"></i>
                                <div class="fw-semibold" style="font-size: 0.73rem;">{{ __('Kesihatan') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="small text-muted" style="font-size: 0.74rem;">
                        <strong class="text-dark">{{ __('Nilai Teras Logo YKN:') }}</strong>
                        {{ __('Ketelusan & Akauntabiliti') }} &middot; {{ __('Keprihatinan & Kehalusan Budi') }} &middot; {{ __('Kerjasama & Saling Membantu') }}.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.documentElement;
            const collapseBtn = document.getElementById('sidebarCollapseBtn');

            if (collapseBtn) {
                collapseBtn.addEventListener('click', function () {
                    const isCollapsed = root.classList.toggle('sidebar-collapsed');
                    try {
                        localStorage.setItem('ykn_sidebar_collapsed', isCollapsed ? '1' : '0');
                    } catch (e) {}
                    setTimeout(() => window.dispatchEvent(new Event('resize')), 240);
                });
            }

            const tabButtons = document.querySelectorAll('.ykn-tab-btn');
            const tabPanes = document.querySelectorAll('.ykn-tab-pane');
            tabButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const target = this.dataset.yknTab;
                    tabButtons.forEach(b => b.classList.toggle('active', b === this));
                    tabPanes.forEach(p => p.classList.toggle('d-none', p.dataset.yknPane !== target));
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
