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

.app-side-link:hover { background: rgba(255, 255, 255, 0.07); color: #fff; }

/* One line per menu item: tighter spacing and slightly smaller type. */
body.app-body #sidebar .app-side-link {
    gap: 10px;
    margin-left: 8px;
    margin-right: 8px;
    padding: 11px 10px;
    font-size: 0.79rem;
    line-height: 1.3;
    white-space: nowrap;
}
body.app-body #sidebar .app-side-link span { min-width: 0; overflow: hidden; text-overflow: ellipsis; }
body.app-body #sidebar .app-side-link i { width: 16px; flex: none; }
body.app-body #sidebar .app-side-link:hover { padding-left: 13px; }
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
    html.sb-collapsed body.app-body #sidebar .app-side-link { justify-content: center; gap: 0; margin-left: 14px; margin-right: 14px; padding: 13px 0; }
    html.sb-collapsed .app-side-link span { display: none; }
    html.sb-collapsed .app-side-link i { width: auto; font-size: 1.05rem; }
    html.sb-collapsed body.app-body #sidebar .app-side-link:hover { padding-left: 0; }
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

.av--initials .av-initials { font-family: var(--mg-display); letter-spacing: 0.02em; }

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
.ed-edu-school { display: block; margin-top: 2px; font-size: 0.82rem; color: var(--mg-stone); }
.emp-hire-alert { display: flex; gap: 12px; align-items: flex-start; margin: 0 0 12px; padding: 12px 16px; border-radius: 14px; background: #fff6e6; border: 1px solid #f1d7a6; color: #6b4a12; font-size: 0.88rem; line-height: 1.5; }
.emp-hire-alert i { color: #b7791f; margin-top: 3px; }
.emp-hire-alert p { margin: 0; }
.emp-archived-note { margin: 0 0 12px; font-size: 0.85rem; color: var(--mg-stone); }
.emp-archived-note a { color: var(--mg-crimson); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
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

/* ---------- DTR confirmation ---------- */
.dtr-confirm {
    display: grid; grid-template-columns: minmax(240px, 360px) minmax(0, 1fr) auto; gap: 16px 22px; align-items: center;
    padding: 20px 22px; border: 1px solid var(--mg-line); border-left: 5px solid var(--mg-gold); border-radius: 22px; background: #fff;
}
.dtr-confirm.is-done { border-left-color: #2e8b57; background: linear-gradient(90deg, #f0faf4, #fff 60%); }
.dtr-confirm-pick { display: grid; gap: 6px; min-width: 0; }
.dtr-confirm-pick label { font-weight: 600; font-size: 0.88rem; color: var(--mg-stone); }
.dtr-confirm-pick select { width: 100%; min-width: 0; height: 46px; padding: 0 34px 0 12px; border: 1px solid var(--mg-line); border-radius: 12px; background-color: #fbf8f7; font-size: 0.92rem; }
.dtr-confirm-pick select:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; }
.dtr-confirm-state p { margin: 0; font-size: 0.92rem; color: var(--mg-ink); }
.dtr-confirm-state strong { color: #2e8b57; }
.dtr-confirm-btn { height: 46px; padding: 0 20px; border: 0; border-radius: 12px; background: var(--mg-crimson); color: #fff; font: 700 0.95rem var(--mg-body); cursor: pointer; white-space: nowrap; }
.dtr-confirm-btn:hover { background: var(--mg-oxblood); }
.dtr-confirm-btn:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }
@media (max-width: 900px) { .dtr-confirm { grid-template-columns: 1fr; } .dtr-confirm-btn { width: 100%; } }

.dtr-badge { margin-top: 3px; padding: 2px 8px; border-radius: 999px; font-size: 0.68rem; font-weight: 700; white-space: nowrap; }
.dtr-badge--ok { background: #e3f5ea; color: #1f6b41; }
.dtr-badge--wait { background: #fdf0d8; color: #8a5a00; }

/* ---------- Payroll details: contributions and notes ---------- */
.pay-note { margin: 8px 0 0; padding: 8px 10px; border-radius: 8px; background: #fdf0d8; color: #7a4f00; font-size: 0.72rem; font-weight: 600; }
.pay-contrib { padding: 12px 10px 14px; border-top: 1px solid #ecebea; }
.pay-contrib h3 { margin: 0 0 4px; color: #b22222; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; }
.pay-contrib p { margin: 0 0 10px; color: #6b7280; font-size: 0.72rem; }
.pay-contrib-grid { display: grid; grid-template-columns: minmax(80px, 1fr) minmax(0, 1fr) minmax(0, 1fr); gap: 6px 10px; align-items: center; }
.pay-contrib-head { font-size: 0.66rem; font-weight: 800; text-transform: uppercase; color: #6b7280; text-align: center; }
.pay-contrib-label { font-size: 0.78rem; font-weight: 700; color: #111827; }
.pay-contrib-grid input { width: 100%; height: 30px; padding: 0 8px; border: 1px solid #ecebea; border-radius: 8px; text-align: center; font-size: 0.78rem; font-weight: 700; }
.pay-contrib-grid input::placeholder { color: #9ca3af; font-weight: 500; }
.pay-contrib-grid input:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; }
.pay-contrib .contents { display: contents; }

.pay-download { display: flex; justify-content: flex-end; padding: 12px 16px 16px; }
.pay-download-btn { display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 18px; border-radius: 12px; background: var(--mg-crimson); color: #fff; font-weight: 700; font-size: 0.9rem; text-decoration: none; }
.pay-download-btn svg { width: 18px; height: 18px; }
.pay-download-btn:hover { background: var(--mg-oxblood); }
.pay-download-btn:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

/* ---------- Government remittances ---------- */
.rm { display: grid; gap: 18px; }
.rm-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; padding: 16px 20px; border: 1px solid var(--mg-line); border-radius: 20px; background: #fff; }
.rm-bar label { font-weight: 600; font-size: 0.88rem; color: var(--mg-stone); }
.rm-bar-gap { margin-left: 8px; }
.rm-bar input, .rm-bar select { height: 42px; padding: 0 12px; border: 1px solid var(--mg-line); border-radius: 10px; background-color: #fbf8f7; font-size: 0.92rem; }
.rm-bar select { padding-right: 32px; }
.rm-bar input:focus, .rm-bar select:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; }
.rm-export { margin-left: auto; height: 42px; padding: 0 18px; border: 0; border-radius: 10px; background: var(--mg-crimson); color: #fff; font-weight: 700; cursor: pointer; }
.rm-export:hover { background: var(--mg-oxblood); }
.rm-export:disabled { opacity: 0.5; cursor: default; }
.rm-totals { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
.rm-total { display: grid; gap: 4px; padding: 16px 18px; border: 1px solid var(--mg-line); border-radius: 18px; background: #fff; }
.rm-total span { font-size: 0.85rem; color: var(--mg-stone); }
.rm-total strong { font-family: var(--mg-display); font-weight: 400; font-size: 1.6rem; color: var(--mg-oxblood); }
.rm-card { overflow: hidden; border: 1px solid var(--mg-line); border-radius: 22px; background: #fff; }
.rm-card-head { padding: 20px 22px 14px; }
.rm-card-head h3 { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.5rem; color: var(--mg-oxblood); }
.rm-card-head p { margin: 4px 0 0; font-size: 0.88rem; color: var(--mg-stone); }
.rm-empty { margin: 0; padding: 30px 22px; text-align: center; color: var(--mg-stone); }
.rm-wrap { overflow-x: auto; border-top: 1px solid var(--mg-line); }
.rm-table { width: 100%; min-width: 900px; border-collapse: collapse; font-size: 0.86rem; }
.rm-table th { padding: 9px 8px; background: var(--mg-oxblood); color: #fff; font-weight: 600; text-align: right; }
.rm-table thead tr:first-child th { text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.18); }
.rm-table td { padding: 11px 8px; border-bottom: 1px solid #f1e9e7; text-align: right; white-space: nowrap; }
.rm-table .rm-name { text-align: left; }
.rm-table td.rm-name { white-space: normal; }
.rm-table td.rm-name strong, .rm-table td.rm-name small { display: block; }
.rm-table td.rm-name small { color: var(--mg-stone); }
.rm-sum { font-weight: 700; color: var(--mg-oxblood); }
.rm-table tfoot td { background: #fbf3f2; font-weight: 800; color: var(--mg-oxblood); border-top: 2px solid var(--mg-crimson); }
@media (max-width: 900px) { .rm-totals { grid-template-columns: repeat(2, minmax(0, 1fr)); } .rm-export { margin-left: 0; } }

/* ---------- Half-day leave ---------- */
.half-day { display: grid; gap: 10px; padding: 12px 14px; border: 1px solid var(--mg-line); border-radius: 12px; background: #fbf8f7; }
.half-day-toggle { display: flex; align-items: center; gap: 10px; font-weight: 700; color: var(--mg-ink); cursor: pointer; }
.half-day-toggle small { font-weight: 500; color: var(--mg-stone); }
.half-day-toggle input, .half-day-periods input { width: 18px; height: 18px; accent-color: var(--mg-crimson); }
.half-day-periods { display: flex; flex-wrap: wrap; gap: 8px 22px; padding-left: 28px; }
.half-day-periods label { display: inline-flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; }

/* ---------- Onboarding requirements ---------- */
.rq { display: grid; gap: 18px; text-align: left; color: var(--mg-ink); }
.rq-flash { margin: 0; padding: 12px 16px; border-radius: 12px; background: #e3f5ea; color: #1f6b41; font-weight: 600; }
.rq-flash--error { background: #fde4e6; color: #8f1420; }
.rq-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
.rq-stat { display: grid; gap: 4px; padding: 16px 18px; border: 1px solid var(--mg-line); border-radius: 18px; background: #fff; }
.rq-stat strong { font-family: var(--mg-display); font-weight: 400; font-size: 2rem; line-height: 1; color: var(--mg-oxblood); }
.rq-stat strong small { font-size: 1rem; color: var(--mg-stone); }
.rq-stat span { font-size: 0.85rem; color: var(--mg-stone); }
.rq-tabs { display: flex; gap: 6px; border-bottom: 1px solid var(--mg-line); }
.rq-tabs button { position: relative; padding: 12px 12px 11px; border: 0; background: none; color: var(--mg-stone); font: 600 0.95rem var(--mg-body); cursor: pointer; }
.rq-tabs button[aria-selected="true"] { color: var(--mg-crimson); }
.rq-tabs button[aria-selected="true"]::after { content: ""; position: absolute; left: 0; right: 0; bottom: -1px; height: 3px; border-radius: 3px 3px 0 0; background: var(--mg-crimson); }
.rq-search input, .rq-filters input, .rq-filters select { width: 100%; height: 42px; padding: 0 12px; border: 1px solid var(--mg-line); border-radius: 10px; background-color: #fff; font-size: 0.92rem; }
.rq-search { display: block; max-width: 420px; margin-bottom: 12px; }
.rq-search input:focus, .rq-filters input:focus, .rq-filters select:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; }
.rq-list { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
.rq-item { display: flex; align-items: center; gap: 14px; width: 100%; padding: 12px 16px; border: 1px solid var(--mg-line); border-radius: 14px; background: #fff; text-align: left; cursor: pointer; transition: border-color 0.2s ease, transform 0.2s ease; }
.rq-item:hover { border-color: var(--mg-gold); transform: translateY(-1px); }
.rq-item:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }
.rq-num { display: grid; place-items: center; width: 32px; height: 32px; flex: none; border-radius: 50%; background: #f6efe9; color: var(--mg-oxblood); font-family: var(--mg-display); font-size: 0.95rem; }
.rq-name { display: grid; gap: 2px; flex: 1; min-width: 0; font-weight: 600; }
.rq-name small { color: var(--mg-stone); font-weight: 500; font-size: 0.8rem; }
.rq-counts { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 6px; }
.rq-chip { padding: 3px 10px; border-radius: 999px; background: #f1ecea; color: var(--mg-stone); font-size: 0.76rem; font-weight: 700; white-space: nowrap; }
.rq-chip--ok { background: #e3f5ea; color: #1f6b41; }
.rq-chip--wait { background: #fdf0d8; color: #8a5a00; }
.rq-empty { margin: 0; padding: 18px; text-align: center; color: var(--mg-stone); }
.rq-note { margin: 10px 0 0; font-size: 0.82rem; color: var(--mg-stone); }

.rq-progress { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
.rq-progress li { display: grid; grid-template-columns: minmax(180px, 1.2fr) minmax(120px, 2fr) auto; gap: 16px; align-items: center; padding: 12px 16px; border: 1px solid var(--mg-line); border-radius: 14px; background: #fff; }
.rq-progress-who { display: grid; min-width: 0; }
.rq-progress-who span { font-size: 0.82rem; color: var(--mg-stone); }
.rq-progress-num { display: flex; align-items: center; gap: 8px; justify-content: flex-end; }
.rq-progress-num strong { font-family: var(--mg-display); font-weight: 400; font-size: 1.15rem; color: var(--mg-oxblood); }
.rq-bar { height: 10px; overflow: hidden; border-radius: 999px; background: #f1e9e7; }
.rq-bar span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--mg-crimson), var(--mg-gold)); }
.rq-bar--lg { height: 14px; }

.rq-modal { max-width: 820px; padding-bottom: 18px; }
.rq-modal-head { position: relative; display: flex; align-items: center; gap: 14px; padding: 22px 64px 16px 24px; border-bottom: 1px solid var(--mg-line); }
.rq-modal-head h3 { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.35rem; line-height: 1.25; color: var(--mg-oxblood); }
.rq-modal-head .ed-close { background: #f6efe9; color: var(--mg-oxblood); }
.rq-filters { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 10px; padding: 16px 24px; }
.rq-filters label { display: grid; gap: 4px; font-size: 0.78rem; font-weight: 600; color: var(--mg-stone); }
.rq-subs { display: grid; gap: 8px; margin: 0; padding: 0 24px; list-style: none; }
.rq-subs li { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px; padding: 12px 14px; border: 1px solid var(--mg-line); border-radius: 12px; }
.rq-sub-who { display: grid; gap: 2px; min-width: 0; }
.rq-sub-who span { font-size: 0.8rem; color: var(--mg-stone); }
.rq-file { color: var(--mg-crimson); font-size: 0.85rem; font-weight: 600; text-decoration: underline; text-underline-offset: 3px; overflow-wrap: anywhere; }
.rq-sub-act, .rq-btns { display: flex; align-items: center; gap: 8px; }
.rq-btn { display: inline-flex; align-items: center; height: 36px; padding: 0 14px; border-radius: 10px; font: 700 0.85rem var(--mg-body); cursor: pointer; border: 1px solid transparent; }
.rq-btn--ok { background: #2e8b57; color: #fff; }
.rq-btn--ok:hover { background: #246d44; }
.rq-btn--no { background: #fff; border-color: #f0c6ca; color: #8f1420; }
.rq-btn--no:hover { background: #fdf0f1; }
.rq-btn--up { background: var(--mg-crimson); color: #fff; }
.rq-btn--up:hover { background: var(--mg-oxblood); }
.rq-btn:focus-within, .rq-btn:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }
.rq-missing { margin: 14px 24px 0; padding: 10px 14px; border-radius: 12px; background: #fbf8f7; font-size: 0.88rem; }
.rq-missing summary { cursor: pointer; font-weight: 600; color: var(--mg-oxblood); }
.rq-missing p { margin: 8px 0 0; color: var(--mg-stone); }

.rq-me { display: grid; gap: 10px; padding: 20px 22px; border: 1px solid var(--mg-line); border-left: 5px solid var(--mg-gold); border-radius: 20px; background: #fff; }
.rq-me h3 { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.4rem; color: var(--mg-oxblood); }
.rq-me p { margin: 2px 0 0; color: var(--mg-stone); }
.rq-mine { display: flex; align-items: center; gap: 14px; padding: 12px 16px; border: 1px solid var(--mg-line); border-radius: 14px; background: #fff; }
.rq-mine--approved { border-left: 4px solid #2e8b57; }
.rq-mine--pending { border-left: 4px solid #d9a514; }
.rq-mine-act { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 8px; }

@media (max-width: 720px) {
    .rq-stats { grid-template-columns: 1fr; }
    .rq-filters { grid-template-columns: 1fr 1fr; }
    .rq-filter-name { grid-column: 1 / -1; }
    .rq-progress li { grid-template-columns: 1fr; gap: 8px; }
    .rq-progress-num { justify-content: flex-start; }
    .rq-item, .rq-mine { flex-wrap: wrap; }
    .rq-counts, .rq-mine-act { width: 100%; justify-content: flex-start; padding-left: 46px; }
}

/* ---------- Final pay (offboarding) ---------- */
.fp { padding: 16px; border: 1px solid #f0dada; border-radius: 12px; background: #fff; text-align: left; }
.fp-head { display: flex; flex-wrap: wrap; align-items: end; justify-content: space-between; gap: 10px; margin-bottom: 12px; }
.fp-head h4 { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.25rem; color: var(--mg-oxblood); }
.fp-cause { display: grid; gap: 4px; }
.fp-cause label { font-size: 0.75rem; font-weight: 700; color: var(--mg-stone); }
.fp-cause select { height: 38px; padding: 0 30px 0 10px; border: 1px solid var(--mg-line); border-radius: 10px; font-size: 0.85rem; }
.fp-facts { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 18px; margin: 0 0 12px; }
.fp-facts dt { font-size: 0.75rem; color: var(--mg-stone); }
.fp-facts dd { margin: 2px 0 0; font-weight: 600; font-size: 0.88rem; }
.fp-facts small { font-weight: 500; color: #8a5a00; }
.fp-lines { display: grid; margin: 0; padding: 0; list-style: none; border-top: 1px solid var(--mg-line); }
.fp-lines li { display: flex; justify-content: space-between; gap: 14px; padding: 10px 0; border-bottom: 1px solid #f1e9e7; }
.fp-lines li div { display: grid; gap: 2px; }
.fp-lines small { color: var(--mg-stone); font-size: 0.76rem; }
.fp-lines span { font-weight: 700; white-space: nowrap; }
.fp-total strong, .fp-total span { font-size: 1rem; color: var(--mg-oxblood); }
.fp-note { margin: 10px 0 0; font-size: 0.76rem; color: var(--mg-stone); }
@media (max-width: 560px) { .fp-facts { grid-template-columns: 1fr; } }

/* ---------- Homepage CMS ---------- */
.cms { display: grid; gap: 18px; }
.cms-intro { padding: 18px 20px; border: 1px solid var(--mg-line); border-left: 5px solid var(--mg-gold); border-radius: 18px; background: #fff; }
.cms-intro p { margin: 0; color: var(--mg-ink); }
.cms-links { display: flex; flex-wrap: wrap; gap: 8px 22px; margin-top: 10px; }
.cms-links a { color: var(--mg-crimson); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
.cms-group { display: grid; gap: 14px; margin: 0; padding: 18px 20px 20px; border: 1px solid var(--mg-line); border-radius: 18px; background: #fff; }
.cms-group legend { padding: 0 6px; font-family: var(--mg-display); font-size: 1.25rem; color: var(--mg-oxblood); }
.cms-field { display: grid; gap: 6px; }
.cms-field span { font-weight: 600; font-size: 0.88rem; color: var(--mg-stone); }
.cms-field input, .cms-field textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--mg-line); border-radius: 10px; background: #fbf8f7; font: 0.95rem/1.5 var(--mg-body); color: var(--mg-ink); }
.cms-field textarea { resize: vertical; }
.cms-field input:focus, .cms-field textarea:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; background: #fff; }
.cms-error { color: #8f1420; font-weight: 600; }
.cms-actions { position: sticky; bottom: 12px; display: flex; justify-content: flex-end; }
.cms-actions button { height: 48px; padding: 0 24px; border: 0; border-radius: 12px; background: var(--mg-crimson); color: #fff; font: 700 1rem var(--mg-body); cursor: pointer; box-shadow: 0 14px 28px -16px rgba(74, 10, 18, 0.7); }
.cms-actions button:hover { background: var(--mg-oxblood); }

.al-dl { display: inline-flex; align-items: center; margin-left: 0; text-decoration: none; background: #fff; color: var(--mg-crimson); border: 1px solid var(--mg-line); }
.al-dl:hover { background: #fbf3f2; }
.al-table td, .al-table th { text-align: left; white-space: normal; vertical-align: top; }
.al-table td small { display: block; color: var(--mg-stone); }
.al-changes { max-width: 520px; color: var(--mg-stone); font-size: 0.82rem; overflow-wrap: anywhere; }
.al-action { padding: 3px 10px; border-radius: 999px; font-size: 0.76rem; font-weight: 700; background: #f1ecea; color: var(--mg-stone); white-space: nowrap; }
.al-action--created { background: #e3f5ea; color: #1f6b41; }
.al-action--updated { background: #fdf0d8; color: #8a5a00; }
.al-action--deleted { background: #fde4e6; color: #8f1420; }
.al-pages { padding: 14px 22px; }

/* ---------- Messages ---------- */
.sb-badge { display: inline-grid; place-items: center; min-width: 20px; height: 20px; margin-left: 8px; padding: 0 6px; border-radius: 999px; background: var(--mg-gold); color: var(--mg-oxblood-deep); font-size: 0.72rem; font-weight: 800; vertical-align: middle; }
.msg { display: grid; grid-template-columns: 320px minmax(0, 1fr); height: min(72vh, 760px); overflow: hidden; border: 1px solid var(--mg-line); border-radius: 22px; background: #fff; box-shadow: 0 24px 50px -40px rgba(33, 23, 26, 0.45); }
.msg-side { display: flex; flex-direction: column; min-height: 0; border-right: 1px solid var(--mg-line); background: #fbf8f7; }
.msg-find { padding: 14px; border-bottom: 1px solid var(--mg-line); }
.msg-find input { width: 100%; height: 42px; padding: 0 12px; border: 1px solid var(--mg-line); border-radius: 10px; font-size: 0.92rem; }
.msg-find input:focus, .msg-compose textarea:focus { outline: 2px solid var(--mg-gold); outline-offset: 1px; border-color: transparent; box-shadow: none; }
.msg-people, .msg-convos { flex: 1; overflow-y: auto; margin: 0; padding: 6px; list-style: none; }
.msg-people button, .msg-convos button { position: relative; display: grid; gap: 2px; width: 100%; padding: 10px 12px; border: 0; border-radius: 12px; background: none; text-align: left; cursor: pointer; }
.msg-people button:hover, .msg-convos button:hover { background: #fff; }
.msg-convos button.is-active { background: #fff; box-shadow: inset 3px 0 0 var(--mg-crimson); }
.msg-people small, .msg-who small { color: var(--mg-stone); font-size: 0.76rem; }
.msg-who { display: flex; justify-content: space-between; gap: 8px; padding-right: 26px; }
.msg-last { color: var(--mg-stone); font-size: 0.84rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding-right: 26px; }
.msg-unread { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); display: grid; place-items: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px; background: var(--mg-crimson); color: #fff; font-size: 0.72rem; }
.msg-empty { padding: 14px; color: var(--mg-stone); font-size: 0.88rem; }
.msg-main { display: flex; min-width: 0; min-height: 0; }
.msg-main > div { flex: 1; }
.msg-placeholder { display: grid; place-content: center; gap: 6px; padding: 30px; text-align: center; }
.msg-placeholder h3 { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.5rem; color: var(--mg-oxblood); }
.msg-placeholder p { margin: 0; max-width: 420px; color: var(--mg-stone); }
.msg-thread { display: flex; flex-direction: column; min-height: 0; height: 100%; }
.msg-thread-head { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--mg-line); }
.msg-thread-head div { display: grid; }
.msg-thread-head small { color: var(--mg-stone); font-size: 0.8rem; }
.msg-back { display: none; width: 36px; height: 36px; place-items: center; border: 0; border-radius: 50%; background: #f6efe9; color: var(--mg-oxblood); cursor: pointer; }
.msg-back svg { width: 18px; height: 18px; }
.msg-list { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; margin: 0; padding: 18px; list-style: none; background: linear-gradient(#fff, #fdfaf9); }
.msg-list li:not(.msg-empty) { align-self: flex-start; max-width: min(75%, 560px); padding: 10px 14px; border-radius: 16px 16px 16px 4px; background: #f3eeec; }
.msg-list li.is-mine { align-self: flex-end; border-radius: 16px 16px 4px 16px; background: var(--mg-oxblood); color: #fff; }
.msg-list p { margin: 0; white-space: pre-wrap; overflow-wrap: anywhere; }
.msg-list time { display: block; margin-top: 4px; font-size: 0.7rem; opacity: 0.7; }
.msg-compose { display: flex; gap: 10px; padding: 12px 14px; border-top: 1px solid var(--mg-line); }
.msg-compose textarea { flex: 1; min-height: 44px; max-height: 140px; padding: 10px 12px; border: 1px solid var(--mg-line); border-radius: 12px; font: 0.95rem/1.4 var(--mg-body); resize: vertical; }
.msg-compose button { height: 44px; padding: 0 20px; border: 0; border-radius: 12px; background: var(--mg-crimson); color: #fff; font: 700 0.95rem var(--mg-body); cursor: pointer; }
.msg-compose button:disabled { opacity: 0.5; cursor: default; }
.msg-error { margin: 0; padding: 0 16px 12px; color: #8f1420; font-size: 0.85rem; font-weight: 600; }
@media (max-width: 860px) {
    .msg { grid-template-columns: 1fr; height: 76vh; }
    .msg.is-open .msg-side { display: none; }
    .msg:not(.is-open) .msg-main { display: none; }
    .msg-back { display: grid; }
}

/* ---------- Proctoring days (HR) ---------- */
.proc-form { display: grid; grid-template-columns: 1fr; gap: 14px; }
@media (min-width: 640px) { .proc-form { grid-template-columns: 1fr 1.4fr auto; align-items: end; } .proc-form [role="alert"] { grid-column: 1 / -1; } }
.proc-btn { height: 46px; padding: 0 18px; border: 0; border-radius: 10px; background: #6b3fa0; color: #fff; font-weight: 700; cursor: pointer; white-space: nowrap; }
.proc-btn:hover { background: #563283; }
.proc-btn:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }
.proc-item { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 12px 14px; border: 1px solid #e4dcf1; border-left: 4px solid #6b3fa0; border-radius: 10px; background: #f6f1fc; }

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
.dtr-swatch--proctoring { background: #efe6fa; box-shadow: inset 3px 0 0 #6b3fa0; }
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
.dtr-table { width: 100%; min-width: 860px; border-collapse: collapse; font-size: 0.84rem; }
.dtr-table th { padding: 10px 6px; line-height: 1.25; background: var(--mg-oxblood); color: #fff; font-weight: 600; text-align: left; white-space: normal; }
.dtr-table td { padding: 11px 6px; border-bottom: 1px solid #f1e9e7; color: var(--mg-ink); white-space: nowrap; }
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
.dtr-row--proctoring td { background: #f2eafb; }
.dtr-row--proctoring .dtr-row td:first-child { box-shadow: inset 4px 0 0 #6b3fa0; }
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
    position: relative; width: 100%; max-width: 840px; max-height: 92vh; overflow-y: auto; scrollbar-width: none;
    border-radius: 26px; background: #fff; box-shadow: 0 40px 80px -30px rgba(33, 10, 14, 0.6);
}
.ed-head {
    position: relative; display: flex; align-items: center; gap: 28px; padding: 36px 40px 32px; color: #fff;
    background:
        radial-gradient(380px 220px at 100% 0%, rgba(201, 162, 75, 0.28), transparent 70%),
        radial-gradient(300px 200px at 0% 100%, rgba(165, 18, 30, 0.6), transparent 70%),
        linear-gradient(150deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
}
.ed-face { width: 116px; height: 116px; flex: none; }
.ed-who { min-width: 0; }
.ed-name { margin: 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.9rem; line-height: 1.1; color: #fff; }
.ed-role { margin: 4px 0 0; color: var(--mg-gold-soft); font-size: 1rem; }
.ed-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
.ed-chip { padding: 5px 14px; border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 999px; font-size: 0.8rem; font-weight: 600; color: rgba(255, 255, 255, 0.9); }
.ed-chip--gold { border-color: transparent; background: var(--mg-gold); color: var(--mg-oxblood-deep); }
.ed-close {
    position: absolute; top: 14px; right: 14px; display: grid; place-items: center; width: 36px; height: 36px;
    border: 0; border-radius: 50%; background: rgba(255, 255, 255, 0.12); color: #fff; cursor: pointer;
}
.ed-close svg { width: 18px; height: 18px; }
.ed-close:hover { background: rgba(255, 255, 255, 0.22); }
.ed-close:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: 2px; }

.ed-tabs { display: flex; gap: 22px; overflow-x: auto; padding: 0 40px; border-bottom: 1px solid var(--mg-line); scrollbar-width: none; }
.ed-tabs button {
    position: relative; flex: none; padding: 18px 4px 16px; border: 0; background: none; color: var(--mg-stone);
    font: 600 0.95rem var(--mg-body); cursor: pointer;
}
.ed-tabs button[aria-selected="true"] { color: var(--mg-crimson); }
.ed-tabs button[aria-selected="true"]::after { content: ""; position: absolute; left: 8px; right: 8px; bottom: -1px; height: 3px; border-radius: 3px 3px 0 0; background: var(--mg-crimson); }
.ed-tabs button:focus-visible { outline: 2px solid var(--mg-gold); outline-offset: -2px; border-radius: 8px; }

.ed-body { padding: 32px 40px 16px; min-height: 220px; }
.ed-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 26px 40px; margin: 0; }
.ed-grid > div { min-width: 0; }
.ed-grid dt { font-size: 0.84rem; color: var(--mg-stone); }
.ed-grid dd { margin: 6px 0 0; font-size: 1.02rem; font-weight: 600; color: var(--mg-ink); overflow-wrap: anywhere; }
.ed-wide { grid-column: 1 / -1; }
.ed-note { margin: 0 0 20px; font-size: 0.9rem; color: var(--mg-stone); }
.ed-ids { display: grid; gap: 14px; margin: 0; padding: 0; list-style: none; }
.ed-ids li { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 22px; border: 1px solid var(--mg-line); border-radius: 14px; background: #fbf8f7; font-size: 1rem; }
.ed-ids-label { font-weight: 600; }
.ed-muted { font-style: italic; color: var(--mg-stone); }

.ed-edu { display: grid; gap: 6px; margin: 24px 0 0; padding: 0; list-style: none; border-left: 2px solid var(--mg-line); }
.ed-edu li { position: relative; display: grid; grid-template-columns: 170px minmax(0, 1fr) auto; gap: 6px 22px; align-items: baseline; padding: 14px 0 14px 24px; }
.ed-edu li::before { content: ""; position: absolute; left: -6px; top: 20px; width: 10px; height: 10px; border-radius: 50%; background: var(--mg-gold); box-shadow: 0 0 0 3px #fff; }
.ed-edu-level { font-weight: 700; color: var(--mg-oxblood); }
.ed-edu-detail { color: var(--mg-ink); }
.ed-edu-years { color: var(--mg-stone); font-size: 0.88rem; white-space: nowrap; }
@media (max-width: 560px) { .ed-edu li { grid-template-columns: 1fr; } }

.ed-foot { display: flex; justify-content: flex-end; gap: 12px; padding: 24px 40px 32px; }
.ed-btn { height: 46px; padding: 0 24px; border-radius: 12px; font: 700 0.92rem var(--mg-body); cursor: pointer; transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease; }
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
    .ed-tabs { gap: 18px; padding: 0 20px; }
    .ed-body { padding: 24px 20px 12px; }
    .ed-ids li { padding: 16px 18px; }
    .ed-grid { grid-template-columns: 1fr; }
    .ed-foot { padding: 16px 20px 22px; }
    .ed-foot > * { flex: 1; }
    .ed-foot .ed-btn { width: 100%; }
}

/* ---------- Page changes ----------
   Moving between pages keeps the sidebar and top bar still and only fades the
   content (browsers without view transitions simply load the page as before). */
@view-transition { navigation: auto; }
#sidebar { view-transition-name: smms-sidebar; }
::view-transition-old(root), ::view-transition-new(root) { animation-duration: 0.18s; }
@media (prefers-reduced-motion: reduce) { @view-transition { navigation: none; } }

/* ---------- Motion ---------- */
@media (prefers-reduced-motion: no-preference) {
    .dx-hero-copy > *, .dx-actions { animation: dx-rise 0.7s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
    .dx-hero-copy > :nth-child(2) { animation-delay: 0.06s; }
    .dx-hero-copy > :nth-child(3) { animation-delay: 0.12s; }
    .dx-actions { animation-delay: 0.18s; }
    .dx-stat { animation: dx-rise 0.7s cubic-bezier(0.2, 0.7, 0.2, 1) both; animation-delay: calc(0.2s + var(--i, 0) * 80ms); }
    .dx-hero::before { animation: dx-ring 9s ease-in-out infinite alternate; }

    html.sb-intro .app-side-link { animation: dx-slide 0.5s cubic-bezier(0.2, 0.7, 0.2, 1) both; }
    html.sb-intro #sidebar nav > a:nth-child(2) { animation-delay: 0.03s; }
    html.sb-intro #sidebar nav > a:nth-child(3) { animation-delay: 0.06s; }
    html.sb-intro #sidebar nav > a:nth-child(4) { animation-delay: 0.09s; }
    html.sb-intro #sidebar nav > a:nth-child(5) { animation-delay: 0.12s; }
    html.sb-intro #sidebar nav > a:nth-child(6) { animation-delay: 0.15s; }
    html.sb-intro #sidebar nav > a:nth-child(7) { animation-delay: 0.18s; }
    html.sb-intro #sidebar nav > a:nth-child(8) { animation-delay: 0.21s; }
    html.sb-intro #sidebar nav > a:nth-child(9) { animation-delay: 0.24s; }
    html.sb-intro #sidebar nav > a:nth-child(n+10) { animation-delay: 0.27s; }
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
