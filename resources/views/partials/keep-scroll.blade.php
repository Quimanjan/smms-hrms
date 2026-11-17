{{-- After Approve, Reject, Save and similar buttons the page reloads. This keeps the
     page and any open window at the same scroll position instead of jumping to the top. --}}
<script>
(() => {
    const KEY = 'hrms-keep-scroll';
    const pathOf = (el) => {
        const parts = [];
        while (el && el !== document.body) {
            parts.unshift([...el.parentNode.children].indexOf(el));
            el = el.parentNode;
        }
        return parts.join('.');
    };
    const byPath = (path) => path.split('.').reduce((el, i) => el && el.children[+i], document.body);

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if ((form.method || '').toLowerCase() !== 'post' || form.hasAttribute('data-keep-scroll-off')) return;
        const boxes = [];
        for (let el = form.parentElement; el && el !== document.body; el = el.parentElement) {
            if (el.scrollTop > 0) boxes.push([pathOf(el), el.scrollTop]);
        }
        try { sessionStorage.setItem(KEY, JSON.stringify({ page: location.pathname, y: window.scrollY, boxes, at: Date.now() })); } catch (_) {}
    }, true);

    const restore = () => {
        let saved;
        try { saved = JSON.parse(sessionStorage.getItem(KEY) || 'null'); sessionStorage.removeItem(KEY); } catch (_) { return; }
        if (!saved || saved.page !== location.pathname || Date.now() - saved.at > 60000 || location.hash) return;
        const apply = () => {
            window.scrollTo(0, saved.y);
            saved.boxes.forEach(([path, top]) => { const el = byPath(path); if (el) el.scrollTop = top; });
        };
        apply();
        setTimeout(apply, 120);
        setTimeout(apply, 400);
    };
    if (document.readyState === 'complete') restore(); else window.addEventListener('load', restore);
})();
</script>
