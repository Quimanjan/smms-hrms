{{--
    Top bar for the public pages other than the landing page.
    $accountButtons: true  -> Log in / Sign up are buttons that open this page's own modals
                     false -> they are links to the landing page's modals
--}}
@php($accountButtons = $accountButtons ?? false)
<script>document.documentElement.classList.add('mg-js');</script>
<header class="mg-bar">
    <div class="mg-wrap mg-bar-inner">
        <a class="mg-brand" href="{{ route('home') }}" aria-label="St. Mary Magdalene School home">
            <img src="{{ asset('image/SMMS Logo.png') }}" alt="" width="44" height="44">
            <span>St. Mary Magdalene School</span>
        </a>
        <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="top-nav"
            onclick="this.classList.toggle('active'); const nav = document.getElementById('top-nav'); const open = nav.classList.toggle('open'); this.setAttribute('aria-expanded', open);">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <nav class="mg-nav" id="top-nav" aria-label="Main">
            <a class="mg-nav-link" href="{{ route('home') }}">Home</a>
            <a class="mg-nav-link {{ request()->routeIs('public.job-openings*') ? 'is-current' : '' }}"
                href="{{ route('public.job-openings') }}"
                @if (request()->routeIs('public.job-openings')) aria-current="page" @endif>Job openings</a>
            @auth
                <a class="mg-btn mg-btn-crimson" href="{{ route('dashboard') }}">Dashboard</a>
            @else
                @if ($accountButtons)
                    <button class="mg-btn mg-btn-outline" id="jobs-login-trigger" type="button">Log in</button>
                    <button class="mg-btn mg-btn-crimson" id="jobs-signup-trigger" type="button">Sign up</button>
                @else
                    <a class="mg-btn mg-btn-outline" href="{{ route('home', ['login' => 1]) }}">Log in</a>
                    <a class="mg-btn mg-btn-crimson" href="{{ route('home', ['signup' => 1]) }}">Sign up</a>
                @endif
            @endauth
        </nav>
    </div>
</header>
