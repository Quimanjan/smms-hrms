@extends('layouts.home')

@section('title', $jobOpening->title . ' | Job Opening')

@section('styles')
@include('partials.mg-theme')
@verbatim
<style>
/* Job opening detail page */
.detail-banner {
    margin: clamp(-48px, -4vw, -28px) 0 0;
    position: relative;
    z-index: 1;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 30px 60px -30px rgba(74, 10, 18, 0.5);
}
.detail-banner img { display: block; width: 100%; height: auto; }
@media (prefers-reduced-motion: no-preference) {
    .detail-banner img { animation: mg-banner-zoom 14s ease-in-out infinite alternate; }
}
@keyframes mg-banner-zoom { from { transform: scale(1); } to { transform: scale(1.04); } }
.detail-body { padding-top: clamp(36px, 5vw, 64px) !important; }

.detail-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 0.95rem;
}
.detail-status::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: #6fd08c; }
.detail-status.is-closed::before { background: #f0a3a3; }

.detail-tags { display: flex; flex-wrap: wrap; align-items: center; gap: 12px 18px; margin-top: 22px; }
.detail-cta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }

.detail-body {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.8fr);
    gap: clamp(28px, 5vw, 72px);
    align-items: start;
    padding-block: clamp(40px, 6vw, 80px) clamp(64px, 8vw, 104px);
}

.detail-section + .detail-section { margin-top: 44px; }
.detail-section .mg-h2::after { content: ''; display: block; width: 56px; height: 2px; margin-top: 14px; background: var(--mg-gold); }
.detail-section p { max-width: 64ch; margin: 18px 0 0; color: #3b2f32; white-space: pre-line; }

.detail-list { list-style: none; margin: 18px 0 0; padding: 0; }
.detail-list li { position: relative; max-width: 64ch; padding: 10px 0 10px 30px; border-bottom: 1px solid var(--mg-line); color: #3b2f32; }
.detail-list li::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 19px;
    width: 9px;
    height: 9px;
    transform: rotate(45deg);
    background: var(--mg-gold);
}

.detail-aside { position: sticky; top: calc(var(--mg-bar) + 24px); display: grid; gap: 18px; }

.detail-panel { padding: 26px; border: 1px solid var(--mg-line); border-radius: 18px; background: var(--mg-mist); }
.detail-panel h2 { margin: 0 0 6px; font-family: var(--mg-display); font-weight: 400; font-size: 1.3rem; color: var(--mg-oxblood); }
.detail-panel dl { margin: 0; }
.detail-panel dl div { display: flex; justify-content: space-between; gap: 16px; padding: 11px 0; border-bottom: 1px solid var(--mg-line); }
.detail-panel dl div:last-child { border-bottom: 0; padding-bottom: 0; }
.detail-panel dt { color: var(--mg-stone); }
.detail-panel dd { margin: 0; font-weight: 600; text-align: right; }
.detail-panel ul { margin: 8px 0 0; padding: 0; list-style: none; }
.detail-panel ul li { padding: 10px 0; border-bottom: 1px solid var(--mg-line); }
.detail-panel ul li:last-child { border-bottom: 0; padding-bottom: 0; }
.detail-panel ul small { display: block; color: var(--mg-stone); font-size: 0.88rem; }

.detail-panel--apply { border: 0; color: #fff; background: linear-gradient(160deg, var(--mg-oxblood) 0%, var(--mg-oxblood-deep) 100%); }
.detail-panel--apply h2 { color: var(--mg-gold-soft); }
.detail-panel--apply p { margin: 6px 0 18px; color: rgba(255, 255, 255, 0.82); }
.detail-panel--apply .mg-btn { width: 100%; }

@media (max-width: 900px) {
    .detail-body { grid-template-columns: 1fr; }
    .detail-aside { position: static; }
}
</style>
@endverbatim
@endsection

@section('content')
@php
    $requirementLines = collect(preg_split('/\r\n|\r|\n/', (string) $jobOpening->requirements))
        ->map(fn ($line) => trim(ltrim(trim($line), "-*•\t ")))
        ->filter()
        ->values();

    // The school's hiring banner for this position, when there is one.
    $positionBanner = match (true) {
        str_contains(strtolower($jobOpening->title), 'senior high') => 'image/announce-senior-high.jpg',
        str_contains(strtolower($jobOpening->title), 'high school') => 'image/announce-high-school.jpg',
        str_contains(strtolower($jobOpening->title), 'elementary') => 'image/announce-elementary.jpg',
        $jobOpening->filterType() === 'non-teaching' => 'image/announce-team.jpg',
        default => 'image/announce-faculty.jpg',
    };

    // Applicants go straight to the form; visitors get the Create your account modal on this page.
    $applyUrl = auth()->check()
        ? (auth()->user()->hasRole('Applicant') ? route('applications.index', ['position' => $jobOpening->title]) : null)
        : '#signup';
@endphp

@include('partials.mg-header', ['accountButtons' => true])

<main class="mg-main">
    <section class="mg-pagehead">
        <div class="mg-wrap mg-pagehead-inner">
            <a class="mg-crumb" href="{{ route('public.job-openings') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                All job openings
            </a>
            <h1>{{ $jobOpening->title }}</h1>
            <div class="detail-tags">
                <span class="mg-pill">{{ $jobType }}</span>
                <span class="detail-status {{ $jobOpening->is_open ? '' : 'is-closed' }}">
                    {{ $jobOpening->is_open ? 'Accepting applications' : 'Not accepting applications' }}
                </span>
            </div>
            @if ($jobOpening->is_open && $applyUrl)
                <div class="detail-cta">
                    @guest
                        <button class="mg-btn mg-btn-gold" type="button" data-open-signup>Apply for this position</button>
                    @else
                        <a class="mg-btn mg-btn-gold" href="{{ $applyUrl }}">Apply for this position</a>
                    @endguest
                </div>
            @endif
        </div>
    </section>

    <div class="mg-wrap">
        <figure class="detail-banner" data-reveal>
            <img src="{{ asset($positionBanner) }}" alt="Hiring announcement for {{ $jobOpening->title }}" width="1704" height="624">
        </figure>
    </div>

    <div class="mg-wrap detail-body">
        <article>
            <section class="detail-section" data-reveal>
                <h2 class="mg-h2">Job description</h2>
                <p>{{ $jobOpening->description ?: 'A detailed description for this position has not been published yet. Please contact St. Mary Magdalene School for role-specific responsibilities and qualifications.' }}</p>
            </section>

            @if ($requirementLines->isNotEmpty())
                <section class="detail-section" data-reveal>
                    <h2 class="mg-h2">Requirements</h2>
                    <ul class="detail-list">
                        @foreach ($requirementLines as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </article>

        <aside class="detail-aside" aria-label="Position information">
            <section class="detail-panel" data-reveal>
                <h2>Position information</h2>
                <dl>
                    <div><dt>Position</dt><dd>{{ $jobOpening->title }}</dd></div>
                    <div><dt>Job type</dt><dd>{{ $jobType }}</dd></div>
                    <div><dt>Applications</dt><dd>{{ $jobOpening->is_open ? 'Open' : 'Closed' }}</dd></div>
                </dl>
            </section>

            <section class="detail-panel" data-reveal style="--i: 1">
                <h2>Documents to prepare</h2>
                <ul>
                    @foreach (\App\Models\Application::REQUIRED_SUBMISSIONS as $submission)
                        <li>{{ $submission }}<small>PDF, up to 5 MB</small></li>
                    @endforeach
                </ul>
            </section>

            @if ($jobOpening->is_open && $applyUrl)
                <section class="detail-panel detail-panel--apply" data-reveal style="--i: 2">
                    <h2>Ready to apply?</h2>
                    <p>
                        @auth
                            Your application takes a few minutes.
                        @else
                            Create an account first, then send your application online.
                        @endauth
                    </p>
                    @guest
                        <button class="mg-btn mg-btn-gold" type="button" data-open-signup>Apply for this position</button>
                    @else
                        <a class="mg-btn mg-btn-gold" href="{{ $applyUrl }}">Apply for this position</a>
                    @endguest
                </section>
            @endif
        </aside>
    </div>
</main>

@include('partials.mg-footer')

@guest
    @include('partials.public-account-modals', ['context' => 'job-openings'])
@endguest
@endsection
