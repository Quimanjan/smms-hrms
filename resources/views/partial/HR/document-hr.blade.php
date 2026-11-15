{{--
    HR Document Management: the school's fixed onboarding requirements.
    Click a requirement to see who submitted it, filter by name or date, and
    approve or reject each file. A rejected file is deleted so the employee can
    upload a new one.
--}}
<div class="rq" x-data="hrRequirements(@js($requirements), @js($reopenBox))" @keydown.escape.window="openId = null">

    @if (session('success'))
        <p class="rq-flash" role="status">{{ session('success') }}</p>
    @endif

    <div class="rq-stats" aria-label="Summary">
        <div class="rq-stat"><strong>{{ $stats['waiting'] }}</strong><span>Waiting for review</span></div>
        <div class="rq-stat"><strong>{{ $stats['approved'] }}</strong><span>Approved files</span></div>
        <div class="rq-stat"><strong>{{ $stats['complete'] }} <small>of {{ $stats['employees'] }}</small></strong><span>Employees with every required file</span></div>
    </div>

    <div class="rq-tabs" role="tablist" aria-label="Document views">
        <button type="button" role="tab" :aria-selected="(tab === 'requirements').toString()" @click="tab = 'requirements'">Requirements</button>
        <button type="button" role="tab" :aria-selected="(tab === 'progress').toString()" @click="tab = 'progress'">Onboarding progress</button>
    </div>

    {{-- Requirements --}}
    <section x-show="tab === 'requirements'" role="tabpanel" aria-label="Requirements">
        <label class="rq-search">
            <span class="sr-only">Search requirements</span>
            <input type="search" x-model="q" placeholder="Search requirements">
        </label>

        <ol class="rq-list">
            <template x-for="r in shownRequirements" :key="r.id">
                <li>
                    <button type="button" class="rq-item" @click="open(r.id)">
                        <span class="rq-num" x-text="r.number"></span>
                        <span class="rq-name">
                            <span x-text="r.name"></span>
                            <small x-show="r.optional && !/\bif /i.test(r.name)">If applicable</small>
                        </span>
                        <span class="rq-counts">
                            <span class="rq-chip rq-chip--wait" x-show="r.waiting" x-text="r.waiting + ' waiting'"></span>
                            <span class="rq-chip rq-chip--ok" x-text="r.approved + ' approved'"></span>
                        </span>
                    </button>
                </li>
            </template>
        </ol>
        <p class="rq-empty" x-show="!shownRequirements.length">No requirement matches your search.</p>
    </section>

    {{-- Onboarding progress per employee --}}
    <section x-show="tab === 'progress'" x-cloak role="tabpanel" aria-label="Onboarding progress">
        <ul class="rq-progress">
            @forelse ($progress as $p)
                @php($pct = $p['required'] ? round(100 * $p['approved'] / $p['required']) : 0)
                <li>
                    <div class="rq-progress-who">
                        <strong>{{ $p['name'] }}</strong>
                        <span>{{ $p['position'] }}{{ $p['number'] ? ', ID '.$p['number'] : '' }}</span>
                    </div>
                    <div class="rq-bar" role="img" aria-label="{{ $p['approved'] }} of {{ $p['required'] }} required documents approved">
                        <span style="width: {{ $pct }}%"></span>
                    </div>
                    <div class="rq-progress-num">
                        <strong>{{ $p['approved'] }}/{{ $p['required'] }}</strong>
                        @if ($p['waiting'])
                            <span class="rq-chip rq-chip--wait">{{ $p['waiting'] }} waiting</span>
                        @elseif ($p['approved'] >= $p['required'] && $p['required'])
                            <span class="rq-chip rq-chip--ok">Complete</span>
                        @endif
                    </div>
                </li>
            @empty
                <li class="rq-empty">No employee accounts yet.</li>
            @endforelse
        </ul>
        <p class="rq-note">Counts the {{ $progress->first()['required'] ?? 22 }} required documents. Items marked "If applicable" are not counted.</p>
    </section>

    {{-- One requirement: who submitted it --}}
    <div class="ed-overlay" x-show="current" x-cloak x-transition.opacity @click.self="openId = null">
        <div class="ed rq-modal" role="dialog" aria-modal="true" aria-labelledby="rq-title">
            <template x-if="current">
                <div>
                    <header class="rq-modal-head">
                        <span class="rq-num" x-text="current.number"></span>
                        <h3 id="rq-title" x-text="current.name"></h3>
                        <button type="button" class="ed-close" @click="openId = null" aria-label="Close">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                        </button>
                    </header>

                    <div class="rq-filters">
                        <label class="rq-filter-name">
                            <span>Employee</span>
                            <input type="search" x-model="who" placeholder="Search name or ID">
                        </label>
                        <label>
                            <span>From</span>
                            <input type="date" x-model="from">
                        </label>
                        <label>
                            <span>To</span>
                            <input type="date" x-model="to">
                        </label>
                        <label>
                            <span>Status</span>
                            <select x-model="state">
                                <option value="">All</option>
                                <option value="Pending">Waiting</option>
                                <option value="Approved">Approved</option>
                            </select>
                        </label>
                    </div>

                    <ul class="rq-subs">
                        <template x-for="s in submissions" :key="s.id">
                            <li>
                                <div class="rq-sub-who">
                                    <strong x-text="s.employee"></strong>
                                    <span x-text="(s.number ? 'ID ' + s.number + ', ' : '') + 'Uploaded ' + s.date_label"></span>
                                    <a x-show="s.url" :href="s.url" target="_blank" rel="noopener" class="rq-file" x-text="s.file + ' (' + s.size + ')'"></a>
                                </div>
                                <div class="rq-sub-act">
                                    <span class="rq-chip" :class="s.status === 'Approved' ? 'rq-chip--ok' : 'rq-chip--wait'" x-text="s.status === 'Approved' ? 'Approved' : 'Waiting'"></span>
                                    <template x-if="s.status !== 'Approved'">
                                        <div class="rq-btns">
                                            <form method="POST" action="{{ route('hr.documents.decide') }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="document_id" :value="s.id">
                                                <input type="hidden" name="decision" value="{{ \App\Models\EmployeeDocument::APPROVED }}">
                                                <button type="submit" class="rq-btn rq-btn--ok">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('hr.documents.decide') }}"
                                                data-confirm="Reject this file? It will be deleted so the employee can upload a new one."
                                                data-confirm-button="Reject">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="document_id" :value="s.id">
                                                <input type="hidden" name="decision" value="{{ \App\Models\EmployeeDocument::REJECTED }}">
                                                <button type="submit" class="rq-btn rq-btn--no">Reject</button>
                                            </form>
                                        </div>
                                    </template>
                                </div>
                            </li>
                        </template>
                    </ul>
                    <p class="rq-empty" x-show="!submissions.length"
                        x-text="current.submissions.length ? 'No submission matches these filters.' : 'No one has submitted this yet.'"></p>

                    <details class="rq-missing" x-show="current.missing.length">
                        <summary x-text="current.missing.length + ' employee' + (current.missing.length === 1 ? ' has' : 's have') + ' not submitted yet'"></summary>
                        <p x-text="current.missing.join(', ')"></p>
                    </details>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function hrRequirements(requirements, reopen) {
        return {
            requirements,
            tab: 'requirements',
            q: '',
            openId: reopen || null,
            who: '', from: '', to: '', state: '',

            get shownRequirements() {
                const q = this.q.trim().toLowerCase();
                return q ? this.requirements.filter((r) => r.name.toLowerCase().includes(q)) : this.requirements;
            },
            get current() { return this.requirements.find((r) => r.id === this.openId) || null; },
            get submissions() {
                if (!this.current) return [];
                const who = this.who.trim().toLowerCase();
                return this.current.submissions.filter((s) =>
                    (!who || (s.employee + ' ' + (s.number || '')).toLowerCase().includes(who))
                    && (!this.from || s.date >= this.from)
                    && (!this.to || s.date <= this.to)
                    && (!this.state || s.status === this.state)
                );
            },
            open(id) {
                this.who = ''; this.from = ''; this.to = ''; this.state = '';
                this.openId = id;
            },
        };
    }
</script>
