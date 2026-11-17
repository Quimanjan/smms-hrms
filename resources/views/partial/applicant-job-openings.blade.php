<style>
    .applicant-jobs {
        --jobs-primary: #b22222;
        --jobs-text: #172033;
        --jobs-muted: #64748b;
        color: var(--jobs-text);
        background: #f4f7fb;
    }

    .applicant-jobs *,
    .applicant-jobs *::before,
    .applicant-jobs *::after {
        box-sizing: border-box;
    }

    .applicant-jobs__layout {
        display: grid;
        width: min(1160px, calc(100% - 48px));
        grid-template-columns: 260px minmax(0, 1fr);
        gap: 36px;
        margin: 34px auto;
    }

    .applicant-jobs__filter h2 { margin: 0 0 26px; font-size: 19px; font-weight: 700; }
    .applicant-jobs__filter fieldset { margin: 0; border: 0; padding: 0; }
    .applicant-jobs__filter legend { margin-bottom: 8px; font-size: 14px; font-weight: 500; }
    .applicant-jobs__filter label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        color: #334155;
        font-size: 14px;
        cursor: pointer;
    }
    .applicant-jobs__filter input {
        width: 18px;
        height: 18px;
        margin: 0;
        accent-color: var(--jobs-primary);
    }

    .applicant-jobs__list {
        display: grid;
        align-content: start;
        gap: 14px;
        padding-top: 48px;
    }

    .applicant-jobs__card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        border: 1px solid #dce3ed;
        border-bottom: 2px solid #b22222;
        border-radius: 9px;
        padding: 16px 18px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .03);
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .applicant-jobs__card:hover {
        border-color: #b22222;
        box-shadow: 0 6px 16px rgba(15, 23, 42, .08);
        transform: translateY(-1px);
    }

    .applicant-jobs__card h3 { margin: 0; font-size: 15px; font-weight: 700; line-height: 1.5; }
    .applicant-jobs__card-title { color: inherit; text-decoration: none; }
    .applicant-jobs__card-title:hover { color: var(--jobs-primary); text-decoration: underline; }
    .applicant-jobs__card-title:focus-visible,
    .applicant-jobs__apply:focus-visible {
        outline: 3px solid rgba(178, 34, 34, .25);
        outline-offset: 2px;
    }
    .applicant-jobs__card p { margin: 6px 0 0; color: var(--jobs-muted); font-size: 13px; }
    .applicant-jobs__apply {
        display: inline-flex;
        min-height: 38px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--jobs-primary);
        border-radius: 6px;
        padding: 0 16px;
        color: #fff;
        background: var(--jobs-primary);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: background-color .15s ease, color .15s ease;
    }
    .applicant-jobs__apply:hover { color: var(--jobs-primary); background: #fff; }
    .applicant-jobs__applied {
        display: inline-flex;
        min-height: 38px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 1px solid #fecaca;
        border-radius: 6px;
        padding: 0 16px;
        color: var(--jobs-primary);
        background: #fee2e2;
        font-size: 13px;
        font-weight: 700;
    }
    .applicant-jobs__applied--progress {
        border-color: var(--jobs-primary);
        color: #fff;
        background: var(--jobs-primary);
    }
    .applicant-jobs__status {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .applicant-jobs__progress { width: min(280px, 100%); }
    .applicant-jobs__progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        color: var(--jobs-muted);
        font-size: 12px;
    }
    .applicant-jobs__progress-track {
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #fee2e2;
    }
    .applicant-jobs__progress-fill {
        height: 100%;
        border-radius: inherit;
        background: var(--jobs-primary);
        transition: width .2s ease;
    }
    .applicant-jobs__empty {
        border: 1px solid #dce3ed;
        border-radius: 9px;
        padding: 24px;
        color: var(--jobs-muted);
        background: #fff;
        font-size: 14px;
    }
    .applicant-jobs [hidden] { display: none !important; }

    @media (max-width: 700px) {
        .applicant-jobs__layout {
            width: calc(100% - 28px);
            grid-template-columns: 1fr;
            gap: 18px;
            margin: 24px auto;
        }
        .applicant-jobs__filter h2 { margin-bottom: 14px; }
        .applicant-jobs__list { padding-top: 0; }
        .applicant-jobs__card { flex-direction: column; gap: 12px; }
        .applicant-jobs__status { width: 100%; justify-content: space-between; }
    }

    /* Modern look, same as the homepage */
    .applicant-jobs { background: #faf6f3; --jobs-text: #2a1a1a; --jobs-muted: #7a6464; }
    .applicant-jobs__filter { align-self: start; position: sticky; top: 90px; background: #fff; border: 1px solid #efe3e3; border-radius: 18px; padding: 22px 20px; box-shadow: 0 10px 30px rgba(92,15,15,.06); }
    .applicant-jobs__filter h2 { font-family: Marcellus, serif; font-weight: 400; font-size: 22px; color: #5c0f0f; margin-bottom: 16px; }
    .applicant-jobs__filter legend { font-size: 12px; font-weight: 600; color: #7a6464; margin-bottom: 10px; }
    .applicant-jobs__filter fieldset { display: flex; flex-wrap: wrap; gap: 8px; }
    .applicant-jobs__filter label { position: relative; margin: 0; padding: 8px 16px; border: 1px solid #ead9d9; border-radius: 999px; background: #fff; color: #5c0f0f; font-weight: 600; font-size: 13.5px; transition: background .15s, color .15s, border-color .15s; }
    .applicant-jobs__filter label:hover { border-color: #b22222; }
    .applicant-jobs__filter input { position: absolute; opacity: 0; pointer-events: none; }
    .applicant-jobs__filter label:has(input:checked) { background: #b22222; border-color: #b22222; color: #fff; }
    .applicant-jobs__filter label:has(input:focus-visible) { outline: 2px solid #b22222; outline-offset: 2px; }
    .applicant-jobs__list { padding-top: 0; gap: 12px; }
    .applicant-jobs__card { align-items: center; border: 1px solid #efe3e3; border-left: 4px solid #b22222; border-bottom: 1px solid #efe3e3; border-radius: 16px; padding: 20px 22px; box-shadow: 0 6px 20px rgba(92,15,15,.05); transition: transform .15s, box-shadow .15s; }
    .applicant-jobs__card:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(92,15,15,.10); }
    .applicant-jobs__card h3 { font-family: Marcellus, serif; font-weight: 400; font-size: 19px; color: #2a1a1a; }
    .applicant-jobs__card-title:hover { color: #b22222; text-decoration: none; }
    .applicant-jobs__card p { color: #3d7a45; font-weight: 600; font-size: 12.5px; }
    .applicant-jobs__apply { border-radius: 999px; padding: 10px 22px; background: #b22222; border-color: #b22222; color: #fff; }
    .applicant-jobs__apply:hover { background: #8f1c1c; color: #fff; }
    @media (max-width: 700px) { .applicant-jobs__filter { position: static; } }
</style>

<section class="applicant-jobs" data-applicant-job-openings aria-label="Job openings">
    <div class="applicant-jobs__layout">
        <aside class="applicant-jobs__filter">
            <h2>Search for Jobs</h2>
            <fieldset>
                <legend>Job Types</legend>
                <label>
                    <input type="checkbox" value="teaching" data-job-type-filter>
                    <span>Teaching</span>
                </label>
                <label>
                    <input type="checkbox" value="non-teaching" data-job-type-filter>
                    <span>Non-Teaching</span>
                </label>
            </fieldset>
        </aside>

        <div class="applicant-jobs__list">
            @forelse ($jobOpenings as $job)
                <article class="applicant-jobs__card" data-applicant-job-card data-job-type="{{ $job->filterType() === 'teaching-related' ? 'teaching' : $job->filterType() }}">
                    <div>
                        <h3>
                            <a class="applicant-jobs__card-title" href="{{ route('public.job-openings.show', $job) }}">
                                {{ $job->title }}
                            </a>
                        </h3>
                        <p>{{ $job->is_open ? 'Accepting applications' : 'Not accepting applications' }}</p>
                    </div>
                    @if ($application && $application->position === $job->title)
                        @php
                            $interviewCompleted = (bool) $application->interview_completed_at;
                            $decisionComplete = in_array($application->status, [
                                ...\App\Models\Application::APPROVED_STATUSES,
                                ...\App\Models\Application::REJECTED_STATUSES,
                            ], true);
                            $completedSteps = 2 + (int) $interviewCompleted + (int) $decisionComplete;
                            $progressPercent = (int) round($completedSteps / 4 * 100);
                        @endphp
                        <div class="applicant-jobs__status">
                            <span class="applicant-jobs__applied applicant-jobs__applied--progress">In Progress</span>
                            <div class="applicant-jobs__progress">
                                <div class="applicant-jobs__progress-label">
                                    <span>Application progress</span>
                                    <span>{{ $progressPercent }}%</span>
                                </div>
                                <div class="applicant-jobs__progress-track"
                                    role="progressbar"
                                    aria-label="Application progress for {{ $job->title }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    aria-valuenow="{{ $progressPercent }}">
                                    <div class="applicant-jobs__progress-fill" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>
                        </div>
                    @elseif ($application)
                        <span class="applicant-jobs__applied">You have already applied for a position</span>
                    @elseif ($job->is_open)
                        <a class="applicant-jobs__apply"
                            href="{{ route('applications.index', ['position' => $job->title]) }}">
                            Apply Now
                        </a>
                    @else
                        <span class="applicant-jobs__applied">Applications Closed</span>
                    @endif
                </article>
            @empty
                <p class="applicant-jobs__empty">There are no job openings available right now.</p>
            @endforelse
            <p class="applicant-jobs__empty" data-applicant-no-job-results hidden>No matching positions found.</p>
        </div>
    </div>
</section>

<script>
    (() => {
        const container = document.querySelector('[data-applicant-job-openings]');
        if (!container) return;

        const filters = Array.from(container.querySelectorAll('[data-job-type-filter]'));
        const cards = Array.from(container.querySelectorAll('[data-applicant-job-card]'));
        const noResults = container.querySelector('[data-applicant-no-job-results]');

        function filterOpenings() {
            const selectedTypes = filters.filter((filter) => filter.checked).map((filter) => filter.value);
            let visibleCount = 0;

            cards.forEach((card) => {
                const visible = selectedTypes.length === 0 || selectedTypes.includes(card.dataset.jobType);
                card.hidden = !visible;
                if (visible) visibleCount++;
            });

            if (noResults) noResults.hidden = selectedTypes.length === 0 || visibleCount > 0 || cards.length === 0;
        }

        filters.forEach((filter) => filter.addEventListener('change', filterOpenings));
        filterOpenings();
    })();
</script>
