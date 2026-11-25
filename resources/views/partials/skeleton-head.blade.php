<noscript>
    <style>
        .ykn-page-skeleton,
        .ykn-top-progress { display: none !important; }
    </style>
</noscript>
<script>
    (function () {
        try {
            if (document.documentElement.classList.contains('ykn-splash-active')) {
                sessionStorage.removeItem('ykn_nav_skeleton_ts');
                return;
            }

            var rawTs = sessionStorage.getItem('ykn_nav_skeleton_ts');
            sessionStorage.removeItem('ykn_nav_skeleton_ts');

            if (rawTs && (Date.now() - parseInt(rawTs, 10)) < 4500) {
                document.documentElement.classList.add('ykn-skeleton-active');
                window.__yknSkeletonShownAt = Date.now();
            }
        } catch (e) {}
    })();
</script>
