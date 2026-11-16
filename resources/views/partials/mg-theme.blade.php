{{-- Shared look for the public pages (top bar, buttons, page header band, footer). --}}
@verbatim
<style>
:root {
    --mg-oxblood: #4a0a12;
    --mg-oxblood-deep: #33060c;
    --mg-crimson: #b22222;
    --mg-gold: #c9a24b;
    --mg-gold-soft: #e6cf9a;
    --mg-ink: #21171a;
    --mg-stone: #6d6164;
    --mg-mist: #f6f2f1;
    --mg-line: #e6ddda;
    --mg-display: 'Marcellus', 'Times New Roman', serif;
    --mg-body: 'Instrument Sans', system-ui, 'Segoe UI', Roboto, Arial, sans-serif;
    --mg-wrap: 1180px;
    --mg-bar: 72px;
}

*, *::before, *::after { box-sizing: border-box; }

html { scroll-padding-top: var(--mg-bar); }

html, body {
    margin: 0;
    font-family: var(--mg-body);
    color: var(--mg-ink);
    background: #fff;
}

body {
    max-width: none;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding-top: var(--mg-bar);
    font-size: 17px;
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

.mg-main { flex: 1; }

.mg-wrap { width: min(100% - 48px, var(--mg-wrap)); margin-inline: auto; }

.mg-bar :focus-visible,
.mg-main :focus-visible,
.mg-foot :focus-visible {
    outline: 2px solid var(--mg-gold);
    outline-offset: 3px;
    border-radius: 4px;
}

/* Top bar */
.mg-bar {
    position: fixed;
    inset: 0 0 auto 0;
    z-index: 50;
    height: var(--mg-bar);
    background: rgba(255, 255, 255, 0.96);
    -webkit-backdrop-filter: saturate(160%) blur(10px);
    backdrop-filter: saturate(160%) blur(10px);
    border-bottom: 1px solid var(--mg-line);
}

.mg-bar-inner { height: 100%; display: flex; align-items: center; justify-content: space-between; gap: 24px; }

.mg-brand { display: flex; align-items: center; gap: 12px; min-width: 0; color: var(--mg-ink); text-decoration: none; }
.mg-brand img { width: 44px; height: 44px; flex: none; }
.mg-brand span {
    font-family: var(--mg-display);
    font-size: 1.22rem;
    letter-spacing: 0.01em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mg-nav { display: flex; align-items: center; gap: 6px; }

.mg-nav a.mg-nav-link {
    position: relative;
    padding: 9px 14px;
    border-radius: 999px;
    color: var(--mg-ink);
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.mg-nav a.mg-nav-link:hover { background: var(--mg-mist); color: var(--mg-crimson); }
.mg-nav a.mg-nav-link.is-current { color: var(--mg-crimson); }
.mg-nav a.mg-nav-link.is-current::after {
    content: '';
    position: absolute;
    left: 14px;
    right: 14px;
    bottom: 4px;
    height: 2px;
    border-radius: 2px;
    background: var(--mg-crimson);
}

/* Buttons */
.mg-btn {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 0 24px;
    border: 1px solid transparent;
    border-radius: 999px;
    font: 600 0.98rem/1 var(--mg-body);
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}

.mg-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, transparent 35%, rgba(255, 255, 255, 0.5) 50%, transparent 65%);
    transform: translateX(-120%);
    pointer-events: none;
}

.mg-btn-crimson { background: var(--mg-crimson); color: #fff; }
.mg-btn-crimson:hover { background: var(--mg-oxblood); }
.mg-btn-gold { background: var(--mg-gold); color: var(--mg-oxblood-deep); }
.mg-btn-gold:hover { background: var(--mg-gold-soft); }
.mg-btn-outline { border-color: var(--mg-crimson); color: var(--mg-crimson); background: #fff; }
.mg-btn-outline:hover { background: var(--mg-crimson); color: #fff; }
.mg-btn-ghost { border-color: rgba(255, 255, 255, 0.45); color: #fff; background: transparent; }
.mg-btn-ghost:hover { border-color: #fff; background: rgba(255, 255, 255, 0.08); }

.mg-nav .mg-btn { min-height: 40px; padding: 0 20px; margin-left: 8px; }

.mg-bar .menu-toggle {
    display: none;
    width: 44px;
    height: 44px;
    padding: 0;
    border: 1px solid var(--mg-line);
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.mg-bar .menu-toggle span { display: block; width: 20px; height: 2px; margin: 0; background: var(--mg-ink); border-radius: 2px; transition: transform 0.2s ease, opacity 0.2s ease; }
.mg-bar .menu-toggle.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.mg-bar .menu-toggle.active span:nth-child(2) { opacity: 0; }
.mg-bar .menu-toggle.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* Page header band */
.mg-pagehead {
    position: relative;
    overflow: hidden;
    color: #fff;
    background:
        radial-gradient(700px 360px at 82% 20%, rgba(165, 18, 30, 0.55), transparent 70%),
        linear-gradient(180deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
}

.mg-pagehead-inner { position: relative; padding-block: clamp(40px, 6vw, 72px); }

.mg-crumb { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; color: var(--mg-gold-soft); font-size: 0.95rem; font-weight: 500; text-decoration: none; }
.mg-crumb svg { width: 18px; height: 18px; transition: transform 0.2s ease; }
.mg-crumb:hover svg { transform: translateX(-4px); }

.mg-pagehead h1 {
    max-width: 20ch;
    margin: 0;
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: clamp(2.2rem, 4.6vw, 3.6rem);
    line-height: 1.08;
    text-wrap: balance;
}

.mg-pagehead p { max-width: 56ch; margin: 16px 0 0; color: rgba(255, 255, 255, 0.82); font-size: 1.08rem; }

.mg-h2 { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: clamp(1.5rem, 2.4vw, 1.9rem); line-height: 1.15; color: var(--mg-oxblood); }

.mg-pill {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    border: 1px solid rgba(230, 207, 154, 0.55);
    border-radius: 999px;
    color: var(--mg-gold-soft);
    font-size: 0.82rem;
    font-weight: 600;
}

/* Footer */
.mg-foot { background: var(--mg-oxblood-deep); color: rgba(255, 255, 255, 0.78); border-top: 1px solid rgba(201, 162, 75, 0.35); font-size: 0.97rem; }
.mg-foot-grid { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 40px 64px; padding-block: 56px 40px; }
.mg-foot-brand { display: flex; gap: 16px; align-items: flex-start; }
.mg-foot-brand img { width: 60px; height: 60px; flex: none; padding: 4px; border-radius: 14px; background: #fff; }
.mg-foot-brand strong { display: block; font-family: var(--mg-display); font-weight: 400; font-size: 1.3rem; color: #fff; }
.mg-foot-brand span { display: block; margin-top: 4px; }
.mg-contact { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(2, max-content); gap: 6px 48px; }
.mg-contact a { display: flex; align-items: center; gap: 12px; min-height: 44px; color: inherit; text-decoration: none; overflow-wrap: anywhere; }
.mg-contact a:hover { color: #fff; }
.mg-contact svg { width: 20px; height: 20px; flex: none; color: var(--mg-gold); transition: transform 0.25s ease; }
.mg-contact a:hover svg { transform: translateY(-3px) scale(1.12); }
.mg-foot-legal { padding-block: 20px 28px; border-top: 1px solid rgba(255, 255, 255, 0.12); color: rgba(255, 255, 255, 0.55); font-size: 0.88rem; }

/* Modals: match the page's type and color */
.modal-card, .login-modal-content, .login-modal-content input, .login-modal-content button, .modal-card input, .modal-card button { font-family: var(--mg-body); }
.login-modal-content { border-radius: 18px; }
.login-header h2, .modal-card h2 { font-family: var(--mg-display); font-weight: 400; color: var(--mg-oxblood); }
.login-submit { background: var(--mg-crimson); border-radius: 999px; }
.login-submit:hover { background: var(--mg-oxblood); box-shadow: none; }

/* Motion */
@media (prefers-reduced-motion: no-preference) {
    .mg-btn:hover::after { transform: translateX(120%); transition: transform 0.7s ease; }

    .mg-pagehead-inner > * { animation: mg-rise 0.7s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
    .mg-pagehead-inner > :nth-child(2) { animation-delay: 0.08s; }
    .mg-pagehead-inner > :nth-child(3) { animation-delay: 0.16s; }
    .mg-pagehead-inner > :nth-child(4) { animation-delay: 0.24s; }

    .mg-js [data-reveal] {
        opacity: 0;
        transform: translateY(26px);
        transition: opacity 0.8s cubic-bezier(0.2, 0.7, 0.2, 1), transform 0.8s cubic-bezier(0.2, 0.7, 0.2, 1);
        transition-delay: calc(var(--i, 0) * 90ms);
    }
    .mg-js [data-reveal].is-in { opacity: 1; transform: none; }
}

@keyframes mg-rise {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: none; }
}

@media (max-width: 900px) {
    .mg-foot-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    :root { --mg-bar: 64px; }
    body { font-size: 16px; }
    .mg-wrap { width: min(100% - 36px, var(--mg-wrap)); }
    .mg-brand img { width: 38px; height: 38px; }
    .mg-brand span { font-size: 1.02rem; }
    .mg-bar .menu-toggle { display: flex; }

    .mg-nav {
        position: absolute;
        inset: var(--mg-bar) 0 auto 0;
        display: none;
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
        padding: 12px 18px 20px;
        background: #fff;
        border-bottom: 1px solid var(--mg-line);
        box-shadow: 0 24px 40px -24px rgba(33, 23, 26, 0.35);
    }

    .mg-nav.open { display: flex; }
    .mg-nav a.mg-nav-link { padding: 13px 12px; border-radius: 10px; }
    .mg-nav .mg-btn { min-height: 48px; margin: 8px 0 0; }
    .mg-contact { grid-template-columns: 1fr; }
}
</style>
@endverbatim

{{-- Reveal-on-scroll for elements marked data-reveal. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var seen = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-in');
            seen.unobserve(entry.target);
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('[data-reveal]').forEach(function (el) { seen.observe(el); });
});
</script>
{{-- Sign up / log in windows: a slim scrollbar that stays inside the rounded corners --}}
<style>
/* Firefox only: Chrome ignores the ::-webkit-scrollbar rules below when these are set */
@supports not selector(::-webkit-scrollbar) {
    #signup-modal .modal-card, .login-modal-content, .modal-card { scrollbar-width: thin; scrollbar-color: #d6c4c1 transparent; }
}
#signup-modal .modal-card::-webkit-scrollbar, .login-modal-content::-webkit-scrollbar, .modal-card::-webkit-scrollbar { width: 10px; }
#signup-modal .modal-card::-webkit-scrollbar-button, .login-modal-content::-webkit-scrollbar-button, .modal-card::-webkit-scrollbar-button { display: none; height: 0; }
#signup-modal .modal-card::-webkit-scrollbar-track, .login-modal-content::-webkit-scrollbar-track, .modal-card::-webkit-scrollbar-track { margin-block: 18px; background: transparent; }
#signup-modal .modal-card::-webkit-scrollbar-thumb, .login-modal-content::-webkit-scrollbar-thumb, .modal-card::-webkit-scrollbar-thumb { border: 3px solid transparent; border-radius: 999px; background: #d6c4c1 padding-box; }
#signup-modal .modal-card::-webkit-scrollbar-thumb:hover, .modal-card::-webkit-scrollbar-thumb:hover { background: #b89c98 padding-box; }

/* ---------- Fonts: every control uses the page font (client revision) ---------- */
button, input, select, textarea, optgroup { font-family: inherit; }
.font-sans { font-family: var(--mg-body); }

/* ---------- One brand red everywhere (client revision) ---------- */
:root { --brand-red: #b22222; --brand-red-dark: #8f1c1c; --brand-green: #15803d; --brand-green-dark: #166534; }
.bg-red-500, .bg-red-600, .bg-red-700, .bg-red-800 { background-color: var(--brand-red) !important; }
.hover\:bg-red-500:hover, .hover\:bg-red-600:hover, .hover\:bg-red-700:hover, .hover\:bg-red-800:hover, .active\:bg-red-700:active, .active\:bg-red-900:active { background-color: var(--brand-red-dark) !important; }
.text-red-500, .text-red-600, .text-red-700 { color: var(--brand-red) !important; }
.hover\:text-red-700:hover, .hover\:text-red-800:hover, .text-\[\#8f1c1c\], .hover\:text-\[\#8f1c1c\]:hover { color: var(--brand-red-dark) !important; }
.border-red-500, .border-red-600, .border-red-700, .focus\:border-red-500:focus, .focus\:border-red-600:focus { border-color: var(--brand-red) !important; }
.border-l-\[\#b22222\] { border-left-color: var(--brand-red); }
.bg-\[\#8f1c1c\] { background-color: var(--brand-red-dark); }
.border-\[\#8f1c1c\] { border-color: var(--brand-red-dark); }
.hover\:border-\[\#8f1c1c\]\/30:hover { border-color: rgba(143, 28, 28, .3); }
.ring-red-700, .focus-visible\:ring-red-700:focus-visible, .focus\:ring-red-700:focus { --tw-ring-color: var(--brand-red) !important; }

/* ---------- Decision buttons: one look, grouped together (client revision) ---------- */
.hb-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 10px; }
.hb { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 0 18px;
      border: 1px solid transparent; border-radius: 10px; font-family: inherit; font-size: 0.9rem; font-weight: 700; line-height: 1;
      white-space: nowrap; cursor: pointer; text-decoration: none;
      transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease, transform .15s ease; }
.hb i { font-size: 0.85em; }
.hb:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(15, 23, 42, .12); }
.hb:focus-visible { outline: 2px solid var(--brand-red); outline-offset: 2px; }
.hb:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }
.hb--approve { background: var(--brand-green); border-color: var(--brand-green); color: #fff; }
.hb--approve:hover:not(:disabled) { background: var(--brand-green-dark); border-color: var(--brand-green-dark); }
.hb--approve:focus-visible { outline-color: var(--brand-green); }
.hb--reject, .hb--primary { background: var(--brand-red); border-color: var(--brand-red); color: #fff; }
.hb--reject:hover:not(:disabled), .hb--primary:hover:not(:disabled) { background: var(--brand-red-dark); border-color: var(--brand-red-dark); }
.hb--ghost { background: #fff; border-color: #e5e7eb; color: #374151; }
.hb--ghost:hover:not(:disabled) { background: #f9fafb; border-color: #d1d5db; }
.hb--muted, .hb--muted:disabled { background: #e5e7eb; border-color: #e5e7eb; color: #4b5563; opacity: 1; }
.hb--sm { min-height: 34px; padding: 0 12px; gap: 6px; border-radius: 8px; font-size: 0.78rem; }
</style>
