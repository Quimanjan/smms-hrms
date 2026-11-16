<script>
    // A page transition the browser cancels (for example between the homepage and the portal) is normal; don't report it as an error.
    (() => {
        const quiet = (e) => { const t = e.viewTransition; if (t) [t.ready, t.finished, t.updateCallbackDone].forEach((p) => p && p.catch(() => {})); };
        addEventListener('pageswap', quiet);
        addEventListener('pagereveal', quiet);
    })();
</script>
