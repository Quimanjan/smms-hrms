{{--
    Autosave for long forms (panel revision: "if the system goes down, what was typed is kept").
    Any <form data-autosave="name"> keeps its entries in this browser while the person types.
    When they come back after a crash, a lost connection or a closed tab, the entries are put back.
    The saved copy is removed once the form is submitted successfully.
    Never saved: passwords, government ID numbers, file uploads, and hidden fields.
--}}
<script data-user="{{ auth()->id() }}" data-saved="{{ (session('success') || session('status')) && ! $errors->any() ? '1' : '0' }}">
    (() => {
        const me = document.currentScript;
        const user = me.dataset.user || 'guest';
        const PENDING = 'smms.draft.pending';
        const MAX_AGE = 7 * 24 * 60 * 60 * 1000;
        const SKIP = /(^_token$|^_method$|password|(^|\[|_)(sss_number|philhealth_number|pagibig_number|tin)(\]|$))/i;
        const keyOf = (form) => `smms.draft:${user}:${location.pathname}:${form.dataset.autosave}`;
        const store = {
            get: (k) => { try { return JSON.parse(localStorage.getItem(k) || 'null'); } catch (e) { return null; } },
            set: (k, v) => { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} },
            del: (k) => { try { localStorage.removeItem(k); } catch (e) {} },
        };

        // The last submit went through (the page shows a success message): forget that draft.
        try {
            const pending = sessionStorage.getItem(PENDING);
            if (pending && me.dataset.saved === '1') store.del(pending);
            sessionStorage.removeItem(PENDING);
        } catch (e) {}

        const fieldsOf = (form) => [...form.elements].filter((el) =>
            el.name && !SKIP.test(el.name) && !['file', 'hidden', 'submit', 'button', 'password', 'reset'].includes(el.type));

        const read = (form) => {
            const values = {};
            for (const el of fieldsOf(form)) {
                if (el.type === 'checkbox' || el.type === 'radio') {
                    (values[el.name] ??= []);
                    if (el.checked) values[el.name].push(el.value);
                } else {
                    values[el.name] = el.value;
                }
            }
            return values;
        };

        const fire = (el) => {
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        };

        const write = (form, values) => {
            let changed = false;
            for (const el of fieldsOf(form)) {
                if (!(el.name in values)) continue;
                const v = values[el.name];
                if (el.type === 'checkbox' || el.type === 'radio') {
                    const on = Array.isArray(v) && v.includes(el.value);
                    if (el.checked !== on) { el.checked = on; fire(el); changed = true; }
                } else if (typeof v === 'string' && v !== '' && el.value !== v) {
                    el.value = v; fire(el); changed = true;
                }
            }
            return changed;
        };

        const notice = (form, key) => {
            const box = document.createElement('div');
            box.className = 'autosave-note';
            box.setAttribute('role', 'status');
            box.innerHTML = '<span>Your unsaved entries from earlier were put back.</span>';
            form.prepend(box);
            const fold = form.closest('details');
            if (fold) fold.open = true;
        };

        const setup = (form) => {
            if (form.dataset.autosaveReady) return;
            form.dataset.autosaveReady = '1';
            const key = keyOf(form);
            const saved = store.get(key);
            if (saved && Date.now() - saved.t < MAX_AGE) {
                if (write(form, saved.v)) notice(form, key);
            } else if (saved) {
                store.del(key);
            }

            let timer;
            const save = () => { clearTimeout(timer); timer = setTimeout(() => store.set(key, { t: Date.now(), v: read(form) }), 300); };
            form.addEventListener('input', save);
            form.addEventListener('change', save);
            form.addEventListener('submit', () => {
                store.set(key, { t: Date.now(), v: read(form) });
                try { sessionStorage.setItem(PENDING, key); } catch (e) {}
            });
        };

        // Run after Alpine has drawn the page, so restored values reach its x-model fields.
        const start = () => document.querySelectorAll('form[data-autosave]').forEach(setup);
        if (window.Alpine) setTimeout(start, 0);
        else document.addEventListener('alpine:initialized', () => setTimeout(start, 0), { once: true });
        addEventListener('load', () => setTimeout(start, 400), { once: true });
    })();
</script>
