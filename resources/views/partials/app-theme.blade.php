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

/* ---------- Sidebar: slimmer, and collapsible to an icon rail ---------- */
.sb-toggle {
    display: none;
    align-items: center;
    gap: 12px;
    margin: 8px 24px 22px;
    padding: 10px 14px;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.04);
    color: rgba(255, 255, 255, 0.75);
    font: 500 0.88rem var(--mg-body);
    cursor: pointer;
    transition: background-color 0.2s ease, color 0.2s ease;
}
.sb-toggle svg { width: 18px; height: 18px; flex: none; transition: transform 0.3s ease; }
.sb-toggle:hover { background: rgba(255, 255, 255, 0.1); color: #fff; }
.sb-toggle:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

@media (min-width: 1024px) {
    .sb-toggle { display: flex; }
    body.app-body #sidebar { width: 264px; transition: width 0.28s cubic-bezier(0.2, 0.7, 0.2, 1); }
    body.app-body .app-main-col { padding-left: 264px; transition: padding-left 0.28s cubic-bezier(0.2, 0.7, 0.2, 1); }

    html.sb-collapsed body.app-body #sidebar { width: 88px; }
    html.sb-collapsed body.app-body .app-main-col { padding-left: 88px; }

    html.sb-collapsed #sidebar > div:first-of-type { padding-left: 0; padding-right: 0; }
    html.sb-collapsed #sidebar > div:first-of-type a { width: 56px; height: 56px; box-shadow: 0 0 0 3px rgba(201, 162, 75, 0.35); }
    html.sb-collapsed #sidebar > div:first-of-type a img { height: 42px; }
    html.sb-collapsed #sidebar > div:first-of-type h2 { display: none; }
    html.sb-collapsed #sidebar > div:first-of-type::after { margin-top: 14px; width: 28px; }

    html.sb-collapsed #sidebar nav { padding-left: 0; padding-right: 0; }
    html.sb-collapsed .app-side-link { justify-content: center; gap: 0; margin-left: 14px; margin-right: 14px; padding: 13px 0; }
    html.sb-collapsed .app-side-link span { display: none; }
    html.sb-collapsed .app-side-link i { width: auto; font-size: 1.05rem; }
    html.sb-collapsed .app-side-link:hover { padding-left: 0; }
    html.sb-collapsed .app-side-link.is-active::before { top: 12px; bottom: 12px; }

    html.sb-collapsed .sb-toggle { justify-content: center; margin: 8px 16px 22px; padding: 10px; }
    html.sb-collapsed .sb-toggle span { display: none; }
    html.sb-collapsed .sb-toggle svg { transform: rotate(180deg); }
}

/* ---------- Animated avatars ---------- */
.av-host { display: inline-block; border-radius: 50%; position: relative; }
.av-host .av { display: block; width: 100%; height: 100%; overflow: visible; border-radius: 50%; }
.av-host--ring { box-shadow: 0 0 0 2px #fff, 0 0 0 3px var(--mg-gold); }
.av-host--lg { box-shadow: 0 0 0 4px #fff, 0 0 0 6px var(--mg-gold), 0 22px 40px -22px rgba(74, 10, 18, 0.6); }

@media (prefers-reduced-motion: no-preference) {
    .av .av-body { transform-box: fill-box; transform-origin: 50% 100%; animation: av-breathe 4.4s ease-in-out infinite; }
    .av .av-head { transform-box: fill-box; transform-origin: 50% 92%; animation: av-sway 7s ease-in-out infinite; }
    .av .av-eyes { transform-box: fill-box; transform-origin: 50% 50%; animation: av-blink 5.2s infinite; }
    .av .av-book { transform-box: fill-box; transform-origin: 80% 90%; animation: av-book 4s ease-in-out infinite; }
    .av .av-wrench { transform-box: fill-box; transform-origin: 0% 100%; animation: av-wrench 2.6s ease-in-out infinite; }
    .av .av-cross, .av .av-pulse { transform-box: fill-box; transform-origin: 50% 50%; animation: av-pulse 1.6s ease-in-out infinite; }
    .av .av-shine { animation: av-shine 3s ease-in-out infinite; }
    .av .av-glint { animation: av-shine 3.6s ease-in-out infinite; }
    .av .av-lanyard { transform-box: fill-box; transform-origin: 50% 0%; animation: av-lanyard 3.4s ease-in-out infinite; }
    .av .av-calc { transform-box: fill-box; transform-origin: 50% 100%; animation: av-book 3.4s ease-in-out infinite; }
    .av .av-hat { transform-box: fill-box; transform-origin: 50% 100%; animation: av-hat 5s ease-in-out infinite; }
    .av .av-halo { transform-box: fill-box; transform-origin: 50% 50%; stroke-dasharray: 6 5; animation: av-halo 18s linear infinite; }

    /* Hover or focus: a happy little hop. */
    .av-host:hover .av .av-body,
    a:hover .av-host .av .av-body,
    article:hover .av-host .av .av-body,
    [role="button"]:hover .av-host .av .av-body { animation: av-hop 0.7s cubic-bezier(0.2, 0.9, 0.3, 1.4) both; }
}

@keyframes av-breathe { 0%, 100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-1.2px) scale(1.012); } }
@keyframes av-sway { 0%, 100% { transform: rotate(-2.2deg); } 50% { transform: rotate(2.2deg); } }
@keyframes av-blink { 0%, 44%, 48%, 100% { transform: scaleY(1); } 46% { transform: scaleY(0.08); } }
@keyframes av-book { 0%, 100% { transform: rotate(0deg) translateY(0); } 50% { transform: rotate(-5deg) translateY(-1.5px); } }
@keyframes av-wrench { 0%, 100% { transform: rotate(0deg); } 50% { transform: rotate(-14deg); } }
@keyframes av-pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.12); } }
@keyframes av-shine { 0%, 70%, 100% { opacity: 0.35; } 80% { opacity: 1; } }
@keyframes av-lanyard { 0%, 100% { transform: rotate(1.5deg); } 50% { transform: rotate(-1.5deg); } }
@keyframes av-hat { 0%, 100% { transform: rotate(0deg); } 50% { transform: rotate(-1.5deg); } }
@keyframes av-halo { to { transform: rotate(360deg); } }
@keyframes av-hop { 0% { transform: translateY(0); } 35% { transform: translateY(-6px) scale(1.03); } 70% { transform: translateY(1px); } 100% { transform: none; } }

/* Fixed avatar sizes (the prebuilt stylesheet has no custom size classes). */
.av-size-44 { width: 44px; height: 44px; flex: none; }
.av-size-120 { width: 120px; height: 120px; }
.av-size-top { width: 36px; height: 36px; flex: none; }
.av-size-profile { width: 80px; height: 80px; flex: none; }
@media (min-width: 640px) {
    .av-size-top { width: 44px; height: 44px; }
    .av-size-profile { width: 112px; height: 112px; }
}

/* ---------- Employee cards (compact) ---------- */
.emp-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; }
.emp-card {
    display: flex; align-items: center; gap: 14px; min-width: 0; padding: 14px 16px;
    border: 1px solid var(--mg-line); border-radius: 18px; background: #fff; text-align: left; cursor: pointer;
    box-shadow: 0 10px 24px -22px rgba(33, 23, 26, 0.5);
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}
.emp-card:hover { transform: translateY(-2px); border-color: var(--mg-gold); box-shadow: 0 18px 30px -22px rgba(74, 10, 18, 0.55); }
.emp-card:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }
.emp-card-face { width: 64px; height: 64px; flex: none; box-shadow: 0 0 0 3px #fff, 0 0 0 4px #efe4e1; }
.emp-card-info { display: grid; gap: 2px; min-width: 0; }
.emp-card-name { margin: 0; font-weight: 700; font-size: 1rem; color: var(--mg-ink); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.emp-card-role { margin: 0; font-size: 0.9rem; color: var(--mg-crimson); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.emp-card-id { margin: 0; font-size: 0.82rem; color: var(--mg-stone); }

/* ---------- Dashboard: people and workforce ---------- */
.dp { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr); gap: 20px; margin-bottom: 24px; }

.dp-card {
    padding: 24px;
    border: 1px solid var(--mg-line);
    border-radius: 24px;
    background: #fff;
    box-shadow: 0 24px 50px -40px rgba(33, 23, 26, 0.45);
}

.dp-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
.dp-title { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.45rem; color: var(--mg-oxblood); }
.dp-link { color: var(--mg-crimson); font-size: 0.92rem; font-weight: 600; text-decoration: underline; text-decoration-color: rgba(165, 18, 30, 0.3); text-underline-offset: 4px; }
.dp-link:hover { text-decoration-color: currentColor; }
.dp-sub { margin: 4px 0 0; color: var(--mg-stone); font-size: 0.92rem; }

.dp-faces {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(96px, 1fr));
    gap: 16px 10px;
    margin: 20px 0 0;
    padding: 0;
    list-style: none;
}

.dp-faces li { display: flex; flex-direction: column; align-items: center; text-align: center; min-width: 0; }
.dp-face { width: 76px; height: 76px; box-shadow: 0 0 0 3px #fff, 0 12px 24px -14px rgba(74, 10, 18, 0.55); }
.dp-face-name { margin-top: 10px; max-width: 100%; font-weight: 600; font-size: 0.92rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.dp-face-role { max-width: 100%; color: var(--mg-stone); font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.dp-mix-body { display: flex; align-items: center; gap: 22px; margin-top: 16px; }
.dp-donut { width: 150px; height: 150px; flex: none; }
.dp-donut-num { font-family: var(--mg-display); font-size: 26px; fill: var(--mg-oxblood); }
.dp-donut-label { font-family: var(--mg-body); font-size: 9.5px; fill: var(--mg-stone); }

.dp-legend { flex: 1; margin: 0; padding: 0; list-style: none; display: grid; gap: 10px; }
.dp-legend li { display: flex; align-items: center; gap: 10px; font-size: 0.92rem; }
.dp-legend-label { flex: 1; min-width: 0; color: var(--mg-ink); }
.dp-legend strong { font-family: var(--mg-display); font-weight: 400; font-size: 1.2rem; color: var(--mg-oxblood); }
.dp-swatch { width: 10px; height: 10px; flex: none; border-radius: 3px; }

.dp-split { margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--mg-line); }
.dp-bar { display: flex; height: 10px; overflow: hidden; border-radius: 999px; background: #f1e9e7; }
.dp-bar-seg { display: block; width: var(--w); height: 100%; }
.dp-bar-seg--female { background: var(--mg-crimson); }
.dp-bar-seg--male { background: var(--mg-gold); }
.dp-bar-seg--neutral { background: #cbbfbc; }
.dp-split-legend { display: flex; flex-wrap: wrap; gap: 6px 18px; margin: 12px 0 0; padding: 0; list-style: none; font-size: 0.88rem; color: var(--mg-stone); }
.dp-split-legend li { display: inline-flex; align-items: center; gap: 8px; }
.dp-split-legend strong { color: var(--mg-ink); }

@media (prefers-reduced-motion: no-preference) {
    .dp-faces li { animation: dx-rise 0.6s cubic-bezier(0.2, 0.7, 0.2, 1) both; animation-delay: calc(0.25s + var(--i, 0) * 70ms); }
    .dp-arc { animation: dp-draw 1.3s cubic-bezier(0.2, 0.7, 0.2, 1) both; animation-delay: calc(0.3s + var(--i, 0) * 0.2s); }
    .dp-bar-seg { animation: dp-grow 1.1s cubic-bezier(0.2, 0.7, 0.2, 1) both; animation-delay: calc(0.4s + var(--i, 0) * 0.15s); }
}

@keyframes dp-draw { from { stroke-dasharray: 0 1000; } }
@keyframes dp-grow { from { width: 0; } }

/* ---------- Calendar with agenda ---------- */
.cx {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    overflow: hidden;
    border: 1px solid var(--mg-line);
    border-radius: 24px;
    background: #fff;
    box-shadow: 0 24px 50px -40px rgba(33, 23, 26, 0.45);
}

.cx-main { padding: 24px; min-width: 0; }
.cx-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
.cx-month { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.7rem; color: var(--mg-oxblood); }
.cx-nav { display: flex; align-items: center; gap: 6px; }
.cx-today, .cx-arrow {
    display: inline-grid;
    place-items: center;
    height: 40px;
    border: 1px solid var(--mg-line);
    border-radius: 999px;
    background: #fff;
    color: var(--mg-ink);
    font: 600 0.9rem var(--mg-body);
    cursor: pointer;
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}
.cx-today { padding: 0 16px; }
.cx-arrow { width: 40px; }
.cx-arrow svg { width: 18px; height: 18px; }
.cx-today:hover, .cx-arrow:hover { border-color: var(--mg-crimson); color: var(--mg-crimson); }
.cx-today:focus-visible, .cx-arrow:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

.cx-week, .cx-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 8px; }
.cx-week span { padding-bottom: 8px; color: var(--mg-stone); font-size: 0.8rem; font-weight: 600; text-align: center; }

.cx-day {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
    min-height: 84px;
    padding: 9px;
    border: 1px solid #f1e9e7;
    border-radius: 14px;
    background: #fff;
    color: var(--mg-ink);
    font: inherit;
    text-align: left;
    cursor: pointer;
    transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
}
.cx-day--empty { border: 0; background: none; cursor: default; }
.cx-day--weekend { background: #fbf8f7; }
.cx-day--past .cx-num { color: #a89c99; }
button.cx-day:hover { transform: translateY(-2px); border-color: var(--mg-gold); box-shadow: 0 14px 24px -18px rgba(74, 10, 18, 0.55); }
button.cx-day:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

.cx-num { display: grid; place-items: center; min-width: 28px; height: 28px; border-radius: 50%; font-weight: 700; font-size: 0.95rem; }
.cx-day--today .cx-num { background: var(--mg-crimson); color: #fff; box-shadow: 0 0 0 4px rgba(165, 18, 30, 0.14); }
.cx-day--selected { border-color: var(--mg-gold); background: #fffaf0; box-shadow: inset 0 0 0 1px var(--mg-gold); }

.cx-tag {
    max-width: 100%;
    padding: 2px 7px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    line-height: 1.35;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.cx-day--regular .cx-tag { background: #fbe4e4; color: #8f1420; }
.cx-day--special .cx-tag { background: #fbf0d6; color: #7a5a14; }
.cx-day--school .cx-tag { background: #4a0a12; color: var(--mg-gold-soft); }

.cx-count { display: flex; align-items: center; gap: 3px; margin-top: auto; }
.cx-count i { width: 7px; height: 7px; border-radius: 50%; background: var(--mg-crimson); }
.cx-count b { font-size: 0.7rem; color: var(--mg-crimson); }

.cx-legend { display: flex; flex-wrap: wrap; gap: 8px 18px; margin: 16px 0 0; padding: 0; list-style: none; color: var(--mg-stone); font-size: 0.82rem; }
.cx-legend li { display: inline-flex; align-items: center; gap: 7px; }
.cx-dot { display: inline-block; width: 9px; height: 9px; flex: none; border-radius: 50%; }
.cx-dot--regular { background: #c1121f; }
.cx-dot--special { background: var(--mg-gold); }
.cx-dot--school { background: var(--mg-oxblood); }
.cx-dot--task { background: var(--mg-crimson); box-shadow: 0 0 0 2px #fbe4e4; }

.cx-agenda {
    display: flex;
    flex-direction: column;
    padding: 24px;
    color: #fff;
    background:
        radial-gradient(300px 220px at 100% 0%, rgba(165, 18, 30, 0.55), transparent 70%),
        linear-gradient(170deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
}
.cx-selected { display: flex; align-items: center; gap: 14px; }
.cx-selected-num { font-family: var(--mg-display); font-size: 3.6rem; line-height: 1; color: var(--mg-gold-soft); }
.cx-selected-text { display: grid; gap: 2px; font-size: 0.95rem; }
.cx-selected-text span:first-child { font-weight: 600; font-size: 1.05rem; }
.cx-selected-text span:last-child { color: rgba(255, 255, 255, 0.72); }

.cx-holiday { display: grid; gap: 2px; margin: 16px 0 0; padding: 10px 12px; border-radius: 12px; background: rgba(255, 255, 255, 0.08); border-left: 3px solid var(--mg-gold); font-size: 0.9rem; }
.cx-holiday[hidden] { display: none; }
.cx-holiday span { color: rgba(255, 255, 255, 0.7); font-size: 0.8rem; }
.cx-holiday--regular { border-left-color: #ff6b74; }
.cx-holiday--school { border-left-color: var(--mg-gold-soft); }

.cx-sub { margin: 22px 0 8px; font-family: var(--mg-display); font-weight: 400; font-size: 1.05rem; color: var(--mg-gold-soft); }
.cx-tasks, .cx-upcoming { display: grid; gap: 6px; margin: 0; padding: 0; list-style: none; font-size: 0.9rem; }
.cx-tasks li { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 9px 12px; border-radius: 10px; background: rgba(255, 255, 255, 0.07); }
.cx-tasks li span { min-width: 0; overflow-wrap: anywhere; }
.cx-tasks button { flex: none; padding: 4px 10px; border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 999px; background: none; color: rgba(255, 255, 255, 0.8); font: 600 0.75rem var(--mg-body); cursor: pointer; }
.cx-tasks button:hover { border-color: #fff; color: #fff; }
.cx-empty { padding: 4px 0; background: none !important; color: rgba(255, 255, 255, 0.6); }

.cx-add { display: flex; gap: 8px; margin-top: 10px; }
.cx-add input { flex: 1; min-width: 0; height: 42px; padding: 0 12px; border: 1px solid rgba(255, 255, 255, 0.22); border-radius: 10px; background: rgba(255, 255, 255, 0.08); color: #fff; font: 0.9rem var(--mg-body); }
.cx-add input::placeholder { color: rgba(255, 255, 255, 0.55); }
.cx-add input:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; }
.cx-add button { height: 42px; padding: 0 16px; border: 0; border-radius: 10px; background: var(--mg-gold); color: var(--mg-oxblood-deep); font: 700 0.9rem var(--mg-body); cursor: pointer; }
.cx-add button:hover { background: var(--mg-gold-soft); }

.cx-upcoming li { display: grid; grid-template-columns: 52px 9px minmax(0, 1fr); align-items: center; gap: 10px; }
.cx-upcoming time { color: var(--mg-gold-soft); font-weight: 600; font-size: 0.85rem; }
.cx-upcoming li.cx-empty { display: block; }
.cx-footnote { margin: auto 0 0; padding-top: 18px; color: rgba(255, 255, 255, 0.5); font-size: 0.75rem; }

@media (prefers-reduced-motion: no-preference) {
    .cx-grid--next .cx-day, .cx-grid--prev .cx-day { animation: cx-in 0.4s cubic-bezier(0.2, 0.7, 0.2, 1) both; animation-delay: calc(var(--d, 0) * 8ms); }
    .cx-grid--prev .cx-day { animation-name: cx-in-prev; }
    .cx-day--today .cx-num { animation: cx-today 2.4s ease-out infinite; }
    .cx-agenda--flash .cx-selected, .cx-agenda--flash .cx-holiday, .cx-agenda--flash .cx-tasks { animation: dx-rise 0.4s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
}

@keyframes cx-in { from { opacity: 0; transform: translateX(14px); } to { opacity: 1; transform: none; } }
@keyframes cx-in-prev { from { opacity: 0; transform: translateX(-14px); } to { opacity: 1; transform: none; } }
@keyframes cx-today { 0% { box-shadow: 0 0 0 0 rgba(165, 18, 30, 0.45); } 70%, 100% { box-shadow: 0 0 0 9px rgba(165, 18, 30, 0); } }

.cx--solo { grid-template-columns: minmax(0, 1fr); }
.cx .contents { display: contents; }

@media (max-width: 1180px) {
    .cx { grid-template-columns: 1fr; }
    .dp { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
    .cx-main, .cx-agenda, .dp-card { padding: 16px; }
    .cx-week, .cx-grid { gap: 4px; }
    .cx-day { min-height: 46px; padding: 4px; align-items: center; border-radius: 10px; }
    .cx-num { min-width: 26px; height: 26px; font-size: 0.85rem; }
    .cx-tag { display: none; }
    .cx-day--regular, .cx-day--special, .cx-day--school { box-shadow: inset 0 -3px 0 #c1121f; }
    .cx-day--special { box-shadow: inset 0 -3px 0 var(--mg-gold); }
    .cx-day--school { box-shadow: inset 0 -3px 0 var(--mg-oxblood); }
    .cx-count { margin-top: 0; }
    .cx-count i { width: 5px; height: 5px; }
    .cx-month { font-size: 1.3rem; }
    .dp-mix-body { flex-direction: column; align-items: stretch; }
    .dp-donut { margin: 0 auto; }
    .dp-faces { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .dp-face { width: 64px; height: 64px; }
}

/* ---------- Hidden government IDs ---------- */
.secret-value { display: inline-flex; align-items: center; gap: 8px; }
.secret-value__text { font-variant-numeric: tabular-nums; letter-spacing: 0.04em; }
.secret-value__eye {
    display: inline-grid; place-items: center; width: 30px; height: 30px; flex: none;
    border: 1px solid var(--mg-line); border-radius: 999px; background: #fff; color: var(--mg-stone); cursor: pointer;
    transition: color 0.2s ease, border-color 0.2s ease;
}
.secret-value__eye svg { width: 16px; height: 16px; }
.secret-value__eye:hover { color: var(--mg-crimson); border-color: var(--mg-crimson); }
.secret-value__eye:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

.gov-ids { padding: 14px 16px; border: 1px solid var(--mg-line); border-radius: 14px; background: #fbf8f7; }
.gov-ids__title { margin: 0 0 8px; font-family: var(--mg-display); font-weight: 400; font-size: 1.1rem; color: var(--mg-oxblood); }
.gov-ids ul { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
.gov-ids li { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 32px; font-size: 0.92rem; }
.gov-ids__label { font-weight: 600; color: var(--mg-ink); }

/* ---------- Daily Time Record ---------- */
.dtr { display: grid; gap: 20px; }

.dtr-steps {
    display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; align-items: end;
    padding: 20px; border: 1px solid var(--mg-line); border-radius: 22px; background: #fff;
}
.dtr-step { display: grid; grid-template-columns: auto 1fr; grid-template-rows: auto auto; column-gap: 12px; row-gap: 6px; align-items: center; }
.dtr-step-num {
    grid-row: 1 / span 2; display: grid; place-items: center; width: 34px; height: 34px; border-radius: 50%;
    background: var(--mg-oxblood); color: var(--mg-gold-soft); font-family: var(--mg-display); font-size: 1.05rem;
}
.dtr-step label { font-weight: 600; font-size: 0.88rem; color: var(--mg-stone); }
.dtr-step input {
    height: 46px; padding: 0 12px; border: 1px solid var(--mg-line); border-radius: 12px; background: #fbf8f7; color: var(--mg-ink); font: 0.95rem var(--mg-body);
}
.dtr-step input:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; }
.dtr-step--go button {
    grid-row: 1 / span 2; height: 46px; padding: 0 20px; border: 0; border-radius: 12px; background: var(--mg-crimson); color: #fff;
    font: 700 0.95rem var(--mg-body); cursor: pointer; transition: background-color 0.2s ease;
}
.dtr-step--go button:hover { background: var(--mg-oxblood); }
.dtr-step--go button:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }
.dtr-hint, .dtr-error { grid-column: 1 / -1; margin: 0; font-size: 0.82rem; color: var(--mg-stone); }
.dtr-error { color: var(--mg-crimson); font-weight: 600; }

.dtr-stats { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; }
.dtr-stat { display: grid; gap: 4px; padding: 16px 18px; border: 1px solid var(--mg-line); border-radius: 18px; background: #fff; }
.dtr-stat strong { font-family: var(--mg-display); font-weight: 400; font-size: 2rem; line-height: 1; color: var(--mg-oxblood); }
.dtr-stat span { font-size: 0.85rem; color: var(--mg-stone); }

.dtr-card { overflow: hidden; border: 1px solid var(--mg-line); border-radius: 22px; background: #fff; box-shadow: 0 24px 50px -40px rgba(33, 23, 26, 0.45); }
.dtr-card-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 12px 24px; padding: 22px 22px 8px; }
.dtr-title { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.6rem; color: var(--mg-oxblood); }
.dtr-period { margin: 2px 0 0; color: var(--mg-stone); font-size: 0.92rem; }
.dtr-legend { display: flex; flex-wrap: wrap; gap: 6px 16px; margin: 0; padding: 0; list-style: none; font-size: 0.82rem; color: var(--mg-stone); }
.dtr-legend li { display: inline-flex; align-items: center; gap: 7px; }
.dtr-swatch { width: 14px; height: 14px; border-radius: 4px; }
.dtr-swatch--rest { background: #fdf3c8; box-shadow: inset 3px 0 0 #d9a514; }
.dtr-swatch--holiday { background: #fde4e6; box-shadow: inset 3px 0 0 #c1121f; }
.dtr-swatch--special { background: #e3eefc; box-shadow: inset 3px 0 0 #2f6fcf; }
.dtr-swatch--leave { background: #e3f5ea; box-shadow: inset 3px 0 0 #2e8b57; }

.dtr-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; padding: 12px 22px 16px; }
.dtr-actions { display: inline-flex; overflow: hidden; border: 1px solid var(--mg-line); border-radius: 10px; }
.dtr-actions button { height: 38px; padding: 0 16px; border: 0; background: #fff; color: var(--mg-ink); font: 600 0.88rem var(--mg-body); cursor: pointer; }
.dtr-actions button + button { border-left: 1px solid var(--mg-line); }
.dtr-actions button:hover { background: #fbf3f2; color: var(--mg-crimson); }
.dtr-show { display: inline-flex; align-items: center; gap: 8px; font-size: 0.88rem; color: var(--mg-stone); }
.dtr-show select { height: 38px; padding: 0 30px 0 10px; border: 1px solid var(--mg-line); border-radius: 10px; font-size: 0.88rem; }
.dtr-search { margin-left: auto; }
.dtr-search input { width: 260px; max-width: 100%; height: 38px; padding: 0 12px; border: 1px solid var(--mg-line); border-radius: 10px; font-size: 0.9rem; }
.dtr-search input:focus, .dtr-show select:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; }

.dtr-table-wrap { overflow-x: auto; border-top: 1px solid var(--mg-line); }
.dtr-table { width: 100%; min-width: 960px; border-collapse: collapse; font-size: 0.86rem; }
.dtr-table th { padding: 12px 8px; background: var(--mg-oxblood); color: #fff; font-weight: 600; text-align: left; white-space: nowrap; }
.dtr-table td { padding: 11px 8px; border-bottom: 1px solid #f1e9e7; color: var(--mg-ink); white-space: nowrap; }
.dtr-col-x { width: 44px; text-align: center !important; }
.dtr-date { font-weight: 600; }
.dtr-warn { color: var(--mg-crimson) !important; font-weight: 600; }
.dtr-type { font-weight: 600; }
.dtr-expand {
    display: inline-grid; place-items: center; width: 24px; height: 24px; border: 0; border-radius: 50%;
    background: var(--mg-crimson); color: #fff; font: 700 1rem/1 var(--mg-body); cursor: pointer;
}
.dtr-expand:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

.dtr-row--rest td { background: #fdf6d8; }
.dtr-row--holiday td { background: #fdeaec; }
.dtr-row--special td { background: #e8f1fd; }
.dtr-row--leave td { background: #e8f6ee; }
.dtr-row--rest .dtr-row td:first-child { box-shadow: inset 4px 0 0 #d9a514; }
.dtr-row--holiday .dtr-row td:first-child { box-shadow: inset 4px 0 0 #c1121f; }
.dtr-row--special .dtr-row td:first-child { box-shadow: inset 4px 0 0 #2f6fcf; }
.dtr-row--leave .dtr-row td:first-child { box-shadow: inset 4px 0 0 #2e8b57; }
.dtr-row--regular .dtr-row:hover td { background: #fbf8f7; }

.dtr-detail td { padding-top: 4px; padding-bottom: 14px; white-space: normal; }
.dtr-detail dl { display: flex; flex-wrap: wrap; gap: 8px 28px; margin: 0; }
.dtr-detail dl > div { display: grid; gap: 2px; }
.dtr-detail dt { font-size: 0.76rem; color: var(--mg-stone); }
.dtr-detail dd { margin: 0; font-weight: 600; }
.dtr-empty { padding: 28px !important; text-align: center; color: var(--mg-stone) !important; }

.dtr-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 22px; font-size: 0.88rem; color: var(--mg-stone); }
.dtr-foot p { margin: 0; }
.dtr-pager { display: inline-flex; align-items: center; gap: 10px; }
.dtr-pager button { height: 36px; padding: 0 14px; border: 1px solid var(--mg-line); border-radius: 10px; background: #fff; font: 600 0.86rem var(--mg-body); cursor: pointer; }
.dtr-pager button:disabled { opacity: 0.45; cursor: default; }
.dtr-pager button:not(:disabled):hover { border-color: var(--mg-crimson); color: var(--mg-crimson); }

@media (max-width: 1100px) { .dtr-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
@media (max-width: 720px) {
    .dtr-steps { grid-template-columns: 1fr; }
    .dtr-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dtr-search { margin-left: 0; width: 100%; }
    .dtr-search input { width: 100%; }
}

/* ---------- Employee details window ---------- */
.ed-overlay { position: fixed; inset: 0; z-index: 60; display: grid; place-items: center; padding: 16px; background: rgba(33, 10, 14, 0.55); backdrop-filter: blur(3px); }
.ed {
    position: relative; width: 100%; max-width: 720px; max-height: 92vh; overflow-y: auto; scrollbar-width: none;
    border-radius: 26px; background: #fff; box-shadow: 0 40px 80px -30px rgba(33, 10, 14, 0.6);
}
.ed-head {
    position: relative; display: flex; align-items: center; gap: 22px; padding: 30px 30px 26px; color: #fff;
    background:
        radial-gradient(380px 220px at 100% 0%, rgba(201, 162, 75, 0.28), transparent 70%),
        radial-gradient(300px 200px at 0% 100%, rgba(165, 18, 30, 0.6), transparent 70%),
        linear-gradient(150deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
}
.ed-face { width: 116px; height: 116px; flex: none; }
.ed-who { min-width: 0; }
.ed-name { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.9rem; line-height: 1.1; color: #fff; }
.ed-role { margin: 4px 0 0; color: var(--mg-gold-soft); font-size: 1rem; }
.ed-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 12px; }
.ed-chip { padding: 4px 11px; border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 999px; font-size: 0.8rem; font-weight: 600; color: rgba(255, 255, 255, 0.9); }
.ed-chip--gold { border-color: transparent; background: var(--mg-gold); color: var(--mg-oxblood-deep); }
.ed-close {
    position: absolute; top: 14px; right: 14px; display: grid; place-items: center; width: 36px; height: 36px;
    border: 0; border-radius: 50%; background: rgba(255, 255, 255, 0.12); color: #fff; cursor: pointer;
}
.ed-close svg { width: 18px; height: 18px; }
.ed-close:hover { background: rgba(255, 255, 255, 0.22); }
.ed-close:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

.ed-tabs { display: flex; gap: 4px; overflow-x: auto; padding: 0 22px; border-bottom: 1px solid var(--mg-line); scrollbar-width: none; }
.ed-tabs button {
    position: relative; flex: none; padding: 15px 10px 13px; border: 0; background: none; color: var(--mg-stone);
    font: 600 0.9rem var(--mg-body); cursor: pointer;
}
.ed-tabs button[aria-selected="true"] { color: var(--mg-crimson); }
.ed-tabs button[aria-selected="true"]::after { content: ""; position: absolute; left: 8px; right: 8px; bottom: -1px; height: 3px; border-radius: 3px 3px 0 0; background: var(--mg-crimson); }
.ed-tabs button:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: -2px; border-radius: 8px; }

.ed-body { padding: 24px 30px 8px; min-height: 190px; }
.ed-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 28px; margin: 0; }
.ed-grid > div { min-width: 0; }
.ed-grid dt { font-size: 0.8rem; color: var(--mg-stone); }
.ed-grid dd { margin: 3px 0 0; font-weight: 600; color: var(--mg-ink); overflow-wrap: anywhere; }
.ed-wide { grid-column: 1 / -1; }
.ed-note { margin: 0 0 12px; font-size: 0.85rem; color: var(--mg-stone); }
.ed-ids { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
.ed-ids li { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 14px; border: 1px solid var(--mg-line); border-radius: 12px; background: #fbf8f7; }
.ed-ids-label { font-weight: 600; }
.ed-muted { font-style: italic; color: var(--mg-stone); }

.ed-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 18px 30px 26px; }
.ed-btn { height: 44px; padding: 0 20px; border-radius: 12px; font: 700 0.92rem var(--mg-body); cursor: pointer; transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease; }
.ed-btn--primary { border: 0; background: var(--mg-crimson); color: #fff; }
.ed-btn--primary:hover { background: var(--mg-oxblood); }
.ed-btn--ghost { border: 1px solid var(--mg-line); background: #fff; color: var(--mg-stone); }
.ed-btn--ghost:hover { border-color: var(--mg-crimson); color: var(--mg-crimson); }
.ed-btn:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

@media (prefers-reduced-motion: no-preference) {
    .ed { animation: ed-in 0.35s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
    .ed-body [role="tabpanel"] { animation: dx-rise 0.3s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
}
@keyframes ed-in { from { opacity: 0; transform: translateY(16px) scale(0.98); } to { opacity: 1; transform: none; } }

@media (max-width: 560px) {
    .ed-head { flex-direction: column; text-align: center; padding: 28px 20px 22px; }
    .ed-chips { justify-content: center; }
    .ed-face { width: 96px; height: 96px; }
    .ed-name { font-size: 1.5rem; }
    .ed-body { padding: 20px; }
    .ed-grid { grid-template-columns: 1fr; }
    .ed-foot { padding: 16px 20px 22px; }
    .ed-foot > * { flex: 1; }
    .ed-foot .ed-btn { width: 100%; }
}

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
