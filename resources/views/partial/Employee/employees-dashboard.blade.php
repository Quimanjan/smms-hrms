{{-- resources/views/dashboard/employee.blade.php --}}
  @auth
    <x-slot name="title">
        @if (auth()->user()->hasRole('Employee'))
            Employee Dashboard
        @endif
    </x-slot>

  
@endauth
<x-slot name="header">
    <h1 class="text-2xl sm:text-3xl md:text-4xl  lg:text-4xl font-extrabold leading-tight sm:ml-[10px] md:ml-[20px]  lg:ml-[80px] text-red-700">Dashboard</h1>
</x-slot>
    {{-- Browser tab title --}}

    <div
        class="mr-auto ml-0 max-w-[1920px] px-4 pb-12 pt-6 sm:px-8 lg:ml-[50px] lg:px-12"
        x-data="employeeDashboard(@js([
            'slides' => $slides,
            'today'  => $today,
            'routes' => [
                'tasks' => route('tasks.index'),
                'banners' => route('dashboard.banners.current'),
            ],
        ]))"
    >

        {{-- Page heading --}}
        <h1 class="mb-5 text-[clamp(2rem,3vw,3.2rem)] font-extrabold leading-[0.98] tracking-[-0.04em] text-[#b22222]"></h1>

        {{-- ============ Promo banner (read-only) ============ --}}
        <section class="relative mb-6 overflow-hidden p-4" aria-label="Promotions">
            <div class="relative overflow-hidden rounded-[20px]">
                <template x-for="(s, i) in slides" :key="i">
                    <div x-show="i === current" class="relative aspect-[16/6.4] min-h-[300px] overflow-hidden rounded-[20px] bg-white">
                        <img :src="s.url" :alt="s.alt" class="h-full w-full object-cover object-center saturate-[1.02] contrast-[1.02]">
                    </div>
                </template>
            </div>

            <div class="absolute bottom-[26px] left-1/2 z-[2] flex -translate-x-1/2 gap-2.5">
                <template x-for="(s, i) in slides" :key="'dot' + i">
                    <button type="button" @click="current = i" :aria-label="'Go to slide ' + (i + 1)"
                        class="h-[11px] cursor-pointer rounded-full transition-all duration-200"
                        :class="i === current ? 'w-[15px] bg-[#b22222] sm:w-[22px]' : 'w-[11px] bg-white'"></button>
                </template>
            </div>
            <button type="button" x-show="slides.length > 1" x-cloak @click="paused = !paused"
                :aria-label="paused ? 'Resume banner rotation' : 'Pause banner rotation'"
                :aria-pressed="paused"
                :title="paused ? 'Resume banner rotation' : 'Pause banner rotation'"
                class="absolute right-8 top-8 z-[3] grid h-10 w-10 cursor-pointer place-items-center rounded-full border border-white/60 bg-black/50 p-0 text-white shadow hover:bg-black/70">
                <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                    <path :d="paused
                        ? 'M7 4.8v14.4a1 1 0 0 0 1.5.86l11-7.2a1 1 0 0 0 0-1.72l-11-7.2A1 1 0 0 0 7 4.8z'
                        : 'M6 4h4v16H6z M14 4h4v16h-4z'"></path>
                </svg>
            </button>
        </section>

        <div class="grid items-stretch gap-5">

            {{-- ============ Calendar with personal tasks ============ --}}
            {{-- Same layout as the Admin calendar: month grid plus the selected-day panel --}}
            <section class="cx" aria-label="Calendar and tasks">
                <div class="cx-main">
                    <header class="cx-head">
                        <h2 id="calendarHeading" class="cx-month" x-text="monthLabel" aria-live="polite"></h2>
                        <div class="cx-nav">
                            <button type="button" class="cx-today" @click="goToday()">Today</button>
                            <button type="button" class="cx-arrow" @click="shiftMonth(-1)" aria-label="Previous month">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                            </button>
                            <button type="button" class="cx-arrow" @click="shiftMonth(1)" aria-label="Next month">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                            </button>
                        </div>
                    </header>

                    <div class="cx-week" aria-hidden="true">
                        <template x-for="d in weekdays" :key="d"><span x-text="d"></span></template>
                    </div>

                    <div class="cx-grid" :class="slide" role="grid">
                        <template x-for="(c, i) in cells" :key="monthLabel + i">
                            <div class="contents">
                                <template x-if="c === null"><span class="cx-day cx-day--empty" aria-hidden="true"></span></template>
                                <template x-if="c !== null">
                                    <button type="button" @click="selectDay(c.iso)" :style="'--d:' + c.day" :aria-pressed="c.iso === selected"
                                        :class="dayClasses(c)"
                                        :aria-label="dayLabel(c)">
                                        <span class="cx-num" x-text="c.day"></span>
                                        <template x-if="holiday(c.iso)"><span class="cx-tag" x-text="holiday(c.iso).name"></span></template>
                                        <template x-if="taskCount(c.iso) > 0">
                                            <span class="cx-count" aria-hidden="true">
                                                <template x-for="n in Math.min(taskCount(c.iso), 3)" :key="n"><i></i></template>
                                                <template x-if="taskCount(c.iso) > 3"><b x-text="'+' + (taskCount(c.iso) - 3)"></b></template>
                                            </span>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    <ul class="cx-legend" aria-label="Legend">
                        <li><span class="cx-dot cx-dot--regular"></span>Regular holiday</li>
                        <li><span class="cx-dot cx-dot--special"></span>Special non-working day</li>
                        <li><span class="cx-dot cx-dot--school"></span>School day</li>
                        <li><span class="cx-dot cx-dot--task"></span>Your tasks</li>
                    </ul>
                </div>

                <aside class="cx-agenda" :class="flash && 'cx-agenda--flash'" aria-label="Selected day">
                    <div class="cx-selected">
                        <span class="cx-selected-num" x-text="selDate.getDate()"></span>
                        <span class="cx-selected-text">
                            <span x-text="selDate.toLocaleDateString('en-US', { weekday: 'long' })"></span>
                            <span x-text="selDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })"></span>
                        </span>
                    </div>
                    <template x-if="holiday(selected)">
                        <p class="cx-holiday" :class="'cx-holiday--' + holiday(selected).type">
                            <strong x-text="holiday(selected).name"></strong><span x-text="typeLabel[holiday(selected).type]"></span>
                        </p>
                    </template>

                    <h3 class="cx-sub">Tasks</h3>
                    <ul class="cx-tasks">
                        <template x-for="t in tasksForDate" :key="t.id">
                            <li><span x-text="t.title"></span><button type="button" @click="removeTask(t)" :aria-label="'Remove task: ' + t.title">Remove</button></li>
                        </template>
                        <li class="cx-empty" x-show="tasksForDate.length === 0">Nothing planned. Add a task below.</li>
                    </ul>
                    <form class="cx-add" @submit.prevent="addTask()">
                        <label class="sr-only" for="cx-new-employee">New task</label>
                        <input id="cx-new-employee" type="text" x-model="newTask" maxlength="255" placeholder="Add a task for this day" autocomplete="off">
                        <button type="submit" :disabled="saving">Add</button>
                    </form>
                    <p class="mt-2 text-xs font-semibold text-[#fecaca]" x-show="error" x-text="error"></p>

                    <h3 class="cx-sub">Coming up</h3>
                    <ul class="cx-upcoming">
                        <template x-for="(it, n) in upcoming" :key="n">
                            <li><time x-text="it.d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })"></time><span class="cx-dot" :class="'cx-dot--' + it.type"></span><span x-text="it.text"></span></li>
                        </template>
                        <li class="cx-empty" x-show="upcoming.length === 0">No holidays or tasks in the next few weeks.</li>
                    </ul>
                    <p class="cx-footnote">Holiday dates follow the usual Philippine calendar. Check each year's official proclamation for changes.</p>
                </aside>
            </section>

            {{-- ============ Upcoming birthdays ============ --}}
            <aside aria-labelledby="birthdaysHeading" class="rounded-[22px] bg-white p-[22px] shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
                <div class="mb-5 flex items-start justify-between gap-3">
                    <div>
                        
                        <h2 id="birthdaysHeading" class="dp-title">Upcoming birthdays</h2>
                    </div>
                    <i class="fa-solid fa-cake-candles flex h-14 w-14 shrink-0 items-center justify-center text-[40px] leading-none text-[#b22222]" aria-hidden="true"></i>
                </div>

                @if (count($birthdays) === 0)
                    <div class="grid justify-items-center gap-2 rounded-[14px] bg-[#fff7f7] px-4 py-[34px] text-center text-gray-400">
                        <i class="fa-regular fa-calendar text-2xl text-[#b22222]" aria-hidden="true"></i>
                        <p class="text-[0.85rem]">No upcoming employee birthdays.</p>
                    </div>
                @else
                    <div class="grid max-h-[520px] gap-2.5 overflow-y-auto [-ms-overflow-style:none] [scrollbar-width:none]
           [&::-webkit-scrollbar]:hidden">
                        @foreach ($birthdays as $i => $b)
                            <article @class([
                                'grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3',
                                '-mx-2.5 rounded-xl bg-[#fff7f7] px-2.5 py-3' => $i === 0,
                                'border-b border-[#f1f1f1] py-3 last:border-b-0' => $i !== 0,
                            ])>
                                <div class="av-host av-size-44" aria-hidden="true">{!! $b['avatar'] ?? '' !!}</div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-[0.9rem] font-extrabold text-gray-900">{{ $b['name'] }}</h3>
                                    <p class="mt-[3px] truncate text-[0.75rem] text-gray-400">{{ $b['role'] }}</p>
                                </div>
                                <time datetime="{{ $b['iso'] }}" class="whitespace-nowrap text-[0.8rem] font-extrabold text-[#b22222]">{{ $b['date_label'] }}</time>
                            </article>
                        @endforeach
                    </div>
                @endif
            </aside>
        </div>

    </div>

    @include('partials.ph-holidays')

    <script>
        function employeeDashboard(cfg) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const pad = (n) => String(n).padStart(2, '0');

            const readError = async (res) => {
                const json = await res.json().catch(() => ({}));
                if (json.errors) return Object.values(json.errors)[0][0];
                return json.message || 'Something went wrong. Please try again.';
            };

            return {
                // ---- banner ----
                slides: cfg.slides,
                current: 0,
                paused: false,

                // ---- calendar ----
                weekdays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                today: cfg.today,
                selected: cfg.today,
                year: 0,
                month: 0,
                tasks: {},          // { '2026-10-19': [ {id, title, date}, ... ] }

                // ---- tasks modal ----
                taskDate: null,
                newTask: '',
                error: '',
                saving: false,

                init() {
                    const [y, m] = cfg.today.split('-').map(Number);
                    this.year = y;
                    this.month = m - 1;
                    this.refreshBanners();

                    // Auto-advance the banner every 4.5 seconds
                    setInterval(() => {
                        if (!this.paused && this.slides.length > 1) {
                            this.current = (this.current + 1) % this.slides.length;
                        }
                    }, 2000);

                    this.loadTasks();
                },

                async refreshBanners() {
                    try {
                        const response = await fetch(cfg.routes.banners, {
                            headers: { 'Accept': 'application/json' },
                            cache: 'no-store',
                        });
                        if (!response.ok) throw new Error(`Banner refresh failed with status ${response.status}.`);
                        const data = await response.json();
                        if (!Array.isArray(data.slides)) throw new Error('Banner refresh returned an invalid response.');
                        const changed = data.slides.length !== this.slides.length
                            || data.slides.some((slide, index) => {
                                const current = this.slides[index];
                                return !current || slide.id !== current.id || slide.url !== current.url || slide.alt !== current.alt;
                            });
                        if (changed) {
                            this.slides = data.slides;
                            this.current = 0;
                        }
                    } catch (error) {
                        console.warn('Unable to refresh dashboard banners; retrying.', error);
                    } finally {
                        window.setTimeout(() => this.refreshBanners(), 3000);
                    }
                },

                // ===== Calendar =====
                get selectedLabel() {
                    return new Date(this.selected + 'T00:00:00').toLocaleDateString('en-US', {
                        month: 'long', day: 'numeric', year: 'numeric',
                    });
                },

                get cells() {
                    const firstWeekday = new Date(this.year, this.month, 1).getDay();
                    const daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                    const out = [];
                    for (let i = 0; i < firstWeekday; i++) out.push(null);
                    for (let d = 1; d <= daysInMonth; d++) {
                        out.push({ day: d, iso: `${this.year}-${pad(this.month + 1)}-${pad(d)}` });
                    }
                    return out;
                },

                taskCount(iso) { return (this.tasks[iso] || []).length; },

                slide: '',

                get monthLabel() {
                    return new Date(this.year, this.month, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },

                holiday(iso) { return window.smmsHolidayOn ? window.smmsHolidayOn(new Date(iso + 'T00:00:00')) : null; },

                dayClasses(c) {
                    const d = new Date(c.iso + 'T00:00:00');
                    const h = this.holiday(c.iso);
                    return {
                        'cx-day': true,
                        'cx-day--weekend': d.getDay() === 0 || d.getDay() === 6,
                        'cx-day--today': c.iso === this.today,
                        'cx-day--selected': c.iso === this.selected,
                        'cx-day--past': c.iso < this.today,
                        ['cx-day--' + (h ? h.type : 'plain')]: true,
                    };
                },

                dayLabel(c) {
                    const h = this.holiday(c.iso), n = this.taskCount(c.iso);
                    return new Date(c.iso + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })
                        + (h ? ', ' + h.name : '') + (n ? ', ' + n + (n === 1 ? ' task' : ' tasks') : '');
                },

                shiftMonth(step) {
                    const d = new Date(this.year, this.month + step, 1);
                    this.year = d.getFullYear();
                    this.month = d.getMonth();
                    this.slide = '';
                    this.$nextTick(() => { this.slide = step > 0 ? 'cx-grid--next' : 'cx-grid--prev'; });
                    this.loadTasks();
                },

                goToday() {
                    const [y, m] = this.today.split('-').map(Number);
                    this.selected = this.today;
                    if (y !== this.year || m - 1 !== this.month) { this.year = y; this.month = m - 1; this.loadTasks(); }
                },

                async loadTasks() {
                    const month = `${this.year}-${pad(this.month + 1)}`;
                    try {
                        const res = await fetch(`${cfg.routes.tasks}?month=${month}`, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) throw new Error('failed');
                        const map = {};
                        (await res.json()).forEach((t) => { (map[t.date] = map[t.date] || []).push(t); });
                        this.tasks = map;
                    } catch (e) {
                        this.tasks = {};
                    }
                },

                // ===== Tasks modal =====
                get taskDateLabel() {
                    return this.taskDate
                        ? new Date(this.taskDate + 'T00:00:00').toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
                        : '';
                },

                get tasksForDate() { return this.tasks[this.selected] || []; },

                // ===== Selected-day panel (same as the Admin calendar) =====
                flash: false,
                typeLabel: { regular: 'Regular holiday', special: 'Special non-working day', school: 'School day' },
                get selDate() { return new Date(this.selected + 'T00:00:00'); },

                selectDay(iso) {
                    this.selected = iso;
                    this.newTask = '';
                    this.error = '';
                    this.flash = false;
                    this.$nextTick(() => { this.flash = true; });
                },

                // Next 45 days: holidays and saved tasks (up to five).
                get upcoming() {
                    const items = [];
                    const base = new Date(this.today + 'T00:00:00');
                    for (let n = 1; n <= 45 && items.length < 5; n++) {
                        const d = new Date(base.getFullYear(), base.getMonth(), base.getDate() + n);
                        const iso = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
                        const h = this.holiday(iso);
                        if (h) items.push({ d, text: h.name, type: h.type });
                        (this.tasks[iso] || []).forEach((t) => items.push({ d, text: t.title, type: 'task' }));
                    }
                    return items.slice(0, 5);
                },

                async addTask() {
                    const title = this.newTask.trim();
                    if (!title) return;

                    this.saving = true;
                    this.error = '';
                    try {
                        const res = await fetch(cfg.routes.tasks, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ title, task_date: this.selected }),
                        });
                        if (!res.ok) { this.error = await readError(res); return; }

                        const t = await res.json();
                        this.tasks = { ...this.tasks, [t.date]: [...(this.tasks[t.date] || []), t] };
                        this.newTask = '';
                    } catch (e) {
                        this.error = 'Could not save the task. Please try again.';
                    } finally {
                        this.saving = false;
                    }
                },

                // Used by both the "Remove" button and the checkbox (task completed)
                async removeTask(t) {
                    try {
                        const res = await fetch(`${cfg.routes.tasks}/${t.id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        });
                        if (!res.ok) return;
                        this.tasks = { ...this.tasks, [t.date]: (this.tasks[t.date] || []).filter((x) => x.id !== t.id) };
                    } catch (e) {
                        /* leave the task in place */
                    }
                },
            };
        }
    </script>