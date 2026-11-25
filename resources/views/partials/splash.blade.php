@php
    $splashMessage = $message
        ?? (session('show_splash')
            ? __('Selamat datang! Memuatkan papan pemuka...')
            : __('Menyediakan sistem e-Pilot YKN...'));
@endphp

<div id="yknSplashScreen" class="ykn-splash-screen" role="status" aria-live="polite" aria-label="{{ $splashMessage }}">
    <div class="ykn-splash-inner">
        <div class="ykn-splash-art">
            <img
                src="{{ asset('images/splash-loader.jpg') }}"
                alt="e-Pilot YKN"
                class="ykn-splash-img"
                width="300"
                height="235"
                decoding="sync"
            >
            <div class="ykn-splash-shadow" aria-hidden="true"></div>
        </div>

        <div class="ykn-splash-brand">
            <img src="{{ asset('images/ykn-emblem.png') }}" alt="Logo YKN" class="ykn-splash-emblem">
            <span class="ykn-splash-title">e-Pilot <span>YKN</span></span>
        </div>
        <div class="ykn-splash-sub">{{ __('Yayasan Kebajikan Negara Malaysia') }}</div>

        <div class="ykn-splash-loader">
            <div class="ykn-splash-track" aria-hidden="true">
                <div class="ykn-splash-bar" id="yknSplashBar"></div>
            </div>
            <div class="ykn-splash-status" id="yknSplashStatus">
                <span class="ykn-splash-dot" aria-hidden="true"></span>
                <span class="ykn-splash-status-text">{{ $splashMessage }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var root = document.documentElement;
        var splash = document.getElementById('yknSplashScreen');
        var bar = document.getElementById('yknSplashBar');
        var statusText = splash ? splash.querySelector('.ykn-splash-status-text') : null;
        var dismissed = false;

        function hideSplashNow() {
            if (!splash || dismissed) return;
            dismissed = true;

            if (bar) {
                bar.style.transition = 'width 180ms ease-out';
                bar.style.width = '100%';
            }

            setTimeout(function () {
                splash.classList.add('is-hiding');
                setTimeout(function () {
                    root.classList.remove('ykn-splash-active');
                    splash.classList.remove('is-visible', 'is-hiding');
                }, 420);
            }, 150);
        }

        function scheduleDismiss() {
            if (!root.classList.contains('ykn-splash-active') || !splash) return;

            var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var minDisplayMs = prefersReduced ? 260 : 920;
            var shownAt = window.__yknSplashShownAt || Date.now();

            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    if (bar && !dismissed) {
                        bar.style.width = '84%';
                    }
                });
            });

            function finishWhenReady() {
                var elapsed = Date.now() - shownAt;
                var wait = Math.max(0, minDisplayMs - elapsed);
                setTimeout(hideSplashNow, wait);
            }

            if (document.readyState === 'complete') {
                finishWhenReady();
            } else {
                window.addEventListener('load', finishWhenReady, { once: true });
            }

            // Hard safety fallback so the splash can never block the page
            setTimeout(hideSplashNow, 2600);
        }

        window.yknShowSplash = function (customMessage) {
            if (!splash) return;
            dismissed = false;
            if (customMessage && statusText) {
                statusText.textContent = customMessage;
                splash.setAttribute('aria-label', customMessage);
            }
            splash.classList.remove('is-hiding');
            splash.classList.add('is-visible');
            root.classList.add('ykn-splash-active');

            if (bar) {
                bar.style.transition = 'none';
                bar.style.width = '18%';
                void bar.offsetWidth;
                bar.style.transition = 'width 1.1s cubic-bezier(0.22, 1, 0.36, 1)';
                bar.style.width = '88%';
            }
        };

        window.addEventListener('pageshow', function (event) {
            if (event.persisted && splash) {
                dismissed = true;
                root.classList.remove('ykn-splash-active');
                splash.classList.remove('is-visible', 'is-hiding');
            }
        });

        scheduleDismiss();
    })();
</script>
