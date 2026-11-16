{{-- resources/views/layouts/app.blade.php --}}
@php
    $user = Auth::user();

    // Uses Spatie roles, same as your dashboard
    $role = $user?->getRoleNames()->first() ?? 'No Role';

    // "Daniel Reyes" -> "DR"
    $initials = $user
        ? collect(preg_split('/\s+/', trim($user->name)))
            ->filter()
            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->take(2)
            ->implode('')
        : '';

    // Illustrated avatar for staff who have an employee record.
    $topAvatar = $user?->employee ? $user->employee->avatarSvg() : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? (request()->routeIs('profile.edit') && in_array($role, ['HR', 'Admin', 'Accounting', 'Employee'], true) ? "{$role} My Account" : config('app.name', 'HR Management System')) }}</title>

        {{-- Favicons --}}
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->

        <!-- Icons (Font Awesome) used by the sidebar and the notification bell -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        {{-- Hides Alpine elements until Alpine has loaded (needed by the notification bell) --}}
        <style>[x-cloak] { display: none !important; }</style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Apply the remembered sidebar state before the page paints. --}}
        <script>try { if (localStorage.getItem('smms.sidebar') === 'collapsed') document.documentElement.classList.add('sb-collapsed'); } catch (e) {}</script>
        {{-- The menu slides in once per sign-in, not on every page. --}}
        <script>try { if (!sessionStorage.getItem('smms.intro')) { document.documentElement.classList.add('sb-intro'); sessionStorage.setItem('smms.intro', '1'); } } catch (e) {}</script>
        {{-- A page transition the browser cancels (for example when going to the homepage) is normal; don't report it as an error. --}}
        <script>
            (() => {
                const quiet = (e) => { const t = e.viewTransition; if (t) [t.ready, t.finished, t.updateCallbackDone].forEach((p) => p && p.catch(() => {})); };
                addEventListener('pageswap', quiet);
                addEventListener('pagereveal', quiet);
            })();
        </script>
        {{-- Start loading a menu page while the pointer rests on its link, so the click opens it right away. --}}
        <script type="speculationrules">
        {"prefetch": [{"where": {"and": [{"selector_matches": "#sidebar a[href]"}, {"not": {"href_matches": "*/logout*"}}, {"not": {"href_matches": "*/export*"}}, {"not": {"href_matches": "*download*"}}]}, "eagerness": "moderate"}]}
        </script>

        {{-- School colors and type for the signed-in pages --}}
        @include('partials.app-theme')
    </head>
    <body class="app-body font-sans antialiased">
        {{--
            sidebarOpen controls the sidebar below the lg breakpoint (phones + tablets).
            - Esc closes it
            - it closes by itself if the window is resized up to desktop width
            - the page behind it does not scroll while it is open
        --}}
        <div x-data="{ sidebarOpen: false }"
             x-effect="document.body.classList.toggle('overflow-hidden', sidebarOpen)"
             @keydown.escape.window="sidebarOpen = false"
             @resize.window="if (window.innerWidth >= 1024) sidebarOpen = false"
             class="app-shell min-h-screen overflow-x-hidden bg-gray-100">

            @auth
                <div data-system-updates
                     data-update-url="{{ route('system-updates.current') }}"
                     data-current-version="{{ $systemDataVersion ?? 0 }}"></div>

                <style>
                    [data-system-update-notice][hidden] { display: none !important; }
                </style>
                <div data-system-update-notice hidden role="status" aria-live="polite"
                     class="fixed inset-x-4 bottom-4 z-[100] mx-auto flex max-w-xl items-center justify-between gap-4 rounded-lg border border-red-200 bg-white p-4 shadow-lg">
                    <p class="text-sm text-gray-800">New HRMS updates are available. Refresh to see the latest information.</p>
                    <button type="button" data-system-update-refresh
                            class="shrink-0 rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-700 focus-visible:ring-offset-2">
                        Refresh now
                    </button>
                </div>

                {{-- Applicant top navigation --}}
                @hasanyrole('Applicant')
                    @include('layouts.applicant-nav')
                @endhasanyrole

                {{-- Admin, HR, Accounting, Employee: left sidebar navigation --}}
                @hasanyrole('Admin|HR|Accounting|Employee')
                    @include('layouts.navigation')
                @endhasanyrole

            @endauth

            {{-- lg:pl-80 leaves room for the fixed sidebar (only for roles that have the sidebar) --}}
            <div @auth @hasanyrole('Admin|HR|Accounting|Employee') class="app-main-col min-w-0 lg:pl-80" @endhasanyrole @endauth>

                @auth
                @hasanyrole('Admin|HR|Accounting|Employee')
                <!-- Top bar: hamburger (phone/tablet) + page heading on the left, bell + profile dropdown on the right -->
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-2 px-3 py-3 sm:gap-4 sm:px-6 sm:py-4 lg:px-8 lg:py-6">

                    <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                        {{-- 40x40 tap target, hidden once the sidebar is permanently visible (lg+) --}}
                        <button type="button" @click="sidebarOpen = true"
                                :aria-expanded="sidebarOpen.toString()" aria-controls="sidebar"
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-lg text-red-900 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-700 lg:hidden"
                                aria-label="Open menu">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>

                        <!-- Page Heading -->
                        @isset($header)
                            <div class="app-page-heading min-w-0 flex-1 truncate">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    {{-- mr-3 = right margin on phones only (sm and up go back to 0) --}}
                    <div class="mr-3 flex shrink-0 items-center gap-2 sm:mr-0 sm:gap-4">

                        <!-- Notification bell -->
                        <x-notification-bell />

                        <!-- Profile dropdown -->
                        <x-dropdown align="right" width="48" content-classes=" p-2 bg-white">
                            <x-slot name="trigger">
                                {{-- Phone: avatar + chevron only. md+: name and role too. Extra right margin on desktop (lg). --}}
                                <button type="button"
                                        class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white py-1.5 pl-1.5 pr-3 text-base shadow-sm transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-700 sm:gap-3 sm:py-2 sm:pl-2 sm:pr-4 sm:text-lg lg:mr-[70px]"
                                        aria-label="Account Menu">
                                    @if ($topAvatar)
                                        <span class="av-host av-host--ring av-size-top">{!! $topAvatar !!}</span>
                                    @else
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-700 text-xs font-bold text-white sm:h-9 sm:w-9 sm:text-sm">
                                            {{ $initials }}
                                        </span>
                                    @endif

                                    <span class="hidden min-w-0 text-left leading-tight md:block">
                                        <span class="block max-w-[160px] truncate font-semibold text-gray-900 lg:max-w-[220px]">{{ $user->name }}</span>
                                        <span class="block max-w-[160px] truncate text-xs text-gray-500 lg:max-w-[220px]">
                                            {{ $user->getRoleNames()->first() ?? 'No role assigned' }}
                                        </span>
                                    </span>

                                    <i class="fa-solid fa-chevron-down text-xs text-gray-500"></i>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                {{-- On phones, show the avatar and account details in the menu. --}}
                                <div class="flex  items-center gap-3 border-b border-gray-100 px-4 py-3 md:hidden">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-700 text-sm font-bold text-white">
                                        {{ $initials }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-gray-500">{{ $user->getRoleNames()->first() ?? 'No role assigned' }}</p>
                                    </div>
                                </div>

                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('My Account') }}
                                </x-dropdown-link>


                           

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
                @endhasanyrole
                @endauth

                <!-- Page Content -->
                <main class="app-content min-w-0 @auth @hasanyrole('Admin|HR|Accounting|Employee') lg:-ml-4 @endhasanyrole @endauth">
                    {{ $slot }}
                </main>
            </div>
        </div>
        {{-- Chat bubble (lower right) for staff --}}
        @hasanyrole('Admin|HR|Accounting|Employee')
            @unless (request()->routeIs('messages.index'))
                @include('partials.chat-widget')
            @endunless
        @endhasanyrole

        {{-- Keep the menu's scroll position when moving between pages. --}}
        <script>
            (() => {
                const side = document.getElementById('sidebar');
                if (!side) return;
                try { const y = sessionStorage.getItem('smms.sideScroll'); if (y) side.scrollTop = +y; } catch (e) {}
                addEventListener('pagehide', () => { try { sessionStorage.setItem('smms.sideScroll', String(side.scrollTop)); } catch (e) {} });
            })();
        </script>
    </body>
</html>