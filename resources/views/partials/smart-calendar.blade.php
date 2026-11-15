{{--
    Dashboard calendar with an agenda panel.

    - Month view with previous / next / today navigation.
    - Philippine holidays and school days are marked on the grid.
    - Tasks are kept in this browser (localStorage) under $storageKey, the same
      key and date format the earlier calendar used, so existing tasks carry over.
--}}
@php($storageKey = $storageKey ?? 'adminDashboardTasks')

@include('partials.ph-holidays')

<section class="cx" data-cx data-storage="{{ $storageKey }}" aria-label="Calendar and tasks">
    <div class="cx-main">
        <header class="cx-head">
            <h2 class="cx-month" data-cx-month aria-live="polite"></h2>
            <div class="cx-nav">
                <button type="button" class="cx-today" data-cx-today>Today</button>
                <button type="button" class="cx-arrow" data-cx-prev aria-label="Previous month">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                </button>
                <button type="button" class="cx-arrow" data-cx-next aria-label="Next month">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                </button>
            </div>
        </header>

        <div class="cx-week" aria-hidden="true">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $d)
                <span>{{ $d }}</span>
            @endforeach
        </div>
        <div class="cx-grid" data-cx-grid role="grid"></div>

        <ul class="cx-legend" aria-label="Legend">
            <li><span class="cx-dot cx-dot--regular"></span>Regular holiday</li>
            <li><span class="cx-dot cx-dot--special"></span>Special non-working day</li>
            <li><span class="cx-dot cx-dot--school"></span>School day</li>
            <li><span class="cx-dot cx-dot--task"></span>Your tasks</li>
        </ul>
    </div>

    <aside class="cx-agenda" aria-label="Selected day">
        <div class="cx-selected">
            <span class="cx-selected-num" data-cx-sel-num></span>
            <span class="cx-selected-text">
                <span data-cx-sel-weekday></span>
                <span data-cx-sel-month></span>
            </span>
        </div>
        <p class="cx-holiday" data-cx-holiday hidden></p>

        <h3 class="cx-sub">Tasks</h3>
        <ul class="cx-tasks" data-cx-tasks></ul>
        <form class="cx-add" data-cx-add>
            <label class="sr-only" for="cx-new-{{ $storageKey }}">New task</label>
            <input id="cx-new-{{ $storageKey }}" type="text" maxlength="120" placeholder="Add a task for this day" autocomplete="off">
            <button type="submit">Add</button>
        </form>

        <h3 class="cx-sub">Coming up</h3>
        <ul class="cx-upcoming" data-cx-upcoming></ul>
        <p class="cx-footnote">Holiday dates follow the usual Philippine calendar. Check each year's official proclamation for changes.</p>
    </aside>
</section>

<script>
(function () {
    const root = document.currentScript.previousElementSibling;
    if (!root || !root.matches('[data-cx]')) return;

    const key = root.dataset.storage;
    const $ = (sel) => root.querySelector(sel);
    const grid = $('[data-cx-grid]');
    const monthLabel = $('[data-cx-month]');

    let tasks = [];
    try { tasks = JSON.parse(localStorage.getItem(key) || '[]'); } catch (e) { tasks = []; }
    const save = () => { try { localStorage.setItem(key, JSON.stringify(tasks)); } catch (e) {} };

    // Same date key format as the earlier calendar, so saved tasks still match.
    const dayKey = (d) => d.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
    const sameDay = (a, b) => dayKey(a) === dayKey(b);
    const escapeHtml = (v) => { const div = document.createElement('div'); div.textContent = v; return div.innerHTML; };

    const holidayOn = window.smmsHolidayOn;
    const typeLabel = { regular: 'Regular holiday', special: 'Special non-working day', school: 'School day' };

    /* ---- State ---- */
    const today = new Date();
    let view = new Date(today.getFullYear(), today.getMonth(), 1);
    let selected = new Date(today.getFullYear(), today.getMonth(), today.getDate());

    function renderGrid(direction) {
        const year = view.getFullYear(), month = view.getMonth();
        monthLabel.textContent = view.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        const first = new Date(year, month, 1).getDay();
        const total = new Date(year, month + 1, 0).getDate();
        let html = '';
        for (let i = 0; i < first; i++) html += '<span class="cx-day cx-day--empty" aria-hidden="true"></span>';

        for (let day = 1; day <= total; day++) {
            const date = new Date(year, month, day);
            const k = dayKey(date);
            const hol = holidayOn(date);
            const count = tasks.filter((t) => t.date === k).length;
            const classes = ['cx-day'];
            if (date.getDay() === 0 || date.getDay() === 6) classes.push('cx-day--weekend');
            if (sameDay(date, today)) classes.push('cx-day--today');
            if (sameDay(date, selected)) classes.push('cx-day--selected');
            if (hol) classes.push('cx-day--' + hol.type);
            if (date < new Date(today.getFullYear(), today.getMonth(), today.getDate())) classes.push('cx-day--past');

            const label = date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })
                + (hol ? ', ' + hol.name : '') + (count ? ', ' + count + ' task' + (count === 1 ? '' : 's') : '');

            html += `<button type="button" class="${classes.join(' ')}" data-day="${day}" aria-label="${escapeHtml(label)}" aria-pressed="${sameDay(date, selected)}" style="--d:${day}">`
                + `<span class="cx-num">${day}</span>`
                + (hol ? `<span class="cx-tag">${escapeHtml(hol.name)}</span>` : '')
                + (count ? `<span class="cx-count" aria-hidden="true">${Array.from({ length: Math.min(count, 3) }, () => '<i></i>').join('')}${count > 3 ? '<b>+' + (count - 3) + '</b>' : ''}</span>` : '')
                + '</button>';
        }

        grid.classList.remove('cx-grid--next', 'cx-grid--prev');
        void grid.offsetWidth;
        if (direction) grid.classList.add(direction > 0 ? 'cx-grid--next' : 'cx-grid--prev');
        grid.innerHTML = html;
    }

    function renderAgenda() {
        $('[data-cx-sel-num]').textContent = selected.getDate();
        $('[data-cx-sel-weekday]').textContent = selected.toLocaleDateString('en-US', { weekday: 'long' });
        $('[data-cx-sel-month]').textContent = selected.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        const hol = holidayOn(selected);
        const holEl = $('[data-cx-holiday]');
        holEl.hidden = !hol;
        if (hol) {
            holEl.className = 'cx-holiday cx-holiday--' + hol.type;
            holEl.innerHTML = `<strong>${escapeHtml(hol.name)}</strong><span>${typeLabel[hol.type]}</span>`;
        }

        const k = dayKey(selected);
        const list = $('[data-cx-tasks]');
        const dayTasks = tasks.map((t, i) => ({ ...t, i })).filter((t) => t.date === k);
        list.innerHTML = dayTasks.length
            ? dayTasks.map((t) => `<li><span>${escapeHtml(t.title)}</span><button type="button" data-remove="${t.i}" aria-label="Remove task: ${escapeHtml(t.title)}">Remove</button></li>`).join('')
            : '<li class="cx-empty">Nothing planned. Add a task below.</li>';

        // Next 45 days: holidays and saved tasks.
        const items = [];
        for (let n = 1; n <= 45 && items.length < 5; n++) {
            const d = new Date(today.getFullYear(), today.getMonth(), today.getDate() + n);
            const h = holidayOn(d);
            if (h) items.push({ d, text: h.name, type: h.type });
            tasks.filter((t) => t.date === dayKey(d)).forEach((t) => items.push({ d, text: t.title, type: 'task' }));
        }
        $('[data-cx-upcoming]').innerHTML = items.length
            ? items.slice(0, 5).map((it) => `<li><time>${it.d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</time><span class="cx-dot cx-dot--${it.type}"></span><span>${escapeHtml(it.text)}</span></li>`).join('')
            : '<li class="cx-empty">No holidays or tasks in the next few weeks.</li>';
    }

    const render = (direction) => { renderGrid(direction); renderAgenda(); };

    grid.addEventListener('click', (e) => {
        const cell = e.target.closest('[data-day]');
        if (!cell) return;
        selected = new Date(view.getFullYear(), view.getMonth(), Number(cell.dataset.day));
        render();
        root.querySelector('.cx-agenda').classList.remove('cx-agenda--flash');
        void root.offsetWidth;
        root.querySelector('.cx-agenda').classList.add('cx-agenda--flash');
    });

    $('[data-cx-prev]').addEventListener('click', () => { view = new Date(view.getFullYear(), view.getMonth() - 1, 1); render(-1); });
    $('[data-cx-next]').addEventListener('click', () => { view = new Date(view.getFullYear(), view.getMonth() + 1, 1); render(1); });
    $('[data-cx-today]').addEventListener('click', () => {
        view = new Date(today.getFullYear(), today.getMonth(), 1);
        selected = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        render();
    });

    $('[data-cx-add]').addEventListener('submit', (e) => {
        e.preventDefault();
        const input = e.currentTarget.querySelector('input');
        const title = input.value.trim();
        if (!title) return;
        tasks.push({ date: dayKey(selected), title });
        save();
        input.value = '';
        render();
        input.focus();
    });

    $('[data-cx-tasks]').addEventListener('click', (e) => {
        const btn = e.target.closest('[data-remove]');
        if (!btn) return;
        tasks.splice(Number(btn.dataset.remove), 1);
        save();
        render();
    });

    render();
})();
</script>
