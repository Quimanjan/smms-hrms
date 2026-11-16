{{-- Smooth page changes on every page (portal, homepage and sign-in), so the browser never has a half-started change to cancel. --}}
<style>
    @view-transition { navigation: auto; }
    @media (prefers-reduced-motion: reduce) { @view-transition { navigation: none; } }
</style>
<script>
    // A page transition the browser cancels (for example between the homepage and the portal) is normal; don't report it as an error.
    (() => {
        const quiet = (e) => { const t = e.viewTransition; if (t) [t.ready, t.finished, t.updateCallbackDone].forEach((p) => p && p.catch(() => {})); };
        addEventListener('pageswap', quiet);
        addEventListener('pagereveal', quiet);
    })();
</script>
