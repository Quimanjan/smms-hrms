@extends('layouts.home')

@section('title', $jobOpening->title . ' | Job Opening')

@section('styles')
<style>
    :root {
        --primary: #b22222;
        --text: #172033;
        --muted: #64748b;
        --page: #f4f7fb;
        --border: #dce3ed;
    }

    * { box-sizing: border-box; }

    body {
        min-height: 100vh;
        margin: 0;
        padding: 90px 20px 36px;
        color: var(--text);
        background: var(--page);
        font-family: Inter, system-ui, Segoe UI, Roboto, Arial, sans-serif;
    }

    .detail-topbar {
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

    .detail-brand {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        color: var(--primary);
        font-size: clamp(18px, 2.2vw, 30px);
        font-weight: 800;
        text-decoration: none;
    }

    .detail-brand img { width: 60px; height: 60px; object-fit: contain; }
    .detail-actions { display: flex; align-items: center; gap: 10px; }

    .detail-button {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--primary);
        border-radius: 6px;
        padding: 0 14px;
        color: #fff;
        background: var(--primary);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: background-color .15s ease, color .15s ease;
    }

    .detail-button:hover { color: var(--primary); background: #fff; }
    .detail-button--secondary { color: #334155; border-color: #cbd5e1; background: #fff; }
    .detail-button--secondary:hover { color: var(--primary); border-color: var(--primary); }

    .detail-page { width: min(1120px, 100%); margin: 26px auto 0; }
    .detail-crumb { margin: 0 0 10px; color: #475569; font-size: 13px; }
    .detail-grid { display: grid; grid-template-columns: minmax(0, 1fr) 330px; align-items: start; gap: 16px; }
    .detail-card { border: 1px solid var(--border); border-radius: 8px; background: #fff; }

    .detail-main { padding: 22px; }
    .detail-main h1 { margin: 0; font-size: clamp(20px, 2.5vw, 28px); line-height: 1.35; }
    .detail-meta { display: flex; flex-wrap: wrap; gap: 8px 18px; margin-top: 14px; color: #52647e; font-size: 13px; }
    .detail-meta span { display: inline-flex; align-items: center; gap: 7px; }
    .detail-status { display: inline-block; margin-top: 14px; border-radius: 999px; padding: 5px 10px; color: #166534; background: #f0fdf4; font-size: 12px; font-weight: 700; }
    .detail-section { margin-top: 22px; border-top: 1px solid #e8edf3; padding-top: 18px; }
    .detail-section h2 { margin: 0 0 8px; font-size: 16px; }
    .detail-section p { margin: 0; color: #475569; font-size: 14px; line-height: 1.65; }

    .detail-side { display: grid; gap: 12px; }
    .detail-side-card { overflow: hidden; }
    .detail-side-card h2 { margin: 0; border-bottom: 1px solid var(--border); padding: 12px 14px; font-size: 14px; }
    .detail-side-card ul { display: grid; gap: 0; margin: 0; padding: 0; list-style: none; }
    .detail-side-card li { border-bottom: 1px solid var(--border); padding: 11px 14px; color: #475569; font-size: 12px; line-height: 1.5; }
    .detail-side-card li:last-child { border-bottom: 0; }
    .detail-side-card li strong { display: block; margin-bottom: 3px; color: var(--text); font-size: 12px; }
    .detail-side-actions { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 14px; }

    @media (max-width: 760px) {
        body { padding: 78px 14px 24px; }
        .detail-topbar { padding: 10px 14px; }
        .detail-brand { gap: 8px; font-size: 16px; }
        .detail-brand img { width: 46px; height: 46px; }
        .detail-actions { gap: 6px; }
        .detail-button { min-height: 38px; padding: 0 10px; font-size: 12px; }
        .detail-page { margin-top: 20px; }
        .detail-grid { grid-template-columns: 1fr; }
        .detail-main { padding: 17px; }
        .detail-side-actions { justify-content: flex-start; }
    }
</style>
@endsection

@section('content')
<header class="detail-topbar">
    <a class="detail-brand" href="{{ route('home') }}" aria-label="St. Mary Magdalene School home">
        <img src="{{ asset('Image/SMMS Logo.png') }}" alt="">
        <span>St. Mary Magdalene School</span>
    </a>
    <nav class="detail-actions" aria-label="Account">
        @auth
            <a class="detail-button" href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a class="detail-button" href="{{ route('home', ['login' => 1]) }}">Log In</a>
            <a class="detail-button" href="{{ route('home', ['signup' => 1]) }}">Sign Up</a>
        @endauth
    </nav>
</header>

<main class="detail-page">
    <div class="detail-side-actions">
        <a class="detail-button detail-button--secondary" href="{{ route('public.job-openings') }}">Back to Job Openings</a>
        @if ($jobOpening->is_open)
            @auth
                @if (auth()->user()->hasRole('Applicant'))
                <a class="detail-button" href="{{ route('applications.index', ['position' => $jobOpening->title]) }}">Quick Apply</a>
                @endif
            @else
                <a class="detail-button" href="{{ route('home', ['signup' => 1]) }}">Quick Apply</a>
            @endauth
        @endif
    </div>

    <div class="detail-grid">
        <article class="detail-card detail-main">
            <h1>{{ $jobOpening->title }}</h1>
            <div class="detail-meta">
                <span>{{ $jobType }}</span>
                <span>St. Mary Magdalene School</span>
            </div>
            <span class="detail-status" style="{{ $jobOpening->is_open ? '' : 'color:#991b1b;background:#fef2f2;' }}">
                {{ $jobOpening->is_open ? 'Accepting applications' : 'Not accepting applications' }}
            </span>

            <section class="detail-section">
                <h2>Job Description</h2>
                <p>{{ $jobOpening->description ?: 'A detailed description for this position has not been published yet. Please contact St. Mary Magdalene School for role-specific responsibilities and qualifications.' }}</p>
            </section>
        </article>

        <aside class="detail-side" aria-label="Position information">
            <section class="detail-card detail-side-card">
                <h2>Position Information</h2>
                <ul>
                    <li><strong>Position</strong>{{ $jobOpening->title }}</li>
                    <li><strong>Job Type</strong>{{ $jobType }}</li>
                    <li><strong>Application Status</strong>{{ $jobOpening->is_open ? 'Open' : 'Closed' }}</li>
                </ul>
            </section>

            <section class="detail-card detail-side-card">
                <h2>Required Documents</h2>
                <ul>
                    @foreach (\App\Models\Application::REQUIRED_SUBMISSIONS as $submission)
                        <li>{{ $submission }} (PDF, max 5 MB)</li>
                    @endforeach
                </ul>
            </section>
        </aside>
    </div>
</main>
@endsection
