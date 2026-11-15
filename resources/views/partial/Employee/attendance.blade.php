{{-- resources/views/employee/attendance.blade.php --}}
  @auth
    <x-slot name="title">
        @if (auth()->user()->hasRole('Employee'))
            Employee Attendance
       @endif
    </x-slot>

  
@endauth
    <x-slot name="header">
        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-4xl font-extrabold leading-tight sm:ml-[10px] md:ml-[20px] lg:ml-[80px] text-red-700">Attendance</h1>
    </x-slot>

    @php
        $daysPresent = rtrim(rtrim(number_format($summary['present'], 1), '0'), '.');
        $stats = [
            ['Days present', $daysPresent],
            ['Days late', $summary['late']],
            ['Late minutes', $summary['late_minutes']],
            ['Days absent', $summary['absent']],
            ['Half days', $summary['half_day']],
            ['Hours worked', number_format($summary['hours'], 2)],
        ];
        $fileName = 'DTR-'.$from->format('Y-m-d').'-to-'.$to->format('Y-m-d').'.xlsx';
    @endphp

    <div class="mx-auto max-w-[1920px] px-4 pb-12 pt-2 sm:px-6 lg:px-10">

        @if (! $employee)
            <div class="rounded-[20px] bg-white p-10 text-center text-gray-500 shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
                <p class="font-semibold">Your account is not linked to an employee record yet.</p>
                <p class="mt-1 text-sm">Please contact HR.</p>
            </div>
        @else
            <section class="dtr" aria-labelledby="dtr-title"
                x-data="dtrSheet(@js($rows), @js($fileName), @js($employee->name ?? ''), @js($from->format('M d, Y').' to '.$to->format('M d, Y')))">

                {{-- Pick the period --}}
                <form method="GET" action="{{ route('attendance.attendance-employee.index') }}" class="dtr-steps">
                    <div class="dtr-step">
                        <span class="dtr-step-num" aria-hidden="true">1</span>
                        <label for="dtr-from">Start date</label>
                        <input id="dtr-from" type="date" name="from" value="{{ $from->toDateString() }}" required>
                    </div>
                    <div class="dtr-step">
                        <span class="dtr-step-num" aria-hidden="true">2</span>
                        <label for="dtr-to">End date</label>
                        <input id="dtr-to" type="date" name="to" value="{{ $to->toDateString() }}" required>
                    </div>
                    <div class="dtr-step dtr-step--go">
                        <span class="dtr-step-num" aria-hidden="true">3</span>
                        <button type="submit">Generate timesheet</button>
                    </div>
                    @error('to') <p class="dtr-error">{{ $message }}</p> @enderror
                    <p class="dtr-hint">Up to {{ $maxDays }} days at a time.</p>
                </form>

                {{-- Summary for the period --}}
                <div class="dtr-stats" aria-label="Summary for this period">
                    @foreach ($stats as [$label, $value])
                        <div class="dtr-stat"><strong>{{ $value }}</strong><span>{{ $label }}</span></div>
                    @endforeach
                </div>

                <div class="dtr-card">
                    <header class="dtr-card-head">
                        <div>
                            <h2 id="dtr-title" class="dtr-title">Daily Time Record</h2>
                            <p class="dtr-period">{{ $from->format('F j, Y') }} to {{ $to->format('F j, Y') }}</p>
                        </div>
                        <ul class="dtr-legend" aria-label="Row colors">
                            <li><span class="dtr-swatch dtr-swatch--rest"></span>Rest day</li>
                            <li><span class="dtr-swatch dtr-swatch--holiday"></span>Holiday</li>
                            <li><span class="dtr-swatch dtr-swatch--special"></span>Special day</li>
                            <li><span class="dtr-swatch dtr-swatch--leave"></span>Leave</li>
                        </ul>
                    </header>

                    <div class="dtr-toolbar">
                        <div class="dtr-actions">
                            <button type="button" @click="copy()" x-text="copied ? 'Copied' : 'Copy'"></button>
                            <button type="button" @click="excel()">Excel</button>
                        </div>
                        <label class="dtr-show">Show
                            <select x-model.number="perPage" @change="page = 1">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="31">31</option>
                                <option value="0">All</option>
                            </select>
                            rows
                        </label>
                        <label class="dtr-search">
                            <span class="sr-only">Search the timesheet</span>
                            <input type="search" x-model.debounce.150ms="q" @input="page = 1" placeholder="Search date, day, or type">
                        </label>
                    </div>

                    <div class="dtr-table-wrap">
                        <table class="dtr-table">
                            <thead>
                                <tr>
                                    <th class="dtr-col-x"><span class="sr-only">Details</span></th>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Shift Type</th>
                                    <th>Shift In</th>
                                    <th>Shift Out</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Required Hours</th>
                                    <th>Tardiness</th>
                                    <th>Undertime</th>
                                </tr>
                            </thead>
                            <template x-for="row in visible" :key="row.date">
                                <tbody :class="'dtr-row--' + row.type">
                                    <tr class="dtr-row">
                                        <td class="dtr-col-x">
                                            <button type="button" class="dtr-expand" @click="toggle(row.date)"
                                                :aria-expanded="open[row.date] ? 'true' : 'false'"
                                                :aria-label="(open[row.date] ? 'Hide' : 'Show') + ' details for ' + row.date_label"
                                                x-text="open[row.date] ? '−' : '+'"></button>
                                        </td>
                                        <td class="dtr-date" x-text="row.date_label"></td>
                                        <td x-text="row.day"></td>
                                        <td><span class="dtr-type" x-text="row.shift_type"></span></td>
                                        <td x-text="row.shift_in"></td>
                                        <td x-text="row.shift_out"></td>
                                        <td x-text="row.time_in"></td>
                                        <td x-text="row.time_out"></td>
                                        <td x-text="row.required"></td>
                                        <td :class="row.tardiness && 'dtr-warn'" x-text="row.tardiness"></td>
                                        <td :class="row.undertime && 'dtr-warn'" x-text="row.undertime"></td>
                                    </tr>
                                    <tr class="dtr-detail" x-show="open[row.date]" x-cloak>
                                        <td></td>
                                        <td colspan="10">
                                            <dl>
                                                <div><dt>Status</dt><dd x-text="row.status || '—'"></dd></div>
                                                <div><dt>Hours worked</dt><dd x-text="row.hours"></dd></div>
                                                <template x-if="row.lunch"><div><dt>Lunch break</dt><dd x-text="row.lunch"></dd></div></template>
                                                <template x-if="row.grace"><div><dt>Grace period</dt><dd x-text="row.grace"></dd></div></template>
                                                <template x-if="row.category"><div><dt>Holiday type</dt><dd x-text="row.category"></dd></div></template>
                                                <template x-if="row.holiday_pay"><div><dt>Holiday pay</dt><dd x-text="row.holiday_pay"></dd></div></template>
                                            </dl>
                                        </td>
                                    </tr>
                                </tbody>
                            </template>
                            <tbody x-show="!filtered.length">
                                <tr><td colspan="11" class="dtr-empty">No days match your search.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <footer class="dtr-foot">
                        <p x-text="countLabel"></p>
                        <div class="dtr-pager" x-show="pages > 1">
                            <button type="button" @click="page--" :disabled="page === 1">Previous</button>
                            <span x-text="'Page ' + page + ' of ' + pages"></span>
                            <button type="button" @click="page++" :disabled="page === pages">Next</button>
                        </div>
                    </footer>
                </div>
            </section>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function dtrSheet(rows, fileName, employeeName, period) {
            const head = ['Date', 'Day', 'Shift Type', 'Shift In', 'Shift Out', 'Time In', 'Time Out', 'Required Hours', 'Tardiness', 'Undertime', 'Hours Worked', 'Status'];
            const line = (r) => [r.date_label, r.day, r.shift_type, r.shift_in, r.shift_out, r.time_in, r.time_out, r.required, r.tardiness, r.undertime, r.hours, r.status];

            return {
                rows, q: '', page: 1, perPage: 15, open: {}, copied: false,

                get filtered() {
                    const q = this.q.trim().toLowerCase();
                    return q ? this.rows.filter((r) => line(r).join(' ').toLowerCase().includes(q)) : this.rows;
                },
                get pages() { return this.perPage ? Math.max(1, Math.ceil(this.filtered.length / this.perPage)) : 1; },
                get visible() {
                    if (!this.perPage) return this.filtered;
                    const start = (Math.min(this.page, this.pages) - 1) * this.perPage;
                    return this.filtered.slice(start, start + this.perPage);
                },
                get countLabel() {
                    const n = this.filtered.length;
                    if (!n) return 'No days to show';
                    if (!this.perPage) return 'Showing all ' + n + ' days';
                    const start = (Math.min(this.page, this.pages) - 1) * this.perPage;
                    return 'Showing ' + (start + 1) + ' to ' + Math.min(start + this.perPage, n) + ' of ' + n + ' days';
                },
                toggle(date) { this.open = { ...this.open, [date]: !this.open[date] }; },

                async copy() {
                    const text = [head, ...this.filtered.map(line)].map((r) => r.join('\t')).join('\n');
                    try { await navigator.clipboard.writeText(text); this.copied = true; setTimeout(() => (this.copied = false), 1600); } catch (e) {}
                },
                excel() {
                    if (!window.XLSX) return;
                    const sheet = XLSX.utils.aoa_to_sheet([
                        ['St. Mary Magdalene School: Daily Time Record'],
                        ['Employee', employeeName],
                        ['Period', period],
                        [],
                        head,
                        ...this.filtered.map(line),
                    ]);
                    const book = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(book, sheet, 'DTR');
                    XLSX.writeFile(book, fileName);
                },
            };
        }
    </script>
