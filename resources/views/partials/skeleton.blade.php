@php
    $activeVariant = in_array($variant ?? 'dashboard', ['dashboard', 'table', 'form', 'calendar'], true)
        ? $variant
        : 'dashboard';
@endphp

<div id="yknPageSkeleton" class="ykn-page-skeleton" data-current-variant="{{ $activeVariant }}" aria-hidden="true">
    {{-- Variant 1: Dashboard (KPI Cards + Charts + Recent Table) --}}
    <div class="ykn-skel-layout {{ $activeVariant === 'dashboard' ? 'is-active' : '' }}" data-skel-variant="dashboard">
        <div class="row g-3 mb-4">
            @for($i = 0; $i < 4; $i++)
                <div class="col-6 col-xl-3">
                    <div class="ykn-skel-card h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="ykn-skel ykn-skel-line" style="width: 58%; height: 11px;"></span>
                            <span class="ykn-skel ykn-skel-circle" style="width: 32px; height: 32px;"></span>
                        </div>
                        <span class="ykn-skel ykn-skel-line mb-2" style="width: 42%; height: 26px; border-radius: 8px;"></span>
                        <span class="ykn-skel ykn-skel-pill" style="width: 68%; height: 18px;"></span>
                    </div>
                </div>
            @endfor
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-xl-8">
                <div class="ykn-skel-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div style="width: 48%;">
                            <span class="ykn-skel ykn-skel-line mb-2" style="width: 65%; height: 14px;"></span>
                            <span class="ykn-skel ykn-skel-line" style="width: 90%; height: 10px;"></span>
                        </div>
                        <span class="ykn-skel ykn-skel-pill" style="width: 96px; height: 26px;"></span>
                    </div>
                    <div class="ykn-skel-bars">
                        @foreach([54, 82, 66, 94, 48, 74] as $barH)
                            <div class="ykn-skel-bar-col">
                                <div class="d-flex align-items-end gap-1 h-100">
                                    <span class="ykn-skel" style="width: 14px; height: {{ $barH }}%; border-radius: 99px 99px 4px 4px;"></span>
                                    <span class="ykn-skel" style="width: 14px; height: {{ max(24, $barH - 22) }}%; border-radius: 99px 99px 4px 4px;"></span>
                                </div>
                                <span class="ykn-skel ykn-skel-line mt-2" style="width: 28px; height: 9px;"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="ykn-skel-card h-100">
                    <span class="ykn-skel ykn-skel-line mb-3" style="width: 56%; height: 14px;"></span>
                    <div class="d-flex align-items-center gap-3 my-3">
                        <span class="ykn-skel ykn-skel-donut"></span>
                        <div class="flex-grow-1 d-flex flex-column gap-2">
                            <span class="ykn-skel ykn-skel-line" style="width: 85%; height: 11px;"></span>
                            <span class="ykn-skel ykn-skel-line" style="width: 72%; height: 11px;"></span>
                            <span class="ykn-skel ykn-skel-line" style="width: 64%; height: 11px;"></span>
                        </div>
                    </div>
                    <span class="ykn-skel ykn-skel-pill mt-2" style="width: 100%; height: 32px;"></span>
                </div>
            </div>
        </div>

        <div class="ykn-skel-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="ykn-skel ykn-skel-line" style="width: 180px; height: 15px;"></span>
                <span class="ykn-skel ykn-skel-pill" style="width: 110px; height: 28px;"></span>
            </div>
            @for($r = 0; $r < 4; $r++)
                <div class="ykn-skel-row">
                    <span class="ykn-skel ykn-skel-circle" style="width: 34px; height: 34px;"></span>
                    <div class="flex-grow-1">
                        <span class="ykn-skel ykn-skel-line mb-2" style="width: {{ 44 + ($r * 7) % 24 }}%; height: 12px;"></span>
                        <span class="ykn-skel ykn-skel-line" style="width: {{ 28 + ($r * 9) % 20 }}%; height: 10px;"></span>
                    </div>
                    <span class="ykn-skel ykn-skel-pill d-none d-md-inline-block" style="width: 88px; height: 22px;"></span>
                    <span class="ykn-skel ykn-skel-pill" style="width: 68px; height: 26px;"></span>
                </div>
            @endfor
        </div>
    </div>

    {{-- Variant 2: Table / Listing (Bookings, Vehicles, Users) --}}
    <div class="ykn-skel-layout {{ $activeVariant === 'table' ? 'is-active' : '' }}" data-skel-variant="table">
        <div class="ykn-skel-card mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4"><span class="ykn-skel ykn-skel-input"></span></div>
                <div class="col-6 col-md-3"><span class="ykn-skel ykn-skel-input"></span></div>
                <div class="col-6 col-md-2"><span class="ykn-skel ykn-skel-input"></span></div>
                <div class="col-12 col-md-3 d-flex justify-content-md-end gap-2">
                    <span class="ykn-skel ykn-skel-pill" style="width: 84px; height: 34px;"></span>
                    <span class="ykn-skel ykn-skel-pill" style="width: 120px; height: 34px;"></span>
                </div>
            </div>
        </div>

        <div class="ykn-skel-card">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-2 border-bottom" style="border-color: #f0ecf8 !important;">
                <span class="ykn-skel ykn-skel-line" style="width: 160px; height: 13px;"></span>
                <span class="ykn-skel ykn-skel-pill" style="width: 90px; height: 22px;"></span>
            </div>
            @for($r = 0; $r < 6; $r++)
                <div class="ykn-skel-row">
                    <span class="ykn-skel ykn-skel-circle" style="width: 34px; height: 34px;"></span>
                    <div class="flex-grow-1">
                        <span class="ykn-skel ykn-skel-line mb-2" style="width: {{ 40 + ($r * 9) % 30 }}%; height: 12px;"></span>
                        <span class="ykn-skel ykn-skel-line" style="width: {{ 26 + ($r * 7) % 24 }}%; height: 10px;"></span>
                    </div>
                    <span class="ykn-skel ykn-skel-line d-none d-lg-inline-block" style="width: 110px; height: 12px;"></span>
                    <span class="ykn-skel ykn-skel-pill d-none d-sm-inline-block" style="width: 86px; height: 22px;"></span>
                    <span class="ykn-skel ykn-skel-pill" style="width: 72px; height: 28px;"></span>
                </div>
            @endfor
        </div>
    </div>

    {{-- Variant 3: Form / Detail (Create Booking, Edit Vehicle/User, Booking Show) --}}
    <div class="ykn-skel-layout {{ $activeVariant === 'form' ? 'is-active' : '' }}" data-skel-variant="form">
        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="ykn-skel-card">
                    <span class="ykn-skel ykn-skel-line mb-2" style="width: 42%; height: 16px;"></span>
                    <span class="ykn-skel ykn-skel-line mb-4" style="width: 68%; height: 11px;"></span>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4"><span class="ykn-skel" style="display: block; height: 68px; border-radius: 14px;"></span></div>
                        <div class="col-12 col-md-4"><span class="ykn-skel" style="display: block; height: 68px; border-radius: 14px;"></span></div>
                        <div class="col-12 col-md-4"><span class="ykn-skel" style="display: block; height: 68px; border-radius: 14px;"></span></div>
                    </div>

                    <div class="row g-3 mb-3">
                        @for($f = 0; $f < 4; $f++)
                            <div class="col-12 col-md-6">
                                <span class="ykn-skel ykn-skel-line mb-2" style="width: 44%; height: 11px;"></span>
                                <span class="ykn-skel ykn-skel-input" style="height: 38px;"></span>
                            </div>
                        @endfor
                    </div>

                    <span class="ykn-skel ykn-skel-line mb-2" style="width: 32%; height: 11px;"></span>
                    <span class="ykn-skel mb-4" style="display: block; width: 100%; height: 84px; border-radius: 12px;"></span>

                    <div class="d-flex justify-content-end gap-2">
                        <span class="ykn-skel ykn-skel-pill" style="width: 96px; height: 36px;"></span>
                        <span class="ykn-skel ykn-skel-pill" style="width: 148px; height: 36px;"></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="ykn-skel-card mb-3">
                    <span class="ykn-skel ykn-skel-line mb-3" style="width: 58%; height: 14px;"></span>
                    <span class="ykn-skel ykn-skel-line mb-2" style="width: 100%; height: 11px;"></span>
                    <span class="ykn-skel ykn-skel-line mb-2" style="width: 88%; height: 11px;"></span>
                    <span class="ykn-skel ykn-skel-line mb-3" style="width: 74%; height: 11px;"></span>
                    <span class="ykn-skel" style="display: block; height: 74px; border-radius: 14px;"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Variant 4: Calendar (Snapshot Cards + Filters + 7-Column Grid) --}}
    <div class="ykn-skel-layout {{ $activeVariant === 'calendar' ? 'is-active' : '' }}" data-skel-variant="calendar">
        <div class="row g-3 mb-4">
            @for($s = 0; $s < 2; $s++)
                <div class="col-12 col-lg-6">
                    <div class="ykn-skel-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="ykn-skel ykn-skel-line" style="width: 44%; height: 12px;"></span>
                            <span class="ykn-skel ykn-skel-pill" style="width: 78px; height: 20px;"></span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="ykn-skel ykn-skel-pill" style="width: 112px; height: 24px;"></span>
                            <span class="ykn-skel ykn-skel-pill" style="width: 128px; height: 24px;"></span>
                            <span class="ykn-skel ykn-skel-pill" style="width: 104px; height: 24px;"></span>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <div class="ykn-skel-card mb-4">
            <div class="row g-2">
                <div class="col-12 col-md-3"><span class="ykn-skel ykn-skel-input"></span></div>
                <div class="col-12 col-md-3"><span class="ykn-skel ykn-skel-input"></span></div>
                <div class="col-12 col-md-3"><span class="ykn-skel ykn-skel-input"></span></div>
                <div class="col-12 col-md-3 d-flex justify-content-md-end gap-2">
                    <span class="ykn-skel ykn-skel-pill" style="width: 74px; height: 34px;"></span>
                    <span class="ykn-skel ykn-skel-pill" style="width: 104px; height: 34px;"></span>
                </div>
            </div>
        </div>

        <div class="ykn-skel-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="ykn-skel ykn-skel-pill" style="width: 110px; height: 30px;"></span>
                <span class="ykn-skel ykn-skel-line" style="width: 140px; height: 18px;"></span>
                <span class="ykn-skel ykn-skel-pill" style="width: 168px; height: 30px;"></span>
            </div>
            <div class="ykn-skel-cal-grid">
                @for($c = 0; $c < 28; $c++)
                    <div class="ykn-skel-cal-cell">
                        <span class="ykn-skel ykn-skel-line mb-2" style="width: 22px; height: 10px;"></span>
                        @if(in_array($c, [2, 5, 9, 12, 16, 19, 23], true))
                            <span class="ykn-skel ykn-skel-pill" style="width: 86%; height: 16px;"></span>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var root = document.documentElement;
        var skeleton = document.getElementById('yknPageSkeleton');
        var progress = document.getElementById('yknTopProgress');
        var currentVariant = skeleton ? (skeleton.dataset.currentVariant || 'dashboard') : 'dashboard';
        var revealed = false;

        function getContentEl() {
            return document.getElementById('yknPageContent');
        }

        function setSkeletonVariant(variant) {
            if (!skeleton) return;
            var panes = skeleton.querySelectorAll('.ykn-skel-layout');
            var matched = false;
            panes.forEach(function (pane) {
                var active = pane.dataset.skelVariant === variant;
                pane.classList.toggle('is-active', active);
                if (active) matched = true;
            });
            if (!matched && panes.length) {
                panes[0].classList.add('is-active');
            }
        }

        function inferVariantFromUrl(url) {
            var path = (url.pathname || '').toLowerCase();
            if (path.indexOf('/locale/') === 0) return currentVariant;
            if (path.indexOf('/calendar') !== -1) return 'calendar';
            if (path.indexOf('/create') !== -1 || path.indexOf('/edit') !== -1 || /\/bookings\/\d+/.test(path)) return 'form';
            if (path.indexOf('/bookings') !== -1 || path.indexOf('/vehicles') !== -1 || path.indexOf('/users') !== -1) return 'table';
            return 'dashboard';
        }

        function startTopProgress() {
            if (!progress) return;
            progress.classList.remove('is-done');
            progress.classList.add('is-loading');
            progress.style.width = '24%';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    if (progress.classList.contains('is-loading')) {
                        progress.style.width = '78%';
                    }
                });
            });
        }

        function finishTopProgress() {
            if (!progress) return;
            progress.style.width = '100%';
            progress.classList.add('is-done');
            setTimeout(function () {
                progress.classList.remove('is-loading', 'is-done');
                progress.style.width = '0%';
            }, 260);
        }

        function revealPageContent() {
            if (revealed) return;
            revealed = true;
            root.classList.remove('ykn-skeleton-active');
            var content = getContentEl();
            if (content) {
                content.classList.add('is-revealed');
            }
            finishTopProgress();
            requestAnimationFrame(function () {
                window.dispatchEvent(new Event('resize'));
            });
        }

        function activateOutgoingSkeleton(targetVariant) {
            if (root.classList.contains('ykn-splash-active')) return;
            try {
                sessionStorage.setItem('ykn_nav_skeleton_ts', String(Date.now()));
            } catch (e) {}

            setSkeletonVariant(targetVariant || currentVariant);
            var content = getContentEl();
            if (content) {
                content.classList.remove('is-revealed');
            }
            root.classList.add('ykn-skeleton-active');
            startTopProgress();

            // Close mobile offcanvas if open so user sees skeleton immediately
            var mobileSidebar = document.getElementById('mobileSidebar');
            if (mobileSidebar && window.bootstrap && bootstrap.Offcanvas) {
                var offcanvas = bootstrap.Offcanvas.getInstance(mobileSidebar);
                if (offcanvas) offcanvas.hide();
            }
        }

        // Incoming page reveal (only when navigated internally & splash is not active)
        if (root.classList.contains('ykn-skeleton-active') && !root.classList.contains('ykn-splash-active')) {
            var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var minShimmerMs = prefersReduced ? 0 : 250;
            var shownAt = window.__yknSkeletonShownAt || Date.now();
            startTopProgress();

            function finishWhenReady() {
                var elapsed = Date.now() - shownAt;
                var wait = Math.max(0, minShimmerMs - elapsed);
                setTimeout(revealPageContent, wait);
            }

            if (document.readyState === 'interactive' || document.readyState === 'complete') {
                finishWhenReady();
            } else {
                document.addEventListener('DOMContentLoaded', finishWhenReady, { once: true });
            }

            setTimeout(revealPageContent, 1800);
        }

        // Outgoing internal navigation links
        document.addEventListener('click', function (event) {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }
            var link = event.target.closest ? event.target.closest('a[href]') : null;
            if (!link) return;

            if (
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-skeleton') ||
                link.hasAttribute('data-bs-toggle') ||
                (link.target && link.target !== '_self')
            ) {
                return;
            }

            var rawHref = link.getAttribute('href') || '';
            if (!rawHref || rawHref.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/i.test(rawHref)) {
                return;
            }

            var url;
            try {
                url = new URL(link.href, window.location.origin);
            } catch (e) {
                return;
            }

            if (url.origin !== window.location.origin) return;
            if (url.pathname.indexOf('/pdf/') !== -1) return;
            if (url.href === window.location.href) return;

            var targetVariant = link.dataset.skeleton || inferVariantFromUrl(url);
            activateOutgoingSkeleton(targetVariant);
        });

        // GET filter/search forms show table skeleton while refreshing results
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form || form.hasAttribute('data-no-skeleton')) return;
            if ((form.method || 'get').toLowerCase() === 'get') {
                activateOutgoingSkeleton(form.dataset.skeleton || currentVariant);
            }
        });

        // Clean up cleanly when returning via browser Back/Forward cache
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                revealed = false;
                setSkeletonVariant(currentVariant);
                root.classList.remove('ykn-skeleton-active');
                if (progress) {
                    progress.classList.remove('is-loading', 'is-done');
                    progress.style.width = '0%';
                }
            }
        });
    })();
</script>
