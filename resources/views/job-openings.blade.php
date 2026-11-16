@extends('layouts.home')

@section('title', 'Job Openings | St. Mary Magdalene School')

@section('styles')
<style>
    :root {
        --primary: #b22222;
        --text: #172033;
        --muted: #64748b;
        --page: #f4f7fb;
    }

    * { box-sizing: border-box; }

    body {
        min-height: 100vh;
        margin: 0;
        padding-top: 90px;
        color: var(--text);
        background: var(--page);
        font-family: Inter, system-ui, Segoe UI, Roboto, Arial, sans-serif;
    }

    .jobs-topbar {
        position: fixed;
        z-index: 10;
        inset: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 14px 28px;
        background: linear-gradient(to bottom, rgba(229, 228, 228, .98), rgba(255, 255, 255, .98));
        box-shadow: 0 4px 12px rgba(15, 23, 42, .08);
    }

    .jobs-brand {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        color: var(--primary);
        font-size: clamp(18px, 2.2vw, 30px);
        font-weight: 800;
        text-decoration: none;
    }

    .jobs-brand img { width: 60px; height: 60px; object-fit: contain; }

    .jobs-actions { display: flex; align-items: center; gap: 14px; }

    .jobs-button {
        display: inline-flex;
        min-height: 46px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--primary);
        border-radius: 8px;
        padding: 0 20px;
        color: #fff;
        background: var(--primary);
        font-weight: 700;
        font-family: inherit;
        text-decoration: none;
        transition: background-color .15s ease, color .15s ease;
    }

    .jobs-button:hover { color: var(--primary); background: #fff; }
    button.jobs-button { cursor: pointer; }

    .jobs-layout {
        display: grid;
        width: min(1160px, calc(100% - 48px));
        grid-template-columns: 260px minmax(0, 1fr);
        gap: 36px;
        margin: 34px auto;
    }

    .jobs-filter h1 { margin: 0 0 26px; font-size: 19px; }
    .jobs-filter fieldset { margin: 0; border: 0; padding: 0; }
    .jobs-filter legend { margin-bottom: 8px; font-size: 14px; font-weight: 500; }
    .job-type-option {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        color: #334155;
        font-size: 14px;
        cursor: pointer;
    }
    .job-type-option input {
        width: 18px;
        height: 18px;
        margin: 0;
        accent-color: var(--primary);
    }

    .jobs-list { display: grid; align-content: start; gap: 14px; padding-top: 48px; }

    .job-card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        border: 1px solid #dce3ed;
        border-bottom: 2px solid #dc3c42;
        border-radius: 9px;
        padding: 16px 18px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .03);
        color: inherit;
        text-decoration: none;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .job-card[hidden],
    .jobs-empty[hidden] { display: none !important; }

    .job-card:hover, .job-card:focus-visible {
        border-color: #dc3c42;
        box-shadow: 0 6px 16px rgba(15, 23, 42, .08);
        transform: translateY(-1px);
    }

    .job-card h2 { margin: 0; font-size: 15px; line-height: 1.5; }
    .job-card-title { color: inherit; text-decoration: none; }
    .job-card-title:hover { color: var(--primary); text-decoration: underline; }
    .job-card-title:focus-visible, .job-card-action:focus-visible {
        outline: 3px solid rgba(178, 34, 34, .25);
        outline-offset: 2px;
    }
    .job-card p { margin: 6px 0 0; color: var(--muted); font-size: 13px; }
    .job-card-action { flex-shrink: 0; min-height: 38px; border-radius: 6px; padding: 0 16px; font-size: 13px; }

    .jobs-empty {
        border: 1px solid #dce3ed;
        border-radius: 9px;
        padding: 24px;
        color: var(--muted);
        background: #fff;
    }


    @media (max-width: 700px) {
        body { padding-top: 78px; }
        .jobs-topbar { padding: 10px 14px; }
        .jobs-brand { gap: 8px; font-size: 16px; }
        .jobs-brand img { width: 46px; height: 46px; }
        .jobs-actions { gap: 7px; }
        .jobs-button { min-height: 40px; padding: 0 11px; font-size: 13px; }
        .jobs-layout { width: calc(100% - 28px); grid-template-columns: 1fr; gap: 18px; margin: 24px auto; }
        .jobs-filter h1 { margin-bottom: 14px; }
        .jobs-list { padding-top: 0; }
        .job-card { flex-direction: column; gap: 12px; }
    }
</style>
@include('partials.mg-theme')
@verbatim
<style>
/* Job openings page */
.jobs-page { padding-block: clamp(36px, 5vw, 64px) clamp(64px, 8vw, 104px); }

.jobs-filter {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    margin: 0 0 32px;
    padding: 0;
    border: 0;
    position: static;
    width: auto;
    background: none;
    box-shadow: none;
}

.jobs-filter legend { float: left; margin: 0 6px 0 0; padding: 0; color: var(--mg-stone); font-size: 0.95rem; font-weight: 500; }

.jobs-chip { position: relative; display: inline-flex; cursor: pointer; }
.jobs-chip input { position: absolute; inset: 0; width: 100%; height: 100%; margin: 0; opacity: 0; cursor: pointer; }
.jobs-chip span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 44px;
    padding: 0 18px;
    border: 1px solid var(--mg-line);
    border-radius: 999px;
    background: #fff;
    color: var(--mg-ink);
    font-size: 0.95rem;
    font-weight: 500;
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}
.jobs-chip span::before {
    content: '';
    width: 9px;
    height: 9px;
    border-radius: 50%;
    border: 1.5px solid var(--mg-stone);
    transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}
.jobs-chip:hover span { border-color: var(--mg-crimson); }
.jobs-chip input:checked + span { background: var(--mg-crimson); border-color: var(--mg-crimson); color: #fff; }
.jobs-chip input:checked + span::before { background: var(--mg-gold); border-color: var(--mg-gold); transform: scale(1.15); }
.jobs-chip input:focus-visible + span { outline: 2px solid var(--mg-gold); outline-offset: 3px; }

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.job-card {
    position: relative;
    isolation: isolate;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    min-height: 230px;
    margin: 0;
    padding: 28px;
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    color: #fff;
    background: linear-gradient(160deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%);
    box-shadow: none;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.job-card[hidden] { display: none; }

.job-card::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: -1;
    background: radial-gradient(360px circle at var(--mx, 50%) var(--my, 0%), rgba(201, 162, 75, 0.4), transparent 62%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.job-card:hover::before, .job-card:focus-within::before { opacity: 1; }
.job-card:hover { transform: translateY(-4px); box-shadow: 0 26px 44px -26px rgba(74, 10, 18, 0.7); }

.job-card h2 { margin: 18px 0 0; font-family: var(--mg-display); font-weight: 400; font-size: 1.65rem; line-height: 1.15; }
.job-card .job-card-title { color: #fff; text-decoration: none; }
.job-card .job-card-title:hover { text-decoration: underline; text-decoration-color: var(--mg-gold); text-underline-offset: 5px; }

.job-card .job-card-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 10px 0 0;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.95rem;
}
.job-card .job-card-status::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: #6fd08c; }

.job-card-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: auto; padding-top: 24px; }
.job-card-actions .mg-btn { min-height: 44px; padding: 0 20px; }

.jobs-empty { grid-column: 1 / -1; margin: 0; padding: 36px; border: 1px dashed var(--mg-line); border-radius: 18px; color: var(--mg-stone); background: none; text-align: left; }
.jobs-empty[hidden] { display: none; }

@media (prefers-reduced-motion: no-preference) {
    .job-card .job-card-status::before { animation: jobs-open-dot 2.4s ease-out infinite; }
}

@keyframes jobs-open-dot {
    0% { box-shadow: 0 0 0 0 rgba(111, 208, 140, 0.6); }
    70%, 100% { box-shadow: 0 0 0 9px rgba(111, 208, 140, 0); }
}

@media (max-width: 520px) {
    .job-card-actions .mg-btn { flex: 1 1 100%; }
}
</style>
@endverbatim
@endsection

@section('content')
@include('partials.mg-header', ['accountButtons' => true])

<main class="mg-main">
    <section class="mg-pagehead">
        <div class="mg-wrap mg-pagehead-inner">
            <a class="mg-crumb" href="{{ route('home') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                Back to home
            </a>
            <h1>Job openings</h1>
            <p>
                @if ($jobOpenings->isEmpty())
                    No positions are open right now. Check back soon.
                @else
                    {{ $jobOpenings->count() }} {{ $jobOpenings->count() === 1 ? 'position is' : 'positions are' }} open for application
                    at St. Mary Magdalene School. Select one to read its details, or apply right away.
                @endif
            </p>
        </div>
    </section>

    <div class="mg-wrap jobs-page">
        <fieldset class="jobs-filter" aria-label="Filter by job type">
            <legend>Show</legend>
            <label class="jobs-chip">
                <input type="checkbox" name="job-type" value="teaching">
                <span>Teaching</span>
            </label>
            <label class="jobs-chip">
                <input type="checkbox" name="job-type" value="non-teaching">
                <span>Non-teaching</span>
            </label>
            <label class="jobs-chip">
                <input type="checkbox" name="job-type" value="teaching-related">
                <span>Teaching-related</span>
            </label>
        </fieldset>

        <section class="jobs-grid" aria-label="Available job openings">
            @forelse ($jobOpenings as $job)
                <article class="job-card" data-job-type="{{ $job->filterType() }}" data-reveal style="--i: {{ $loop->index }}">
                    <span class="mg-pill">
                        {{ match ($job->filterType()) {
                            'teaching' => 'Teaching',
                            'teaching-related' => 'Teaching-related',
                            default => 'Non-teaching',
                        } }}
                    </span>
                    <h2><a class="job-card-title" href="{{ route('public.job-openings.show', $job) }}">{{ $job->title }}</a></h2>
                    <p class="job-card-status">Accepting applications</p>
                    <div class="job-card-actions">
                        <a class="mg-btn mg-btn-ghost" href="{{ route('public.job-openings.show', $job) }}"
                            aria-label="View details for {{ $job->title }}">View details</a>
                        @auth
                            @if (auth()->user()->hasRole('Applicant'))
                                <a class="mg-btn mg-btn-gold" href="{{ route('applications.index', ['position' => $job->title]) }}">Apply now</a>
                            @else
                                <button class="mg-btn mg-btn-gold" type="button" data-open-apply-modal>Apply now</button>
                            @endif
                        @else
                            <button class="mg-btn mg-btn-gold" type="button" data-open-apply-modal>Apply now</button>
                        @endauth
                    </div>
                </article>
            @empty
                <p class="jobs-empty">There are no job openings available right now.</p>
            @endforelse
            <p class="jobs-empty" id="jobs-no-results" hidden>No positions match the selected types.</p>
        </section>
    </div>
</main>

@include('partials.mg-footer')


@include('partials.public-account-modals', ['context' => 'job-openings'])
@endsection

@section('scripts')
<script>
    const jobCards = Array.from(document.querySelectorAll('.job-card'));
    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        jobCards.forEach((card) => {
            card.addEventListener('pointermove', (event) => {
                const box = card.getBoundingClientRect();
                card.style.setProperty('--mx', (event.clientX - box.left) + 'px');
                card.style.setProperty('--my', (event.clientY - box.top) + 'px');
            });
        });
    }
    const noJobResults = document.getElementById('jobs-no-results');
    const jobTypeFilters = Array.from(document.querySelectorAll('input[name="job-type"]'));
    function filterJobOpenings() {
        const selectedTypes = jobTypeFilters
            .filter((filter) => filter.checked)
            .map((filter) => filter.value);
        let visibleCount = 0;

        jobCards.forEach((card) => {
            const matches = selectedTypes.length === 0 || selectedTypes.includes(card.dataset.jobType);
            card.hidden = !matches;
            if (matches) visibleCount++;
        });

        if (noJobResults) noJobResults.hidden = visibleCount > 0 || jobCards.length === 0;
    }

    jobTypeFilters.forEach((filter) => filter.addEventListener('change', filterJobOpenings));
    filterJobOpenings();
</script>
@endsection
