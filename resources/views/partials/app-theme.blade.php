{{--
    Look of the signed-in side of the system (sidebar, top bar, page headings,
    dashboard). It sits on top of the Tailwind classes in the views, so the
    page markup did not need to change.
--}}
<link rel="preload" href="{{ asset('fonts/instrument-sans-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
<style>
    @font-face {
        font-family: 'Marcellus';
        src: url("{{ asset('fonts/marcellus-400.woff2') }}") format('woff2');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'Instrument Sans';
        src: url("{{ asset('fonts/instrument-sans-variable.woff2') }}") format('woff2');
        font-weight: 400 700;
        font-style: normal;
        font-display: swap;
    }
</style>
@verbatim
<style>
:root {
    --mg-oxblood: #4a0a12;
    --mg-oxblood-deep: #33060c;
    --mg-crimson: #a5121e;
    --mg-gold: #c9a24b;
    --mg-gold-soft: #e6cf9a;
    --mg-ink: #21171a;
    --mg-stone: #6d6164;
    --mg-mist: #f6f2f1;
    --mg-line: #e6ddda;
    --mg-display: 'Marcellus', 'Times New Roman', serif;
    --mg-body: 'Instrument Sans', system-ui, 'Segoe UI', Roboto, Arial, sans-serif;
}

body.app-body {
    font-family: var(--mg-body);
    color: var(--mg-ink);
    background: var(--mg-mist);
    -webkit-font-smoothing: antialiased;
}

body.app-body .app-shell { background: var(--mg-mist); }

/* ---------- Sidebar ---------- */
body.app-body #sidebar {
    border-right: 0;
    background:
        radial-gradient(420px 320px at 0% 0%, rgba(165, 18, 30, 0.55), transparent 70%),
        linear-gradient(185deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
    box-shadow: 12px 0 40px -28px rgba(33, 23, 26, 0.6);
}

#sidebar > div:first-of-type a {
    display: grid;
    place-items: center;
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(201, 162, 75, 0.35), 0 16px 30px -14px rgba(0, 0, 0, 0.6);
}

#sidebar > div:first-of-type a img { height: 72px; }

#sidebar > div:first-of-type h2 {
    margin-top: 18px;
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: 1.18rem;
    letter-spacing: 0.01em;
    color: var(--mg-gold-soft);
}

#sidebar > div:first-of-type::after {
    content: '';
    display: block;
    width: 48px;
    height: 1px;
    margin-top: 18px;
    background: var(--mg-gold);
    opacity: 0.7;
}

#sidebar > button { color: rgba(255, 255, 255, 0.85); }
#sidebar > button:hover { background: rgba(255, 255, 255, 0.08); }

.app-side-link {
    position: relative;
    color: rgba(255, 255, 255, 0.78);
    border-radius: 12px;
    transition: background-color 0.2s ease, color 0.2s ease, padding-left 0.2s ease;
}

.app-side-link i { color: rgba(230, 207, 154, 0.75); transition: color 0.2s ease, transform 0.2s ease; }

.app-side-link:hover { background: rgba(255, 255, 255, 0.07); color: #fff; padding-left: 1.75rem; }
.app-side-link:hover i { color: var(--mg-gold-soft); transform: scale(1.12); }

.app-side-link.is-active {
    background: linear-gradient(90deg, rgba(201, 162, 75, 0.26), rgba(201, 162, 75, 0.08));
    color: #fff;
    font-weight: 600;
}

.app-side-link.is-active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 10px;
    bottom: 10px;
    width: 3px;
    border-radius: 3px;
    background: var(--mg-gold);
}

.app-side-link.is-active i { color: var(--mg-gold); }

.app-side-link:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

/* ---------- Top bar and page headings ---------- */
body.app-body .app-page-heading h1,
body.app-body .app-page-heading h2 {
    font-family: var(--mg-display);
    font-weight: 400;
    letter-spacing: 0.005em;
    color: var(--mg-oxblood);
}

body.app-body [aria-label="Account Menu"] {
    border-color: var(--mg-line);
    box-shadow: 0 8px 24px -16px rgba(33, 23, 26, 0.35);
}

body.app-body [aria-label="Account Menu"] > span:first-child {
    background: linear-gradient(140deg, var(--mg-crimson), var(--mg-oxblood));
    box-shadow: 0 0 0 2px #fff, 0 0 0 3px var(--mg-gold);
}

/* ---------- Dashboard: welcome band ---------- */
.dx-hero {
    position: relative;
    overflow: hidden;
    margin-bottom: 20px;
    padding: clamp(24px, 3.4vw, 40px);
    border-radius: 24px;
    color: #fff;
    background:
        radial-gradient(620px 340px at 88% 0%, rgba(165, 18, 30, 0.6), transparent 70%),
        linear-gradient(160deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
    box-shadow: 0 30px 60px -36px rgba(74, 10, 18, 0.75);
}

.dx-hero::before {
    content: '';
    position: absolute;
    right: -60px;
    bottom: -140px;
    width: 340px;
    height: 340px;
    border: 1px solid rgba(201, 162, 75, 0.45);
    border-radius: 50%;
}

.dx-hero::after {
    content: '';
    position: absolute;
    right: 20px;
    bottom: -200px;
    width: 340px;
    height: 340px;
    border: 1px solid rgba(201, 162, 75, 0.2);
    border-radius: 50%;
}

.dx-motes { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; }

.dx-hero-inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px 40px;
}

.dx-date { margin: 0; color: var(--mg-gold-soft); font-size: 0.95rem; font-weight: 500; }

.dx-greeting {
    margin: 10px 0 0;
    font-family: var(--mg-display);
    font-weight: 400;
    font-size: clamp(1.9rem, 3.4vw, 2.8rem);
    line-height: 1.1;
    color: #fff;
}

.dx-lede { max-width: 48ch; margin: 12px 0 0; color: rgba(255, 255, 255, 0.8); font-size: 1rem; }

.dx-actions { display: flex; flex-wrap: wrap; gap: 10px; }

.dx-action {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    min-height: 44px;
    padding: 0 18px;
    border: 1px solid rgba(255, 255, 255, 0.28);
    border-radius: 999px;
    color: #fff;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.06);
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.dx-action:first-child { border-color: transparent; background: var(--mg-gold); color: var(--mg-oxblood-deep); }
.dx-action svg { width: 18px; height: 18px; transition: transform 0.2s ease; }
.dx-action:hover { background: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.6); }
.dx-action:first-child:hover { background: var(--mg-gold-soft); }
.dx-action:hover svg { transform: translateX(4px); }
.dx-action:focus-visible { outline: 2px solid var(--mg-gold-soft); outline-offset: 3px; }

/* ---------- Dashboard: figures ---------- */
.dx-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.dx-stat {
    position: relative;
    display: grid;
    grid-template-columns: 1fr auto;
    align-content: start;
    padding: 20px 22px;
    border: 1px solid var(--mg-line);
    border-radius: 20px;
    background: #fff;
    color: var(--mg-ink);
    text-decoration: none;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.dx-stat::after {
    content: '';
    position: absolute;
    left: 22px;
    right: 22px;
    bottom: 0;
    height: 3px;
    border-radius: 3px 3px 0 0;
    background: linear-gradient(90deg, var(--mg-crimson), var(--mg-gold));
    transform: scaleX(0.25);
    transform-origin: 0 50%;
    transition: transform 0.4s ease;
}

a.dx-stat[href]:hover { transform: translateY(-4px); border-color: transparent; box-shadow: 0 24px 40px -26px rgba(74, 10, 18, 0.55); }
a.dx-stat[href]:hover::after { transform: scaleX(1); }
a.dx-stat:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 3px; }

.dx-stat-icon {
    grid-column: 2;
    grid-row: 1 / span 2;
    display: grid;
    place-items: center;
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: var(--mg-mist);
    color: var(--mg-crimson);
    font-size: 1.05rem;
}

.dx-stat-value {
    grid-column: 1;
    grid-row: 1;
    font-family: var(--mg-display);
    font-size: 2.6rem;
    line-height: 1;
    color: var(--mg-oxblood);
    font-variant-numeric: tabular-nums;
}

.dx-stat-label { grid-column: 1 / -1; margin-top: 12px; font-weight: 600; }
.dx-stat-note { grid-column: 1 / -1; margin-top: 2px; color: var(--mg-stone); font-size: 0.88rem; }

/* ---------- Dashboard: banner and calendar ---------- */
body.app-body .admin-dashboard .promo-banner { padding: 0; background: none; box-shadow: none; }
body.app-body .admin-dashboard .promo-slides { border-radius: 24px; box-shadow: 0 30px 60px -38px rgba(33, 23, 26, 0.6); }

body.app-body .calendar-panel {
    border: 1px solid var(--mg-line);
    border-radius: 24px;
    box-shadow: 0 24px 50px -40px rgba(33, 23, 26, 0.45);
}

body.app-body #calendarHeading {
    font-family: var(--mg-display);
    font-weight: 400;
    color: var(--mg-oxblood);
}

body.app-body .calendar-widget { border-color: var(--mg-line); }
body.app-body .calendar-day:not(.empty) { transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease; }
body.app-body .calendar-day:not(.empty):hover { transform: translateY(-2px); border-color: var(--mg-gold); box-shadow: 0 14px 24px -18px rgba(74, 10, 18, 0.5); }

/* ---------- Motion ---------- */
@media (prefers-reduced-motion: no-preference) {
    .dx-hero-copy > *, .dx-actions { animation: dx-rise 0.7s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
    .dx-hero-copy > :nth-child(2) { animation-delay: 0.06s; }
    .dx-hero-copy > :nth-child(3) { animation-delay: 0.12s; }
    .dx-actions { animation-delay: 0.18s; }
    .dx-stat { animation: dx-rise 0.7s cubic-bezier(0.2, 0.7, 0.2, 1) both; animation-delay: calc(0.2s + var(--i, 0) * 80ms); }
    .dx-hero::before { animation: dx-ring 9s ease-in-out infinite alternate; }

    .app-side-link { animation: dx-slide 0.5s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
    #sidebar nav > a:nth-child(2) { animation-delay: 0.03s; }
    #sidebar nav > a:nth-child(3) { animation-delay: 0.06s; }
    #sidebar nav > a:nth-child(4) { animation-delay: 0.09s; }
    #sidebar nav > a:nth-child(5) { animation-delay: 0.12s; }
    #sidebar nav > a:nth-child(6) { animation-delay: 0.15s; }
    #sidebar nav > a:nth-child(7) { animation-delay: 0.18s; }
    #sidebar nav > a:nth-child(8) { animation-delay: 0.21s; }
    #sidebar nav > a:nth-child(9) { animation-delay: 0.24s; }
    #sidebar nav > a:nth-child(n+10) { animation-delay: 0.27s; }
}

@keyframes dx-rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
@keyframes dx-slide { from { opacity: 0; transform: translateX(-14px); } to { opacity: 1; transform: none; } }
@keyframes dx-ring { from { transform: scale(1); opacity: 0.6; } to { transform: scale(1.12); opacity: 1; } }

@media (max-width: 1100px) {
    .dx-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 520px) {
    .dx-hero { border-radius: 18px; }
    .dx-stats { gap: 10px; }
    .dx-stat { padding: 16px; border-radius: 16px; }
    .dx-stat-value { font-size: 2.1rem; }
    .dx-stat-icon { width: 36px; height: 36px; border-radius: 12px; font-size: 0.95rem; }
    .dx-action { flex: 1 1 100%; justify-content: space-between; }
}
</style>
@endverbatim
