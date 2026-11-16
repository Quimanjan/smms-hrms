<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    @include('partials.quiet-transitions')
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Account' }} | St. Mary Magdalene School</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.brand-fonts')
        {{-- Password and email pages in the landing page design --}}
        @verbatim
        <style>
            :root { --brand-red: #b22222; --brand-red-dark: #8f1c1c; --brand-green: #15803d; --brand-green-dark: #166534; }
            .gl-body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; color: #1f2937;
                background: radial-gradient(1200px 600px at 20% 0%, #6b0f1a 0%, #3a0a10 55%, #24060b 100%); }
            .gl-card { width: min(460px, 100%); border-radius: 28px; background: #fff; padding: 36px 34px 30px;
                box-shadow: 0 40px 90px rgba(0, 0, 0, .35); }
            .gl-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 22px; text-decoration: none; }
            .gl-brand img { width: 52px; height: 52px; border-radius: 999px; box-shadow: 0 0 0 3px #f6e7c8; }
            .gl-brand span { font-family: 'Marcellus', serif; font-size: 1.2rem; color: #3a0a10; line-height: 1.2; }
            .gl-card .text-sm { line-height: 1.55; }
            .gl-card form > div + div, .gl-card form > .mt-4 { margin-top: 16px; }
            .gl-card .flex.items-center.justify-end { gap: 12px; flex-wrap: wrap; }
            .gl-card a.underline { color: #b22222; }
.hb-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 10px; }
.hb { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 0 18px;
      border: 1px solid transparent; border-radius: 10px; font-family: inherit; font-size: 0.9rem; font-weight: 700; line-height: 1;
      white-space: nowrap; cursor: pointer; text-decoration: none;
      transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease, transform .15s ease; }
.hb i { font-size: 0.85em; }
.hb:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(15, 23, 42, .12); }
.hb:focus-visible { outline: 2px solid var(--brand-red); outline-offset: 2px; }
.hb:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }
.hb--approve { background: var(--brand-green); border-color: var(--brand-green); color: #fff; }
.hb--approve:hover:not(:disabled) { background: var(--brand-green-dark); border-color: var(--brand-green-dark); }
.hb--approve:focus-visible { outline-color: var(--brand-green); }
.hb--reject, .hb--primary { background: var(--brand-red); border-color: var(--brand-red); color: #fff; }
.hb--reject:hover:not(:disabled), .hb--primary:hover:not(:disabled) { background: var(--brand-red-dark); border-color: var(--brand-red-dark); }
.hb--ghost { background: #fff; border-color: #e5e7eb; color: #374151; }
.hb--ghost:hover:not(:disabled) { background: #f9fafb; border-color: #d1d5db; }
.hb--muted, .hb--muted:disabled { background: #e5e7eb; border-color: #e5e7eb; color: #4b5563; opacity: 1; }
.hb--sm { min-height: 34px; padding: 0 12px; gap: 6px; border-radius: 8px; font-size: 0.78rem; }
.hrms-input { display: block; width: 100%; min-height: 46px; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 12px;
    background: #f9fafb; color: #111827; font: inherit; transition: border-color .15s, box-shadow .15s, background-color .15s; }
.hrms-input:focus { outline: none; border-color: #b22222; background: #fff; box-shadow: 0 0 0 4px rgba(178, 34, 34, .12); }
        </style>
        @endverbatim
    </head>
    <body class="font-sans text-gray-900 antialiased gl-body">
        <main class="gl-card">
            <a href="/" class="gl-brand">
                <img src="{{ asset('image/SMMS Logo.png') }}" alt="">
                <span>St. Mary Magdalene School</span>
            </a>
            {{ $slot }}
        </main>
    </body>
</html>
