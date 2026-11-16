{{--
    Search boxes update the list on every character (client revision).
    A form marked data-live-search is fetched in the background as you type; the
    region marked data-live-region is swapped with the new one, and the cursor stays
    in the search box. Pressing Enter still works as before.
--}}
<script>
(() => {
    let timer = null, ctrl = null;
    const fields = 'input[type="search"], input[name="q"], input[name="search"], input[name="employee"]';

    document.addEventListener('input', (e) => {
        const input = e.target.closest?.(fields);
        const form = input?.closest('form[data-live-search]');
        if (!form) return;
        clearTimeout(timer);
        timer = setTimeout(() => run(form, input), 200);
    });

    async function run(form, input) {
        const region = form.closest('[data-live-region]');
        if (!region) return;
        const params = new URLSearchParams(new FormData(form));
        params.delete('page');
        for (const [k, v] of [...params]) if (v === '') params.delete(k);
        const url = (form.getAttribute('action') || location.pathname).split('?')[0] + (params.toString() ? '?' + params : '');

        ctrl?.abort();
        ctrl = new AbortController();
        let html;
        try {
            const res = await fetch(url, { credentials: 'same-origin', signal: ctrl.signal, headers: { 'X-Live-Search': '1' } });
            if (!res.ok) return;
            html = await res.text();
        } catch (_) { return; }

        const doc = new DOMParser().parseFromString(html, 'text/html');
        const name = region.getAttribute('data-live-region');
        const fresh = doc.querySelector(`[data-live-region="${name}"]`);
        if (!fresh) return;

        // Keep what was typed while the request was running, and the cursor position.
        const key = input.name || input.id;
        const typed = input.value, start = input.selectionStart, end = input.selectionEnd;
        region.replaceWith(fresh);
        history.replaceState(null, '', url);
        const again = fresh.querySelector(input.name ? `[name="${key}"]` : `#${key}`);
        if (again) {
            again.value = typed;
            again.focus();
            try { again.setSelectionRange(start, end); } catch (_) {}
        }
    }
})();
</script>
