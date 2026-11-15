
{{-- resources/views/employee/school-employees.blade.php --}}

@auth
    <x-slot name="title">
        @if (auth()->user()->hasRole('Employee'))
            Employee School Employees
        @endif
    </x-slot>
@endauth

<x-slot name="header">
    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-4xl font-extrabold leading-tight sm:ml-[10px] md:ml-[20px] lg:ml-[80px] text-red-700">
        School Employees
    </h1>
</x-slot>

@php
    $inputClass = 'w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-3 text-[15px] text-gray-900 transition focus:border-[#b22222] focus:outline-none focus:ring-4 focus:ring-red-700/10';

    $filterDepartments = $filterDepartments ?? \App\Models\SchoolEmployee::departmentsInDatabase();

    $roleTypes = collect($roleTypes ?? []);
@endphp

<div
    class="px-4 pb-12 pt-2 sm:px-6 lg:px-10"
    x-data="employeeDirectory(@js(['employees' => $cards]))"
    @keydown.escape.window="detailId = null"
>

    <section aria-label="Employee directory" class="flex flex-col gap-5">

        {{-- SEARCH AND FILTERS --}}
        <form
            method="GET"
            action="{{ route('employee.school-employees') }}"
            x-ref="filters"
            class="flex flex-wrap items-end gap-3.5 rounded-[20px] bg-white p-[18px] shadow-[0_10px_30px_rgba(15,23,42,0.05)]"
        >

            {{-- SEARCH NAME ONLY --}}
            <label
                for="employeeSearch"
                class="relative flex min-w-[240px] flex-[1_1_280px] flex-col gap-2 text-sm font-bold text-gray-600"
            >
                <span>Employee Name</span>

                <i class="fa-solid fa-magnifying-glass absolute bottom-[14px] left-[14px] text-gray-400"></i>

                <input
                    type="search"
                    id="employeeSearch"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search employee name..."
                    autocomplete="off"
                    @input.debounce.400ms="$refs.filters.requestSubmit()"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-[42px] pr-3.5 text-gray-900 focus:border-[#b22222] focus:outline-none focus:ring-4 focus:ring-red-700/10"
                >
            </label>

            {{-- DEPARTMENT FILTER --}}
            <label
                for="departmentFilter"
                class="flex min-w-[180px] flex-col gap-2 text-sm font-bold text-gray-600"
            >
                <span>Department</span>

                <select
                    id="departmentFilter"
                    name="department"
                    @change="$refs.filters.requestSubmit()"
                    class="{{ $inputClass }}"
                >
                    <option value="">All Departments</option>

                    @foreach ($filterDepartments as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected((string) request('department') === (string) $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </label>

            {{-- ROLE TYPE FILTER --}}
            <label
                for="roleFilter"
                class="flex min-w-[180px] flex-col gap-2 text-sm font-bold text-gray-600"
            >
                <span>Role Type</span>

                <select
                    id="roleFilter"
                    name="role_type"
                    @change="$refs.filters.requestSubmit()"
                    class="{{ $inputClass }}"
                >
                    <option value="">All Roles</option>

                    @foreach ($roleTypes as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected((string) request('role_type') === (string) $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </label>

            {{-- CLEAR FILTERS --}}
            @if (request()->filled('search') || request()->filled('department') || request()->filled('role_type'))
                <a
                    href="{{ route('employee.school-employees') }}"
                    class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50"
                >
                    <i class="fa-solid fa-rotate-left mr-1"></i>
                    Clear
                </a>
            @endif

        </form>

        {{-- EMPLOYEE RESULTS --}}
        @if ($employees->isEmpty())

            <div class="rounded-[20px] bg-white p-10 text-center text-gray-500 shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
                <i class="fa-solid fa-users-slash mb-3 text-4xl text-gray-300"></i>

                <p class="font-semibold">No employees found.</p>

                <p class="mt-1 text-sm text-gray-400">
                    Try another employee name or change your filters.
                </p>
            </div>

        @else

            {{-- EMPLOYEE CARDS --}}
            <div class="emp-grid">

                @foreach ($employees as $employee)

                    <article
                        @click="openDetail({{ $employee->id }})"
                        tabindex="0"
                        @keydown.enter="openDetail({{ $employee->id }})"
                        @keydown.space.prevent="openDetail({{ $employee->id }})"
                        role="button"
                        aria-label="View {{ $employee->name }} details"
                        class="emp-card"
                    >

                        <div class="av-host emp-card-face">
                            {!! \App\Support\Avatar::for($employee, $employee->name ?? '') !!}
                        </div>

                        <div class="emp-card-info">

                            <h2 class="emp-card-name">
                                {{ $employee->name }}
                            </h2>

                            <p class="emp-card-role">{{ $employee->position }}</p>
                            <p class="emp-card-id">ID {{ $employee->employee_id }}</p>

                        </div>

                    </article>

                @endforeach

            </div>

            {{-- PAGINATION --}}
            <div>
                {{ $employees->links() }}
            </div>

        @endif

    </section>

    {{-- EMPLOYEE DETAILS MODAL --}}
    <div x-show="detail" x-cloak x-transition.opacity
        @click.self="detailId = null" @keydown.escape.window="detailId = null"
        class="ed-overlay">
        <div role="dialog" aria-modal="true" aria-labelledby="employeeModalName" class="ed">
            <template x-if="detail">
                <div>
                    <header class="ed-head">
                        <button type="button" class="ed-close" @click="detailId = null" aria-label="Close">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                        </button>
                        <div class="av-host av-host--lg ed-face" x-html="detail.avatar"></div>
                        <div class="ed-who">
                            <h3 id="employeeModalName" class="ed-name" x-text="detail.name"></h3>
                            <p class="ed-role" x-text="detail.position"></p>
                            <div class="ed-chips">
                                <span class="ed-chip ed-chip--gold" x-text="detail.role_label" x-show="detail.role_label"></span>
                                <span class="ed-chip" x-text="'ID ' + detail.employee_id" x-show="detail.employee_id"></span>
                            </div>
                        </div>
                    </header>

                    <div class="ed-body">
                        <dl class="ed-grid">
                            <div><dt>Position</dt><dd x-text="detail.position || '—'"></dd></div>
                            <div><dt>Department</dt><dd x-text="detail.department_label || '—'"></dd></div>
                            <div><dt>Role type</dt><dd x-text="detail.role_label || '—'"></dd></div>
                            <div><dt>Salary step</dt><dd x-text="detail.step_label || '—'"></dd></div>
                            <div><dt>Email</dt><dd x-text="detail.email || '—'"></dd></div>
                            <div><dt>Birthday</dt><dd x-text="detail.birthday_label || '—'"></dd></div>
                            <div class="ed-wide"><dt>Emergency contact</dt><dd x-text="detail.emergency_contact || '—'"></dd></div>
                        </dl>
                    </div>
                    <div class="ed-foot"></div>
                </div>
            </template>
        </div>
    </div>

</div>

<script>
    function employeeDirectory(cfg) {
        return {
            employees: cfg.employees,
            detailId: null,

            get detail() {
                return this.detailId !== null
                    ? this.employees[this.detailId]
                    : null;
            },

            openDetail(id) {
                this.detailId = id;
            },
        };
    }
</script>
