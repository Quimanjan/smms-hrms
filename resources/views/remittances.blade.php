<x-app-layout>
    <x-slot name="title">Government Remittances</x-slot>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold leading-tight text-red-700 sm:text-3xl md:text-4xl">Government Remittances</h2>
    </x-slot>

    @php
        $peso = fn ($v) => '₱'.number_format((float) $v, 2);
        $exportRows = $rows->map(fn ($r) => [
            $r['name'], $r['sss_ee'], $r['sss_er'], $r['sss_ee'] + $r['sss_er'],
            $r['philhealth_ee'], $r['philhealth_er'], $r['philhealth_ee'] + $r['philhealth_er'],
            $r['pagibig_ee'], $r['pagibig_er'], $r['pagibig_ee'] + $r['pagibig_er'], $r['tax'],
        ])->values();
    @endphp

    <div class="pb-12 ml-4 -mt-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 text-gray-900">
        <section class="rm" x-data="remittances(@js($exportRows), @js($month->format('F Y')), @js('remittances-'.$month->format('Y-m').'.xlsx'))">

            <form method="GET" action="{{ route('remittances.index') }}" class="rm-bar">
                <label for="rm-month">Month</label>
                <input id="rm-month" type="month" name="month" value="{{ $month->format('Y-m') }}" onchange="this.form.submit()">
                <noscript><button type="submit">Show</button></noscript>

                <label for="rm-agency" class="rm-bar-gap">Show</label>
                <select id="rm-agency" x-model="agency">
                    <option value="all">All agencies</option>
                    <option value="sss">SSS only</option>
                    <option value="philhealth">PhilHealth only</option>
                    <option value="pagibig">Pag-IBIG only</option>
                    <option value="bir">BIR only</option>
                </select>

                <button type="button" class="rm-export" @click="excel()" @disabled($rows->isEmpty())>Download Excel</button>
            </form>

            <div class="rm-totals" aria-label="Total to remit this month">
                @foreach ($agencyTotals as [$label, $amount])
                    <div class="rm-total"><span>{{ $label }}</span><strong>{{ $peso($amount) }}</strong></div>
                @endforeach
            </div>

            <div class="rm-card">
                <header class="rm-card-head">
                    <h3>{{ $month->format('F Y') }}</h3>
                    <p>From payslips approved by Admin and HR. EE is the employee's share (taken from pay); ER is the school's share.</p>
                </header>

                @if ($rows->isEmpty())
                    <p class="rm-empty">No approved payslips for {{ $month->format('F Y') }} yet.</p>
                @else
                    <div class="rm-wrap">
                        <table class="rm-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="rm-name">Employee</th>
                                    <th colspan="3" class="rm-grp" x-show="show('sss')">SSS</th>
                                    <th colspan="3" class="rm-grp" x-show="show('philhealth')">PhilHealth</th>
                                    <th colspan="3" class="rm-grp" x-show="show('pagibig')">Pag-IBIG</th>
                                    <th rowspan="2" class="rm-grp" x-show="show('bir')">Tax withheld (BIR)</th>
                                </tr>
                                <tr>
                                    @foreach (['sss', 'philhealth', 'pagibig'] as $a)
                                        <th class="rm-grp" x-show="show('{{ $a }}')">EE</th>
                                        <th x-show="show('{{ $a }}')">ER</th>
                                        <th x-show="show('{{ $a }}')">Total</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $r)
                                    <tr>
                                        <td class="rm-name">
                                            <strong>{{ $r['name'] }}</strong>
                                            <small x-show="agency === 'sss' && @js($r['sss_no'])">SSS {{ $r['sss_no'] }}</small>
                                            <small x-show="agency === 'philhealth' && @js($r['philhealth_no'])">PhilHealth {{ $r['philhealth_no'] }}</small>
                                            <small x-show="agency === 'pagibig' && @js($r['pagibig_no'])">Pag-IBIG {{ $r['pagibig_no'] }}</small>
                                            <small x-show="agency === 'bir' && @js($r['tin'])">TIN {{ $r['tin'] }}</small>
                                        </td>
                                        @foreach (['sss', 'philhealth', 'pagibig'] as $a)
                                            <td class="rm-grp" x-show="show('{{ $a }}')">{{ $peso($r[$a.'_ee']) }}</td>
                                            <td x-show="show('{{ $a }}')">{{ $peso($r[$a.'_er']) }}</td>
                                            <td x-show="show('{{ $a }}')" class="rm-sum">{{ $peso($r[$a.'_ee'] + $r[$a.'_er']) }}</td>
                                        @endforeach
                                        <td x-show="show('bir')" class="rm-sum rm-grp">{{ $peso($r['tax']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="rm-name">Total to remit</td>
                                    @foreach (['sss', 'philhealth', 'pagibig'] as $a)
                                        <td class="rm-grp" x-show="show('{{ $a }}')">{{ $peso($totals[$a.'_ee']) }}</td>
                                        <td x-show="show('{{ $a }}')">{{ $peso($totals[$a.'_er']) }}</td>
                                        <td x-show="show('{{ $a }}')">{{ $peso($totals[$a.'_ee'] + $totals[$a.'_er']) }}</td>
                                    @endforeach
                                    <td class="rm-grp" x-show="show('bir')">{{ $peso($totals['tax']) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function remittances(rows, monthLabel, fileName) {
            const groups = {
                sss: [1, 2, 3], philhealth: [4, 5, 6], pagibig: [7, 8, 9], bir: [10],
            };
            const heads = ['Employee', 'SSS EE', 'SSS ER', 'SSS Total', 'PhilHealth EE', 'PhilHealth ER', 'PhilHealth Total',
                'Pag-IBIG EE', 'Pag-IBIG ER', 'Pag-IBIG Total', 'Tax withheld (BIR)'];

            return {
                agency: 'all',
                show(a) { return this.agency === 'all' || this.agency === a; },
                excel() {
                    if (!window.XLSX || !rows.length) return;
                    const cols = this.agency === 'all' ? heads.map((_, i) => i) : [0, ...groups[this.agency]];
                    const pick = (r) => cols.map((i) => r[i]);
                    const totals = cols.map((i) => i === 0 ? 'Total to remit' : Math.round(rows.reduce((a, r) => a + Number(r[i] || 0), 0) * 100) / 100);
                    const sheet = XLSX.utils.aoa_to_sheet([
                        ['St. Mary Magdalene School: Government Remittances'],
                        ['Month', monthLabel],
                        [],
                        pick(heads),
                        ...rows.map(pick),
                        totals,
                    ]);
                    const book = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(book, sheet, 'Remittances');
                    XLSX.writeFile(book, fileName);
                },
            };
        }
    </script>
</x-app-layout>
