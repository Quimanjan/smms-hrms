{{-- resources/views/layouts/navigation.blade.php --}}

@php
    /*
    |--------------------------------------------------------------------------
    | Current User and Role
    |--------------------------------------------------------------------------
    */

    $user = auth()->user();

    $role = $user
        ? $user->getRoleNames()->first()
        : null;

    /*
    |--------------------------------------------------------------------------
    | Sidebar Menu Items
    |--------------------------------------------------------------------------
    */

    $items = [

        // General Dashboard
        'dashboard' => [
            'label' => 'Dashboard',
            'icon'  => 'fa-table-cells',
            'route' => 'dashboard',
            'match' => 'dashboard',
        ],

        // Employee Dashboard
        'employee-dashboard' => [
            'label' => 'Dashboard',
            'icon'  => 'fa-table-cells',
            'route' => 'employee-dashboard',
            'match' => 'employee-dashboard',
        ],

        // Employee Management
        'employees' => [
            'label' => 'Employee Monitoring',
            'icon'  => 'fa-users',
            'route' => 'employees.index',
            'match' => 'employees.*',
        ],

        // Attendance
        'attendance' => [
            'label' => 'Attendance Monitoring',
            'icon'  => 'fa-calendar-check',
            'route' => 'attendance.index',
            'match' => 'attendance.*',
        ],

        // Time Corrections
        'time-corrections' => [
            'label' => 'Time Correction Management',
            'icon'  => 'fa-clock-rotate-left',
            'route' => 'time-corrections.index',
            'match' => 'time-corrections.*',
        ],

        // Leaves
        'leaves' => [
            'label' => 'Leave Management',
            'icon'  => 'fa-calendar-days',
            'route' => 'leaves.index',
            'match' => 'leaves.*',
        ],

        // Onboarding
        'onboarding' => [
            'label' => 'Onboarding Management',
            'icon'  => 'fa-user-plus',
            'route' => 'onboarding.index',
            'match' => 'onboarding.*',
        ],

        // Payroll
        'payroll' => [
            'label' => 'Payroll Management',
            'icon'  => 'fa-wallet',
            'route' => 'payroll.index',
            'match' => 'payroll.*',
        ],

        // Loans
        'loans' => [
            'label' => 'Financial Loan Management',
            'icon'  => 'fa-hand-holding-dollar',
            'route' => 'loans.index',
            'match' => 'loans.*',
        ],

        // Offboarding
        'offboarding' => [
            'label' => 'Offboarding Management',
            'icon'  => 'fa-user-slash',
            'route' => 'offboarding.index',
            'match' => 'offboarding.*',
        ],

        // Documents
        'documents' => [
            'label' => 'Document Management',
            'icon'  => 'fa-folder-open',
            'route' => 'documents.index',
            'match' => 'documents.*',
        ],

        // General Reports
        'reports' => [
            'label' => 'Reports',
            'icon'  => 'fa-file',
            'route' => 'reports.index',
            'match' => 'reports.*',
        ],

        // Applicant
        'application' => [
            'label' => 'My Application',
            'icon'  => 'fa-file-signature',
            'route' => 'applications.index',
            'match' => 'applications.*',
        ],

        /*
        |--------------------------------------------------------------------------
        | Accounting Menu Items
        |--------------------------------------------------------------------------
        */

        'payroll-processing' => [
            'label' => 'Payroll Processing',
            'icon'  => 'fa-peso-sign',
            'route' => 'payroll-accounting.index',
            'match' => 'payroll-accounting.*',
        ],

        'salary-monitoring' => [
            'label' => 'Salary Monitoring',
            'icon'  => 'fa-receipt',
            'route' => 'salary-monitoring.index',
            'match' => 'salary-monitoring.*',
        ],

        'salary-records' => [
            'label' => 'Salary Record Management',
            'icon'  => 'fa-file-invoice',
            'route' => 'salary-records.index',
            'match' => 'salary-records.*',
        ],

        'loans-accounting' => [
            'label' => 'Financial Loan Management',
            'icon'  => 'fa-hand-holding-dollar',
            'route' => 'loans-accounting.index',
            'match' => 'loans-accounting.*',
        ],

        'audit-trail' => [
            'label' => 'Audit Trail',
            'icon'  => 'fa-clipboard-list',
            'route' => 'activity-log.index',
            'match' => 'activity-log.*',
        ],

        'homepage' => [
            'label' => 'Homepage',
            'icon'  => 'fa-house-laptop',
            'route' => 'site-content.edit',
            'match' => 'site-content.*',
        ],

        'remittances' => [
            'label' => 'Remittances',
            'icon'  => 'fa-building-columns',
            'route' => 'remittances.index',
            'match' => 'remittances.*',
        ],

        'accounting-reports' => [
            'label' => 'Reports',
            'icon'  => 'fa-chart-line',
            'route' => 'reports.index',
            'match' => 'reports.*',
        ],

        /*
        |--------------------------------------------------------------------------
        | Employee-Only Menu Items
        |--------------------------------------------------------------------------
        */

        'attendance-employee' => [
            'label' => 'Attendance',
            'icon'  => 'fa-calendar-check',
            'route' => 'attendance.attendance-employee.index',
            'match' => 'attendance.attendance-employee.index',
        ],

        'my-time-corrections' => [
            'label' => 'Time Corrections Request',
            'icon'  => 'fa-clock-rotate-left',
            'route' => 'time-corrections.employee.index',
            'match' => 'time-corrections.employee.index',
        ],

        'my-leaves' => [
            'label' => 'Leaves Request',
            'icon'  => 'fa-calendar-days',
            'route' => 'leaves.employee.index',
            'match' => 'leaves.employee.index',
        ],

        'loan' => [
            'label' => 'Financial Loans Request',
            'icon'  => 'fa-hand-holding-dollar',
            'route' => 'loans.employee.index',
            'match' => 'loans.employee.index',
        ],

        'my-offboarding' => [
            'label' => 'Offboarding Request',
            'icon'  => 'fa-user-slash',
            'route' => 'offboarding.employee.index',
            'match' => 'offboarding.employee.index',
        ],

        'my-payslips' => [
            'label' => 'Payslip',
            'icon'  => 'fa-file-invoice-dollar',
            'route' => 'salary-records.employee.index',
            'match' => 'salary-records.employee.index',
        ],

        'my-documents' => [
            'label' => 'Documents',
            'icon'  => 'fa-folder-open',
            'route' => 'documents.employee.index',
            'match' => 'documents.employee.index',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Role-Specific Labels
    |--------------------------------------------------------------------------
    */

    if ($role === 'HR') {
        $items['attendance']['label'] = 'Attendance Management';
        $items['employees']['label']  = 'Employee Management';
    } elseif ($role === 'Admin') {
        $items['attendance']['label'] = 'Attendance Monitoring';
        $items['employees']['label']  = 'Employee Monitoring';
    }

    /*
    |--------------------------------------------------------------------------
    | Menus by Role
    |--------------------------------------------------------------------------
    */

    $roleMenus = [

        'Admin' => [
            'dashboard',
            'employees',
            'attendance',
            'time-corrections',
            'leaves',
            'onboarding',
            'payroll',
            'loans',
            'offboarding',
            'remittances',
            'homepage',
            'audit-trail',
            'reports',
        ],

        'HR' => [
            'dashboard',
            'employees',
            'attendance',
            'time-corrections',
            'leaves',
            'onboarding',
            'payroll',
            'loans',
            'documents',
            'offboarding',
            'homepage',
            'reports',
        ],

        'Accounting' => [
            'dashboard',
            'payroll-processing',
            'salary-monitoring',
            'salary-records',
            'loans-accounting',
            'remittances',
            'accounting-reports',
        ],

        'Employee' => [
            'employee-dashboard',
            'attendance-employee',
            'my-time-corrections',
            'my-leaves',
            'loan',
            'my-offboarding',
            'my-payslips',
            'my-documents',
        ],

        'Applicant' => [
            'dashboard',
            'application',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Build Menu
    |--------------------------------------------------------------------------
    */

    $menuKeys = $roleMenus[$role] ?? [];

    $menu = collect($menuKeys)
        ->map(fn ($key) => $items[$key] ?? null)
        ->filter(fn ($item) => $item !== null
            && \Illuminate\Support\Facades\Route::has($item['route']))
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Dashboard / Logo Link
    |--------------------------------------------------------------------------
    */

    $homeRoute = match ($role) {
        'Employee' => 'employee-dashboard',
        default    => 'dashboard',
    };

    if (! \Illuminate\Support\Facades\Route::has($homeRoute)) {
        $homeRoute = \Illuminate\Support\Facades\Route::has('dashboard')
            ? 'dashboard'
            : null;
    }

    $homeUrl = $homeRoute
        ? route($homeRoute)
        : url('/');
@endphp

{{-- Mobile / tablet overlay (below lg) --}}
<div
    x-cloak
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-black/40 lg:hidden"
    aria-hidden="true"
></div>

{{--
    Sidebar
    - below lg: slides in/out, controlled by sidebarOpen (from layouts/app.blade.php)
    - lg and up: always visible, w-80 (matches the lg:pl-80 offset in the layout)
--}}
<aside
    id="sidebar"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 flex w-[min(21rem,calc(100vw-2rem))] flex-col
           overflow-y-auto overscroll-contain border-r border-red-100 bg-red-50
           transition-transform duration-200 ease-in-out
           lg:w-80 lg:translate-x-0
           [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
    aria-label="Main navigation"
>
    {{-- Close button (below lg only) --}}
    <button
        type="button"
        @click="sidebarOpen = false"
        class="absolute right-3 top-3 grid h-10 w-10 place-items-center rounded-md
               text-red-900 hover:bg-red-100
               focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 lg:hidden"
        aria-label="Close navigation menu"
    >
        <i class="fa-solid fa-xmark text-xl" aria-hidden="true"></i>
    </button>

    {{-- School Logo and Name --}}
    <div class="flex flex-col items-center px-4 py-6">
        <a href="{{ $homeUrl }}" class="rounded-md" aria-label="Go to home dashboard">
            <img
                src="{{ asset('image/SMMS%20Logo.png') }}"
                alt="St. Mary Magdalene School Logo"
                class="h-20 w-auto object-contain"
            >
        </a>

        <h2 class="mt-4 text-center text-base font-bold text-gray-900">
            St. Mary Magdalene School
        </h2>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 space-y-1 px-3 pb-6" aria-label="Sidebar menu">
        @forelse ($menu as $item)
            <x-sidebar-link
                :href="route($item['route'])"
                :icon="$item['icon']"
                :active="request()->routeIs($item['match'])"
            >
                {{ $item['label'] }}
            </x-sidebar-link>
        @empty
            <div class="px-3 py-4 text-sm text-gray-500">
                No navigation items are available for your account.
            </div>
        @endforelse
    </nav>

    {{-- Collapse to an icon rail (desktop only). The choice is remembered in this browser. --}}
    <button type="button" class="sb-toggle" aria-controls="sidebar" aria-expanded="true"
        onclick="(function (btn) {
            const collapsed = document.documentElement.classList.toggle('sb-collapsed');
            try { localStorage.setItem('smms.sidebar', collapsed ? 'collapsed' : 'expanded'); } catch (e) {}
            btn.setAttribute('aria-expanded', String(!collapsed));
            btn.setAttribute('aria-label', collapsed ? 'Expand menu' : 'Collapse menu');
            btn.title = collapsed ? 'Expand menu' : 'Collapse menu';
            setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 260);
        })(this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
        <span>Collapse menu</span>
    </button>
    <script>
        (function () {
            const btn = document.currentScript.previousElementSibling;
            const collapsed = document.documentElement.classList.contains('sb-collapsed');
            btn.setAttribute('aria-expanded', String(!collapsed));
            btn.setAttribute('aria-label', collapsed ? 'Expand menu' : 'Collapse menu');
            btn.title = collapsed ? 'Expand menu' : 'Collapse menu';
        })();
    </script>
</aside>