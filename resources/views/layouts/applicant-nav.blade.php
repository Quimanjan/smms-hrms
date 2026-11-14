{{-- resources/views/layouts/applicant-nav.blade.php (top navigation: Applicant and Employee) --}}
@php
    $user = auth()->user();

    // Spatie role name: Employee or Applicant
    $role = $user->getRoleNames()->first() ?? 'No Role';

    $initials = collect(preg_split('/\s+/', trim($user->name)))
        ->filter()
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->take(2)
        ->implode('');

    $items = [
        'dashboard'        => ['label' => 'My Progress',                'icon' => 'fa-table-cells',         'route' => 'dashboard',              'match' => 'dashboard'],
        'attendance'       => ['label' => 'Attendance Monitoring',      'icon' => 'fa-calendar-check',      'route' => 'attendance.index',       'match' => 'attendance.*'],
        'time-corrections' => ['label' => 'Time Correction Management', 'icon' => 'fa-clock-rotate-left',   'route' => 'time-corrections.index', 'match' => 'time-corrections.*'],
        'leaves'           => ['label' => 'Leave Management',           'icon' => 'fa-calendar-days',       'route' => 'leaves.index',           'match' => 'leaves.*'],
        'payroll'          => ['label' => 'Payroll Management',         'icon' => 'fa-wallet',              'route' => 'payroll.index',          'match' => 'payroll.*'],
        'loans'            => ['label' => 'Financial Loan Management',  'icon' => 'fa-hand-holding-dollar', 'route' => 'loans.index',            'match' => 'loans.*'],
        'job-openings'     => ['label' => 'Job Openings',                'icon' => 'fa-briefcase',           'route' => 'dashboard',              'match' => 'dashboard', 'tab' => 'job-openings'],
        'application'      => ['label' => 'My Application',             'icon' => 'fa-file-signature',      'route' => 'applications.index',     'match' => 'applications.*'],
    ];

    $roleMenus = [
        'Applicant' => [
            'dashboard', 'job-openings', 'application',
        ],
     
    ];

    // Unknown role or no role: Dashboard only
    $menu = collect($roleMenus[$role] ?? ['dashboard'])
        ->map(fn ($key) => $items[$key])
        ->all();
@endphp

<nav
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    class="sticky top-0 z-30 bg-white shadow-[0_1px_8px_rgba(0,0,0,0.08)]"
>
    <div class="mx-auto flex max-w-[1920px] items-center gap-4 px-4 py-4 sm:px-8 lg:px-12 lg:py-5">

        {{-- Logo and school name --}}
        <a href="{{ route('dashboard') }}" class="flex min-w-0 flex-1 items-center gap-2 sm:gap-4">
            <img
                src="{{ asset('image/SMMS Logo.png') }}"
                alt="School Logo"
                class="h-[45px] w-[45px] shrink-0 object-contain lg:h-[60px] lg:w-[60px]"
            >
            <span class="min-w-0 truncate text-[clamp(15px,2.2vw,30px)] font-extrabold leading-tight text-[#b22222]">
                St. Mary Magdalene School
            </span>
        </a>

    

        <div class="ml-auto flex shrink-0 items-center gap-2">
            <x-notification-bell />

            <x-dropdown align="right" width="48" content-classes="p-2 bg-white">
                <x-slot name="trigger">
                    <button type="button"
                        class="flex max-w-[min(18rem,65vw)] items-center gap-2 rounded-full border border-gray-200 bg-white py-1.5 pl-1.5 pr-3 text-left shadow-sm transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#b22222]"
                        aria-label="Account menu for {{ $user->name }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#b22222] text-sm font-bold text-white">
                            {{ $initials }}
                        </span>
                        <span class="hidden min-w-0 sm:block">
                            <span class="block max-w-[180px] truncate text-sm font-semibold text-gray-900">{{ $user->name }}</span>
                            <span class="block text-xs text-gray-500">{{ $role }}</span>
                        </span>
                        <i class="fa-solid fa-chevron-down shrink-0 text-xs text-gray-500" aria-hidden="true"></i>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('My Account') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full rounded-xl px-4 py-4 text-start text-md leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        {{-- Hamburger (mobile only) --}}
        <button
            type="button"
            @click="open = !open"
            class="text-[#b22222] lg:hidden"
            :aria-expanded="open.toString()"
            aria-controls="applicant-mobile-menu"
            aria-label="Toggle menu"
        >
            <i class="fa-solid text-xl" :class="open ? 'fa-xmark' : 'fa-bars'"></i>
        </button>
    </div>

    {{-- Mobile dropdown --}}
    <div
        x-show="open"
        id="applicant-mobile-menu"
        x-transition
        x-cloak
        class="max-h-[80vh] overflow-y-auto border-t border-gray-100 px-3 py-3 lg:hidden"
    >
        <nav class="grid gap-1" aria-label="Applicant menu">
            @foreach ($menu as $item)
                @php
                    $active = request()->routeIs($item['match'])
                        && ($item['route'] !== 'dashboard' || request()->query('tab') === ($item['tab'] ?? null));
                    $itemUrl = Route::has($item['route'])
                        ? route($item['route'], isset($item['tab']) ? ['tab' => $item['tab']] : [])
                        : '#';
                @endphp
                <a
                    href="{{ $itemUrl }}"
                    @click="open = false"
                    @class([
                        'flex min-h-11 items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition',
                        'bg-red-50 text-[#b22222]' => $active,
                        'text-gray-700 hover:bg-red-50 hover:text-[#b22222]' => ! $active,
                    ])
                >
                    <i class="fa-solid {{ $item['icon'] }} w-5 text-center" aria-hidden="true"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

    </div>
</nav>

<nav  x-data="{ open: false }"
    @keydown.escape.window="open = false"
    class="sticky hidden bg-white shadow-[0_1px_8px_rgba(0,0,0,0.08)] lg:block"
    >
  <div class="mx-auto flex max-w-[1920px] items-center gap-4 px-4 py-4 sm:px-8 lg:px-12 lg:py-5">
     {{-- Desktop links (scrolls sideways if too long, scrollbar hidden) --}}
        <div
            class="hidden min-w-0 flex-1 items-center gap-1 overflow-x-auto lg:flex [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
        >
            @foreach ($menu as $item)
                @php
                    $active = request()->routeIs($item['match'])
                        && ($item['route'] !== 'dashboard' || request()->query('tab') === ($item['tab'] ?? null));
                    $itemUrl = Route::has($item['route'])
                        ? route($item['route'], isset($item['tab']) ? ['tab' => $item['tab']] : [])
                        : '#';
                @endphp
                <a
                    href="{{ $itemUrl }}"
                    class="flex shrink-0 items-center gap-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition {{ $active ? 'bg-[#b22222] text-white' : 'text-gray-700 hover:bg-red-50 hover:text-[#b22222]' }}"
                >
                    <i class="fa-solid {{ $item['icon'] }}"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
        


    </div>


</nav>
 