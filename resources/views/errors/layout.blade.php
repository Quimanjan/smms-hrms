{{-- Error pages in the school's design (same fonts and colours as the landing page) --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') | St. Mary Magdalene School</title>
    @include('partials.brand-fonts')
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; color: #1f2937;
               background: radial-gradient(1200px 600px at 20% 0%, #6b0f1a 0%, #3a0a10 55%, #24060b 100%); }
        .err { width: min(560px, 100%); border-radius: 28px; background: #fff; padding: 44px 40px 36px; text-align: center;
               box-shadow: 0 40px 90px rgba(0, 0, 0, .35); }
        .err img { width: 72px; height: 72px; border-radius: 999px; box-shadow: 0 0 0 4px #f6e7c8; }
        .err .err-code { margin: 18px auto 0; max-width: none; font-family: 'Marcellus', serif; font-size: 4.5rem; line-height: 1; color: #b22222; }
        .err h1 { margin: 10px 0 8px; font-size: 1.6rem; color: #3a0a10; }
        .err p { margin: 0 auto 26px; max-width: 40ch; color: #6b7280; line-height: 1.55; }
        .err-actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
        .err-actions a { display: inline-flex; align-items: center; min-height: 44px; padding: 0 22px; border-radius: 999px;
               font-weight: 700; text-decoration: none; border: 1px solid #b22222; transition: background-color .15s, color .15s; }
        .err-primary { background: #b22222; color: #fff; }
        .err-primary:hover { background: #8f1c1c; }
        .err-ghost { color: #b22222; background: #fff; }
        .err-ghost:hover { background: #fdf2f2; }
        .err-actions a:focus-visible { outline: 2px solid #b22222; outline-offset: 3px; }
    </style>
</head>
<body>
    <main class="err">
        <img src="{{ asset('image/SMMS Logo.png') }}" alt="St. Mary Magdalene School" onerror="this.remove()">
        <p class="err-code">@yield('code')</p>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <div class="err-actions">
            @auth
                <a class="err-primary" href="{{ route('dashboard') }}">Go to your dashboard</a>
            @else
                <a class="err-primary" href="{{ url('/') }}">Go to the homepage</a>
            @endauth
            <a class="err-ghost" href="javascript:history.back()">Go back</a>
        </div>
    </main>
</body>
</html>
