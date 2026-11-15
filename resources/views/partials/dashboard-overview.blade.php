{{-- Welcome band, live figures, and shortcuts at the top of the staff dashboards. --}}
@php($overview = \App\Support\DashboardOverview::for(auth()->user()))

<section class="dx-hero" aria-labelledby="dx-greeting">
    <canvas class="dx-motes" aria-hidden="true"></canvas>
    <div class="dx-hero-inner">
        <div class="dx-hero-copy">
            <p class="dx-date">{{ $overview['date'] }}</p>
            <h2 id="dx-greeting" class="dx-greeting">{{ $overview['greeting'] }}</h2>
            <p class="dx-lede">Here is what needs your attention at St. Mary Magdalene School today.</p>
        </div>
        @if ($overview['actions'])
            <div class="dx-actions">
                @foreach ($overview['actions'] as $action)
                    <a class="dx-action" href="{{ $action['url'] }}">
                        {{ $action['label'] }}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if ($overview['cards'])
    <section class="dx-stats" aria-label="At a glance">
        @foreach ($overview['cards'] as $card)
            <a class="dx-stat" @if ($card['url']) href="{{ $card['url'] }}" @endif style="--i: {{ $loop->index }}">
                <span class="dx-stat-icon"><i class="fa-solid {{ $card['icon'] }}" aria-hidden="true"></i></span>
                <span class="dx-stat-value" data-count="{{ $card['value'] }}">{{ $card['value'] }}</span>
                <span class="dx-stat-label">{{ $card['label'] }}</span>
                <span class="dx-stat-note">{{ $card['note'] }}</span>
            </a>
        @endforeach
    </section>
@endif

<script>
(function () {
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Figures count up once, when the page opens. */
    document.querySelectorAll('.dx-stat-value[data-count]').forEach((el) => {
        const target = Number(el.dataset.count || 0);
        if (calm || target === 0) return;
        const started = performance.now();
        const tick = (now) => {
            const t = Math.min(1, (now - started) / 1200);
            el.textContent = Math.round(target * (1 - Math.pow(1 - t, 3)));
            if (t < 1) requestAnimationFrame(tick);
        };
        el.textContent = '0';
        requestAnimationFrame(tick);
    });

    /* Points of gold light drift across the welcome band. */
    const band = document.querySelector('.dx-hero');
    const canvas = band && band.querySelector('.dx-motes');
    if (!canvas || calm) return;
    const ctx = canvas.getContext('2d');
    let motes = [], w = 0, h = 0, frame = 0, running = false;

    const size = () => {
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        w = band.clientWidth; h = band.clientHeight;
        canvas.width = w * ratio; canvas.height = h * ratio;
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        motes = Array.from({ length: Math.round(Math.max(16, Math.min(46, w / 26))) }, () => ({
            x: Math.random() * w, y: Math.random() * h, r: 0.6 + Math.random() * 1.6,
            vy: 0.06 + Math.random() * 0.22, sway: Math.random() * 6.28,
            swaySpeed: 0.004 + Math.random() * 0.01, glow: 0.25 + Math.random() * 0.5,
        }));
    };

    const draw = () => {
        if (!running) return;
        ctx.clearRect(0, 0, w, h);
        motes.forEach((m) => {
            m.y -= m.vy; m.sway += m.swaySpeed; m.x += Math.sin(m.sway) * 0.22;
            if (m.y < -6) { m.y = h + 6; m.x = Math.random() * w; }
            ctx.beginPath();
            ctx.arc(m.x, m.y, m.r, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(230, 207, 154, ' + (m.glow * (0.65 + 0.35 * Math.sin(m.sway * 2.3))).toFixed(3) + ')';
            ctx.fill();
        });
        frame = requestAnimationFrame(draw);
    };

    size();
    window.addEventListener('resize', size);
    new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting && !document.hidden) { if (!running) { running = true; draw(); } }
        else { running = false; cancelAnimationFrame(frame); }
    }).observe(band);
})();
</script>
