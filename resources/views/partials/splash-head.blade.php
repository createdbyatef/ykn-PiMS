<link rel="preload" as="image" href="{{ asset('images/splash-loader.jpg') }}">
<noscript>
    <style>
        .ykn-splash-screen { display: none !important; }
    </style>
</noscript>
<script>
    (function () {
        try {
            var force = {{ !empty($forceSplash) ? 'true' : 'false' }};
            var skip = {{ !empty($skipSplash) ? 'true' : 'false' }};
            if (skip) return;

            var navEntries = window.performance && performance.getEntriesByType
                ? performance.getEntriesByType('navigation')
                : [];
            var navType = navEntries.length ? navEntries[0].type : '';
            var firstVisit = !sessionStorage.getItem('ykn_splash_seen');

            if (force || firstVisit || navType === 'reload') {
                document.documentElement.classList.add('ykn-splash-active');
                window.__yknSplashShownAt = Date.now();
                sessionStorage.setItem('ykn_splash_seen', '1');
            }
        } catch (e) {}
    })();
</script>
