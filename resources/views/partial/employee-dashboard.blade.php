{{-- resources/views/employees/index.blade.php --}}

    {{-- Browser tab title --}}
   
   @auth
    <x-slot name="title">
        @if (auth()->user()->hasRole('Admin'))
            Admin Employee Monitoring
        @elseif (auth()->user()->hasRole('HR'))
            HR Employee Management
        @elseif (auth()->user()->hasRole('Accounting'))
            Accounting Employee Management
        @elseif (auth()->user()->hasRole('Employee'))
            Employee Employee Management
        @elseif (auth()->user()->hasRole('Applicant'))
            Applicant Dashboard
        @else
            Dashboard
        @endif
    </x-slot>

  
@endauth


{{-- Page heading (shown in the layout's top bar) --}}
   


      @auth
    <x-slot name="header">
        @if (auth()->user()->hasRole('Admin'))
            <h1 class="ml-20 text-2xl font-extrabold leading-none tracking-tight text-[#b22222] sm:text-4xl">Employee Monitoring</h1>
 
        @elseif (auth()->user()->hasRole('HR'))
             <h1 class="ml-20 text-2xl font-extrabold leading-none tracking-tight text-[#b22222] sm:text-4xl">Employee Management</h1>
        @else
            Dashboard
        @endif
    </x-slot>

  
@endauth

    @php
        $inputClass = 'w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-3 text-[15px] text-gray-900 transition focus:border-[#b22222] focus:outline-none focus:ring-4 focus:ring-red-700/10';
        $labelClass = 'mb-1.5 block text-sm font-bold text-gray-600';
        $errClass   = 'mt-1 text-xs font-semibold text-red-600';

        // Only HR can add / edit / delete (Admin can view only)
        $canManage = auth()->user()->hasRole('HR');

        // Keep what the user typed if validation failed, so the form can reopen
        $old = collect(old())->only([
            'employee_source', 'user_id', 'zkteco_user_id', 'first_name', 'middle_name', 'last_name', 'suffix',
            'position', 'department', 'role_type', 'employment_status', 'step_no', 'email',
            'birthday', 'civil_status', 'gender',
            'address_line', 'address_landmark', 'barangay', 'city_municipality', 'province', 'zip_code',
            'emergency_name', 'emergency_contact',
        ])->all();

        $roleTypes = [
            'Administrative Staff' => 'Administrative Staff',
            'Teaching Staff'       => 'Teaching Staff',
            'Maintenance Staff'    => 'Maintenance Staff',
            'Support Staff'        => 'Support Staff',
        ];
        $filterRoleTypes = collect($filterRoleTypes ?? $roleTypes);

        // Civil status and gender options (stored value = label).
        // Uses the controller's lists if it passed them.
        $civilStatuses = $civilStatuses ?? ['Single', 'Married', 'Widowed', 'Separated', 'Divorced'];
        $genders       = $genders ?? ['Male', 'Female', 'Other'];

        // Employment status: stored value => label.
        // Uses the controller's list if it passed one.
        $employmentStatuses = $employmentStatuses ?? [
            'regular'     => 'Regular',
            'non regular' => 'Probationary',
        ];

        // Payroll steps from the payroll_steps table: step_no => label
        $payrollSteps = $payrollSteps ?? collect();

        // Default step for the Add form (first step in the table)
        $defaultStepNo = (string) ($defaultStepNo ?? $payrollSteps->keys()->first() ?? '');

        $filterDepartments = collect($filterDepartments ?? []);
        $formDepartments   = collect($formDepartments ?? []);
    @endphp

    <div data-live-region="employees"
        class="px-4  pb-12 pt-2 sm:px-6 lg:px-10"
        x-data="employeeDirectory(@js([
            'employees'   => $cards,
            'applicants'  => $applicants,
            'statuses'    => $employmentStatuses,
            'defaultStep' => $defaultStepNo,
            'old'         => $old,
            'reopen'      => $errors->any(),
            'errorKeys'   => array_keys($errors->getMessages()),
            'editingId'   => old('_editing_id'),
            'storeUrl'    => route('employees.store'),
            'updateUrl'   => route('employees.update', '__ID__'),
            'destroyUrl'  => route('employees.destroy', '__ID__'),
        ]))"
        @keydown.escape.window="closeAll()"
    >

        {{-- Floating Add Employee button (employees page only) --}}
        @if ($canManage && request()->routeIs('employees.index'))
            <button type="button" @click="openCreate()"
                x-show="!formOpen && !detail" x-transition.opacity
                class="cw-avoid fixed bottom-6 right-6 z-50 inline-flex cursor-pointer items-center gap-2 rounded-lg bg-[#b22222] px-4 py-3 font-bold text-white shadow-[0_6px_20px_rgba(178,34,34,0.4)] transition hover:-translate-y-0.5 hover:bg-[#8f1c1c] hover:shadow-[0_8px_24px_rgba(178,34,34,0.5)]">
                <i class="fa-solid fa-user-plus"></i> Add Employee
            </button>
        @endif

        {{-- Flash message --}}
        @if (session('success'))
            <div class="mb-5 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <section aria-label="Employee directory" class="flex flex-col gap-5">

{{-- Toolbar: Search + Filters (Manual submission) --}}
<form data-live-search method="GET"
    action="{{ route('employees.index') }}"
    x-ref="filters"
    class="flex flex-wrap items-end gap-3.5 rounded-[20px] bg-white p-[18px] shadow-[0_10px_30px_rgba(15,23,42,0.05)]">

    {{-- Employee Name Search ONLY --}}
    <label for="employeeSearch"
        class="relative flex min-w-[240px] flex-[1_1_280px] flex-col gap-2 text-sm font-bold text-gray-600">

        <span>Employee Name</span>

        <i class="fa-solid fa-magnifying-glass absolute bottom-[14px] left-[14px] text-gray-400"></i>

        <input
            type="search"
            id="employeeSearch"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search employee name..."
            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-[42px] pr-3.5 text-gray-900 focus:border-[#b22222] focus:outline-none focus:ring-4 focus:ring-red-700/10">
    </label>

                {{-- Department filter: Elementary / High School / Senior High School + departments stored in SQL --}}
                <label for="departmentFilter" class="flex min-w-[180px] flex-col gap-2 text-sm font-bold text-gray-600">
                    <span>Department</span>
                    <select id="departmentFilter" name="department" @change="$refs.filters.submit()" class="{{ $inputClass }}">
                        <option value="">All Department</option>
                        @foreach ($filterDepartments as $value => $label)
                            <option value="{{ $value }}" @selected(request('department') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                {{-- Role type filter --}}
                <label for="roleFilter" class="flex min-w-[180px] flex-col gap-2 text-sm font-bold text-gray-600">
                    <span>Role Type</span>
                    <select id="roleFilter" name="role_type" @change="$refs.filters.submit()" class="{{ $inputClass }}">
                        <option value="">All Roles</option>
                        @foreach ($filterRoleTypes as $value => $label)
                            <option value="{{ $value }}" @selected(request('role_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                @if (request()->filled('search') || request()->filled('department') || request()->filled('role_type'))
                    <a href="{{ route('employees.index') }}"
                        class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                        Clear
                    </a>
                @endif
            </form>

            {{-- Employee cards --}}
            @if ($employees->isEmpty())
                <div class="rounded-[20px] bg-white p-10 text-center text-gray-500 shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
                    <i class="fa-solid fa-users-slash mb-3 text-4xl text-gray-300"></i>
                    <p class="font-semibold">No employees found.</p>
                </div>
            @else
                @php
                    $noHireDate = \App\Models\Employee::excludeInternalStaff()->current()->whereNull('date_hired')->orderBy('last_name')->get(['first_name','last_name']);
                @endphp
                @if ($noHireDate->isNotEmpty())
                    <div class="emp-hire-alert" role="status">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        <p><strong>{{ $noHireDate->count() }} {{ \Illuminate\Support\Str::plural('employee', $noHireDate->count()) }} without a date hired:</strong>
                        {{ $noHireDate->map(fn ($e) => trim($e->first_name.' '.$e->last_name))->join(', ') }}.
                        Open each one and choose Edit to add it. Final pay and length of service need it.</p>
                    </div>
                @endif
                <p class="emp-archived-note">Employees who resigned or separated are archived after their last working day. Find them in <a href="{{ route('offboarding.index', ['tab' => 'archive']) }}">Offboarding, Archive</a>.</p>
                <div class="emp-grid">
                    @foreach ($employees as $employee)
                        <article
                            @click="openDetail({{ $employee->id }})"
                            class="emp-card"
                        >
                            <div class="av-host emp-card-face">
                                {!! $employee->avatarSvg() !!}
                            </div>
                            <div class="emp-card-info">
                                <h2 class="emp-card-name">{{ $employee->name }}</h2>
                                <p class="emp-card-role">{{ $employee->position }}</p>
                                <p class="emp-card-id">ID {{ $employee->zkteco_user_id ?? $employee->employee_id }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div>{{ $employees->links() }}</div>
            @endif
        </section>

        {{-- ============ Details modal ============ --}}
        <div x-show="detail" x-cloak x-transition.opacity
            @click.self="detailId = null" @keydown.escape.window="detailId = null"
            class="ed-overlay">
            <div role="dialog" aria-modal="true" aria-labelledby="ed-name" class="ed">
                <template x-if="detail">
                    <div x-data="{ tab: 'personal' }">
                        <header class="ed-head">
                            <button type="button" class="ed-close" @click="detailId = null" aria-label="Close">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                            </button>
                            <div class="av-host av-host--lg ed-face" x-html="detail.avatar"></div>
                            <div class="ed-who">
                                <h3 id="ed-name" class="ed-name" x-text="fullName(detail)"></h3>
                                <p class="ed-role" x-text="detail.position"></p>
                                <div class="ed-chips">
                                    <span class="ed-chip ed-chip--gold" x-text="statusLabel(detail.employment_status)"></span>
                                    <span class="ed-chip" x-show="detail.step_label" x-text="detail.step_label"></span>
                                    <span class="ed-chip" x-text="'ID ' + (detail.zkteco_user_id || detail.employee_id)"></span>
                                </div>
                            </div>
                        </header>

                        <div class="ed-tabs" role="tablist" aria-label="Employee details">
                            <button type="button" role="tab" :aria-selected="(tab === 'personal').toString()" @click="tab = 'personal'">Personal information</button>
                            <button type="button" role="tab" :aria-selected="(tab === 'work').toString()" @click="tab = 'work'">Work</button>
                            <button type="button" role="tab" :aria-selected="(tab === 'education').toString()" @click="tab = 'education'">Educational background</button>
                            <button type="button" role="tab" :aria-selected="(tab === 'emergency').toString()" @click="tab = 'emergency'">Emergency contact</button>
                            <button type="button" role="tab" :aria-selected="(tab === 'ids').toString()" @click="tab = 'ids'">Government IDs</button>
                        </div>

                        <div class="ed-body">
                            <dl class="ed-grid" x-show="tab === 'personal'" role="tabpanel">
                                <div><dt>Full name</dt><dd x-text="fullName(detail) || '—'"></dd></div>
                                <div><dt>Birthday</dt><dd x-text="detail.birthday_label || '—'"></dd></div>
                                <div><dt>Gender</dt><dd x-text="detail.gender || '—'"></dd></div>
                                <div><dt>Civil status</dt><dd x-text="detail.civil_status || '—'"></dd></div>
                                <div><dt>Email</dt><dd x-text="detail.email || '—'"></dd></div>
                                <div class="ed-wide"><dt>Address</dt><dd x-text="detail.address_label || '—'"></dd></div>
                            </dl>

                            <dl class="ed-grid" x-show="tab === 'work'" x-cloak role="tabpanel">
                                <div><dt>Position</dt><dd x-text="detail.position || '—'"></dd></div>
                                <div><dt>Department</dt><dd x-text="detail.department_label || detail.department || '—'"></dd></div>
                                <div><dt>Role type</dt><dd x-text="detail.role_type || '—'"></dd></div>
                                <div><dt>Date hired</dt><dd x-text="detail.date_hired_label || 'Not set'"></dd></div>
                                <div><dt>Employment status</dt><dd x-text="statusLabel(detail.employment_status)"></dd></div>
                                <div><dt>Salary step</dt><dd x-text="detail.step_label || '—'"></dd></div>
                                <div><dt>Biometric ID</dt><dd x-text="detail.zkteco_user_id || detail.employee_id || '—'"></dd></div>
                            </dl>

                            <div x-show="tab === 'education'" x-cloak role="tabpanel">
                                <dl class="ed-grid">
                                    <div class="ed-wide"><dt>Highest education attained</dt><dd x-text="detail.attainment || '—'"></dd></div>
                                </dl>
                                <ol class="ed-edu" x-show="(detail.education || []).length">
                                    <template x-for="row in (detail.education || [])" :key="row.level">
                                        <li>
                                            <span class="ed-edu-level" x-text="row.level"></span>
                                            <span class="ed-edu-detail"><span x-text="row.detail || 'Completed'"></span><small class="ed-edu-school" x-show="row.school" x-text="row.school"></small></span>
                                            <span class="ed-edu-years" x-text="row.years"></span>
                                        </li>
                                    </template>
                                </ol>
                                <p class="ed-note" x-show="!(detail.education || []).length">No schooling recorded yet. Choose Edit employee to add it.</p>
                            </div>

                            <dl class="ed-grid" x-show="tab === 'emergency'" x-cloak role="tabpanel">
                                <div><dt>Contact person</dt><dd x-text="detail.emergency_name || '—'"></dd></div>
                                <div><dt>Contact number</dt><dd x-text="detail.emergency_contact || '—'"></dd></div>
                            </dl>

                            <div x-show="tab === 'ids'" x-cloak role="tabpanel">
                                <p class="ed-note">Numbers stay hidden until you click the eye.</p>
                                <ul class="ed-ids">
                                    <template x-for="gid in (detail.gov_ids || [])" :key="gid.label">
                                        <li x-data="{ shown: false }">
                                            <span class="ed-ids-label" x-text="gid.label"></span>
                                            <template x-if="!gid.value"><span class="ed-muted">Not provided</span></template>
                                            <template x-if="gid.value">
                                                <span class="secret-value">
                                                    <span class="secret-value__text" x-text="shown ? gid.value : '•••• •••• ' + gid.value.replace(/\D+/g, '').slice(-4)"></span>
                                                    <button type="button" class="secret-value__eye" @click="shown = !shown"
                                                        :aria-label="(shown ? 'Hide ' : 'Show ') + gid.label" :aria-pressed="shown.toString()" :title="shown ? 'Hide' : 'Show'">
                                                        <svg x-show="!shown" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                                        <svg x-show="shown" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.4 10.4 0 0 1 12 5c6.4 0 10 7 10 7a17.6 17.6 0 0 1-3.2 4.1M6.6 6.6C3.9 8.4 2 12 2 12s3.6 7 10 7a9.7 9.7 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                                                    </button>
                                                </span>
                                            </template>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        @if ($canManage)
                            <footer class="ed-foot">
                                <form method="POST" :action="deleteAction()"
                                    data-confirm="Delete this employee? This cannot be undone."
                                    data-confirm-button="Delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ed-btn ed-btn--ghost">Delete</button>
                                </form>
                                <button type="button" @click="openEdit()" class="ed-btn ed-btn--primary">Edit employee</button>
                            </footer>
                        @endif
                    </div>
                </template>
            </div>
        </div>

        {{-- ============ Add / Edit modal ============ --}}
        <div x-show="formOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-[60] grid place-items-center bg-[rgba(3,7,18,0.5)] p-4">
            <div role="dialog" aria-modal="true" class="emp-form-dialog relative flex max-h-[92vh] w-full max-w-[800px] flex-col overflow-hidden rounded-[14px] bg-white shadow-[0_20px_50px_rgba(15,23,42,0.14)]">
              <div class="emp-form-scroll overflow-y-auto p-6 sm:p-8">
                <button type="button" @click="formOpen = false" aria-label="Close"
                    class="absolute right-3 top-2 cursor-pointer text-3xl leading-none text-gray-500 hover:text-gray-800">&times;</button>

                <h3 class="mb-5 text-2xl font-extrabold text-[#b22222]" x-text="editingId ? 'Edit Employee' : 'Add Employee'"></h3>

                {{-- Tabs keep the form short (client revision) --}}
                <div class="emp-form-tabs" role="tablist" aria-label="Employee details">
                    <button type="button" role="tab" :aria-selected="(ftab === 'personal').toString()" @click="ftab = 'personal'">Personal information</button>
                    <button type="button" role="tab" :aria-selected="(ftab === 'education').toString()" @click="ftab = 'education'">Educational background</button>
                    <button type="button" role="tab" :aria-selected="(ftab === 'ids').toString()" @click="ftab = 'ids'">Government IDs</button>
                </div>

                <form method="POST" :action="formAction()" class="space-y-4"
                    @invalid.capture="showTabOf($event.target)">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" :disabled="!editingId">
                    <input type="hidden" name="_editing_id" :value="editingId ?? ''">
                    <input type="hidden" name="employee_source" x-model="form.employee_source" :disabled="!!editingId">

                    <div x-show="!editingId" class="space-y-4">
                        <div class="grid grid-cols-2 gap-2 rounded-xl bg-gray-100 p-1" role="tablist" aria-label="Employee type">
                            <button type="button" role="tab" @click="selectSource('applicant')"
                                :aria-selected="form.employee_source === 'applicant'"
                                :class="form.employee_source === 'applicant' ? 'bg-white text-[#b22222] shadow-sm' : 'text-gray-600 hover:bg-white/70'"
                                class="rounded-lg px-3 py-2.5 text-sm font-bold transition">
                                Applicant
                            </button>
                            <button type="button" role="tab" @click="selectSource('existing')"
                                :aria-selected="form.employee_source === 'existing'"
                                :class="form.employee_source === 'existing' ? 'bg-white text-[#b22222] shadow-sm' : 'text-gray-600 hover:bg-white/70'"
                                class="rounded-lg px-3 py-2.5 text-sm font-bold transition">
                                Existing Employee
                            </button>
                        </div>
                        <p class="{{ $errClass }}">{{ $errors->first('employee_source') }}</p>
                    </div>

                    {{-- Select an applicant account to convert it into an employee login. --}}
                    <div x-show="!editingId && form.employee_source === 'applicant'">
                        <label for="f_user_id" class="{{ $labelClass }}">Applicant <span class="text-red-600">*</span></label>

                        <select id="f_user_id" name="user_id" x-model="form.user_id" @change="pickApplicant()"
                            :disabled="!!editingId || form.employee_source !== 'applicant'"
                            :required="!editingId && form.employee_source === 'applicant'"
                            class="{{ $inputClass }} cursor-pointer">
                            <option value="" disabled hidden>Select an applicant</option>
                            <template x-for="a in Object.values(applicants)" :key="a.id">
                                <option :value="a.id" :selected="String(a.id) === String(form.user_id)" x-text="a.name + ' (' + a.email + ')'"></option>
                            </template>
                        </select>

                        <p x-show="!hasApplicants" class="mt-1 text-xs font-semibold text-amber-700">
                            No applicants available. Only accounts with the Applicant role and no employee record yet are listed.
                        </p>
                        <p class="{{ $errClass }}">{{ $errors->first('user_id') }}</p>
                    </div>

                    <div class="space-y-4" data-ftab="personal" x-show="ftab === 'personal'" role="tabpanel">
                    <div>
                        <span class="{{ $labelClass }}">Full Name <span class="text-red-600">*</span></span>
                        <div class="grid grid-cols-[1fr_1fr_1fr_0.65fr] gap-2">
                                <div>
                                    <input id="f_first_name" name="first_name" x-model="form.first_name" placeholder="First Name" required
                                        class="{{ $inputClass }}">
                                    <p class="{{ $errClass }}">{{ $errors->first('first_name') }}</p>
                                </div>
                                <div>
                                    <input id="f_middle_name" name="middle_name" x-model="form.middle_name" placeholder="Middle Name"
                                        class="{{ $inputClass }}">
                                    <p class="{{ $errClass }}">{{ $errors->first('middle_name') }}</p>
                                </div>
                                <div>
                                    <input id="f_last_name" name="last_name" x-model="form.last_name" placeholder="Last Name" required
                                        class="{{ $inputClass }}">
                                    <p class="{{ $errClass }}">{{ $errors->first('last_name') }}</p>
                                </div>
                                <div>
                                    <input id="f_suffix" name="suffix" x-model="form.suffix" placeholder="Suffix (optional)"
                                        :readonly="!editingId && form.employee_source === 'applicant'"
                                        :class="!editingId && form.employee_source === 'applicant' ? 'cursor-not-allowed !bg-gray-100 text-gray-500' : ''"
                                        class="{{ $inputClass }}">
                                    <p class="{{ $errClass }}">{{ $errors->first('suffix') }}</p>
                                </div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            {{-- Employee ID = ZKTeco user ID (saved in employees.zkteco_user_id) --}}
                            <label for="f_zkteco_user_id" class="{{ $labelClass }}">Employee ID</label>
                            {{-- Generated by the system. Shown read-only and never submitted. --}}
                            <input id="f_zkteco_user_id" type="text" readonly tabindex="-1"
                                :value="form.zkteco_user_id || @js($nextEmployeeId ?? '')"
                                class="{{ $inputClass }} cursor-not-allowed bg-gray-100 text-gray-600">
                            <p class="mt-1 text-xs text-gray-500"
                                x-text="form.zkteco_user_id
                                    ? 'System-generated. Use this same ID on the fingerprint device.'
                                    : 'Assigned automatically when you save. Enroll this ID on the fingerprint device.'"></p>
                        </div>
                        <div>
                            <label for="f_step_no" class="{{ $labelClass }}">Step <span class="text-red-600">*</span></label>
                            {{-- Step names come from payroll_steps; wage amounts are managed separately. --}}
                            <select id="f_step_no" name="step_no" x-model="form.step_no" required class="{{ $inputClass }}">
                                <option value="" disabled hidden>Select step</option>
                                @foreach ($payrollSteps as $stepNo => $stepLabel)
                                    <option value="{{ $stepNo }}">{{ $stepLabel }}</option>
                                @endforeach
                            </select>
                            <p class="{{ $errClass }}">{{ $errors->first('step_no') }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="f_department" class="{{ $labelClass }}">Department <span class="text-red-600">*</span></label>
                            <input id="f_department" name="department" x-model="form.department" list="employeeDepartmentOptions"
                                placeholder="Select or type department" maxlength="100" required class="{{ $inputClass }}">
                            <datalist id="employeeDepartmentOptions">
                                @foreach ($formDepartments as $value => $label)
                                    <option value="{{ $value }}" label="{{ $label }}"></option>
                                @endforeach
                            </datalist>
                            <p class="{{ $errClass }}">{{ $errors->first('department') }}</p>
                        </div>
                        <div>
                            <label for="f_position" class="{{ $labelClass }}">Position <span class="text-red-600">*</span></label>
                            <input id="f_position" name="position" x-model="form.position" placeholder="e.g. Mathematics Teacher" required class="{{ $inputClass }}">
                            <p class="{{ $errClass }}">{{ $errors->first('position') }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="f_role_type" class="{{ $labelClass }}">Role Type <span class="text-red-600">*</span></label>
                            {{-- Options = Administrative / Teaching / Maintenance / Support Staff --}}
                            <select id="f_role_type" name="role_type" x-model="form.role_type" required class="{{ $inputClass }}">
                                <option value="" disabled hidden>Select role type</option>
                                @foreach ($roleTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="{{ $errClass }}">{{ $errors->first('role_type') }}</p>
                        </div>
                        <div>
                            <label for="f_employment_status" class="{{ $labelClass }}">Employment Status <span class="text-red-600">*</span></label>
                            <select id="f_employment_status" name="employment_status" x-model="form.employment_status" required class="{{ $inputClass }}">
                                <option value="" disabled hidden>Select employment status</option>
                                @foreach ($employmentStatuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="{{ $errClass }}">{{ $errors->first('employment_status') }}</p>
                        </div>
                    </div>

                    {{-- Date hired: used for length of service in final pay --}}
                    <div>
                        <label for="f_date_hired" class="{{ $labelClass }}">Date hired <span class="text-red-600">*</span></label>
                        <input id="f_date_hired" type="date" name="date_hired" x-model="form.date_hired" required max="{{ now()->toDateString() }}" class="{{ $inputClass }}">
                        <p class="{{ $errClass }}">{{ $errors->first('date_hired') }}</p>
                    </div>

                    {{-- Birthday / Civil Status / Gender (employees.birthday, employees.civil_status, employees.gender) --}}
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="f_birthday" class="{{ $labelClass }}">Birthday <span class="text-red-600">*</span></label>
                            <input id="f_birthday" type="date" name="birthday" x-model="form.birthday" max="{{ now()->subDay()->toDateString() }}" required class="{{ $inputClass }}">
                            <p class="{{ $errClass }}">{{ $errors->first('birthday') }}</p>
                        </div>
                        <div>
                            <label for="f_civil_status" class="{{ $labelClass }}">Civil Status <span class="text-red-600">*</span></label>
                            <select id="f_civil_status" name="civil_status" x-model="form.civil_status" required class="{{ $inputClass }}">
                                <option value="" disabled hidden>Select civil status</option>
                                @foreach ($civilStatuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            <p class="{{ $errClass }}">{{ $errors->first('civil_status') }}</p>
                        </div>
                        <div>
                            <label for="f_gender" class="{{ $labelClass }}">Gender <span class="text-red-600">*</span></label>
                            <select id="f_gender" name="gender" x-model="form.gender" required class="{{ $inputClass }}">
                                <option value="" disabled hidden>Select gender</option>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender }}">{{ $gender }}</option>
                                @endforeach
                            </select>
                            <p class="{{ $errClass }}">{{ $errors->first('gender') }}</p>
                        </div>
                    </div>

                    <div>
                        <label for="f_email" class="{{ $labelClass }}">Email <span class="text-red-600">*</span></label>
                        <input id="f_email" type="email" name="email" x-model="form.email" placeholder="Employee email" required
                            :readonly="!editingId && form.employee_source === 'applicant'"
                            :class="!editingId && form.employee_source === 'applicant' ? 'cursor-not-allowed !bg-gray-100 text-gray-500' : ''"
                            class="{{ $inputClass }}">
                        <p class="{{ $errClass }}">{{ $errors->first('email') }}</p>
                    </div>

                    <div>
                        <span class="{{ $labelClass }}">Address <span class="text-red-600">*</span></span>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <input id="f_address_line" name="address_line" x-model="form.address_line" placeholder="House/Lot/Block No./Street" required class="{{ $inputClass }}">
                                <p class="{{ $errClass }}">{{ $errors->first('address_line') }}</p>
                            </div>
                            <div>
                                <input id="f_address_landmark" name="address_landmark" x-model="form.address_landmark" placeholder="Subdivision/Building/Landmark (optional)" class="{{ $inputClass }}">
                                <p class="{{ $errClass }}">{{ $errors->first('address_landmark') }}</p>
                            </div>
                            <div>
                                <input id="f_barangay" name="barangay" x-model="form.barangay" placeholder="Barangay" required class="{{ $inputClass }}">
                                <p class="{{ $errClass }}">{{ $errors->first('barangay') }}</p>
                            </div>
                            <div>
                                <input id="f_city_municipality" name="city_municipality" x-model="form.city_municipality" placeholder="City / Municipality" required class="{{ $inputClass }}">
                                <p class="{{ $errClass }}">{{ $errors->first('city_municipality') }}</p>
                            </div>
                            <div>
                                <input id="f_province" name="province" x-model="form.province" placeholder="Province" required class="{{ $inputClass }}">
                                <p class="{{ $errClass }}">{{ $errors->first('province') }}</p>
                            </div>
                            <div>
                                <input id="f_zip_code" name="zip_code" x-model="form.zip_code" inputmode="numeric" pattern="[0-9]{4}" maxlength="4"
                                    @input="$el.value = $el.value.replace(/\D/g, '')"
                                    placeholder="ZIP Code" required class="{{ $inputClass }}">
                                <p class="{{ $errClass }}">{{ $errors->first('zip_code') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="f_emergency_name" class="{{ $labelClass }}">Emergency Contact Name <span class="text-red-600">*</span></label>
                            <input id="f_emergency_name" name="emergency_name" x-model="form.emergency_name" placeholder="e.g. Ana Santos" required class="{{ $inputClass }}">
                            <p class="{{ $errClass }}">{{ $errors->first('emergency_name') }}</p>
                        </div>
                        <div>
                            <label for="f_emergency_contact" class="{{ $labelClass }}">Emergency Contact Number <span class="text-red-600">*</span></label>
                            <input id="f_emergency_contact" type="tel" name="emergency_contact" x-model="form.emergency_contact" placeholder="+639XXXXXXXXX" required class="{{ $inputClass }}">
                            <p class="{{ $errClass }}">{{ $errors->first('emergency_contact') }}</p>
                        </div>
                    </div>
                    </div>{{-- /personal --}}

                    {{-- Educational background (entered by HR only) --}}
                    <fieldset class="emp-edu-fields" data-ftab="education" x-show="ftab === 'education'" x-cloak role="tabpanel">
                        <legend>Educational Background</legend>
                        <p class="emp-edu-hint">Fill in each level the employee finished, starting with the highest. The school name is needed for every level you fill in.</p>
                        @php($eduErrors = collect($errors->getMessages())->filter(fn ($m, $k) => str_starts_with($k, 'education'))->flatten()->unique())
                        @if ($eduErrors->isNotEmpty())
                            <p class="{{ $errClass }}">{{ $eduErrors->first() }}</p>
                        @endif
                        @foreach (\App\Models\Employee::EDUCATION_LEVELS as $level => $config)
                            <div class="emp-edu-level">
                                <h4>{{ $config['label'] }}</h4>
                                <div class="emp-edu-grid emp-edu-grid--{{ count($config['fields']) }}">
                                    @foreach ($config['fields'] as $field => [$label, $hint, $max])
                                        <div>
                                            <label for="f_edu_{{ $level }}_{{ $field }}" class="{{ $labelClass }}">{{ $label }}</label>
                                            <input id="f_edu_{{ $level }}_{{ $field }}" name="education[{{ $level }}][{{ $field }}]" maxlength="{{ $max }}"
                                                x-model="form.education.{{ $level }}.{{ $field }}" placeholder="{{ $hint }}" class="{{ $inputClass }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </fieldset>

                    {{-- Government IDs (entered by HR only; kept hidden elsewhere) --}}
                    <div class="space-y-4" data-ftab="ids" x-show="ftab === 'ids'" x-cloak role="tabpanel">
                        <p class="emp-edu-hint">These numbers are used for remittances. Leave a field blank if the employee has none yet.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach (['sss_number' => 'SSS Number', 'philhealth_number' => 'PhilHealth Number', 'pagibig_number' => 'Pag-IBIG Number', 'tin' => 'TIN'] as $key => $label)
                                <div>
                                    <label for="f_{{ $key }}" class="{{ $labelClass }}">{{ $label }}</label>
                                    <input id="f_{{ $key }}" name="{{ $key }}" x-model="form.{{ $key }}" maxlength="32" autocomplete="off" placeholder="{{ $label }}" class="{{ $inputClass }}">
                                    <p class="{{ $errClass }}">{{ $errors->first($key) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button type="button" @click="formOpen = false"
                            class="cursor-pointer rounded-lg bg-gray-100 px-6 py-3 font-bold text-gray-800 transition hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" :disabled="!editingId && form.employee_source === 'applicant' && !hasApplicants"
                            class="cursor-pointer rounded-lg bg-[#b22222] px-6 py-3 font-bold text-white transition hover:bg-[#8f1c1c] hover:shadow-[0_6px_20px_rgba(178,34,34,0.4)] disabled:cursor-not-allowed disabled:opacity-60">
                            <span x-text="editingId ? 'Save Changes' : 'Add Employee'"></span>
                        </button>
                    </div>
                </form>
              </div>
            </div>
        </div>
    </div>

    <script>
        function employeeDirectory(cfg) {
            const defaultStep = String(cfg.defaultStep ?? '');

            // { tertiary: { course: '', school: '', years: '' }, ... }
            const blankEducation = () => JSON.parse(@js(json_encode(collect(\App\Models\Employee::EDUCATION_LEVELS)->map(fn ($c) => array_fill_keys(array_keys($c['fields']), '')))));
            const withEducation = (saved) => {
                const edu = blankEducation();
                for (const level in edu) for (const field in edu[level]) edu[level][field] = (saved && saved[level] && saved[level][field]) || '';
                return edu;
            };

            const blank = () => ({
                employee_source: 'applicant',
                user_id: '',
                zkteco_user_id: '', first_name: '', middle_name: '', last_name: '', suffix: '',
                position: '', department: '', role_type: '',
                employment_status: 'regular',
                step_no: defaultStep,
                email: '', birthday: '', date_hired: '', civil_status: '', gender: '',
                address_line: '', address_landmark: '', barangay: '', city_municipality: '', province: '', zip_code: '',
                emergency_name: '', emergency_contact: '',
                education: blankEducation(),
                sss_number: '', philhealth_number: '', pagibig_number: '', tin: '',
            });

            return {
                employees: cfg.employees,
                applicants: cfg.applicants || {},   // { userId: { id, name, email } }
                statuses: cfg.statuses || {},       // { regular: 'Regular', ... }
                detailId: null,
                formOpen: false,
                ftab: 'personal',
                editingId: null,
                form: blank(),

                get detail() {
                    return this.detailId ? this.employees[this.detailId] : null;
                },

                get hasApplicants() {
                    return Object.keys(this.applicants).length > 0;
                },

                init() {
                    // Validation failed on the server: reopen the form with the typed values
                    if (cfg.reopen) {
                        this.editingId = cfg.editingId || null;
                        this.form = Object.assign(blank(), cfg.old);
                        this.form.education = withEducation(cfg.old.education);
                        this.form.employee_source = this.form.employee_source === 'existing' ? 'existing' : 'applicant';
                        this.form.step_no = String(this.form.step_no ?? defaultStep);
                        // Open the tab that holds the first error
                        const keys = cfg.errorKeys || [];
                        this.ftab = keys.some(k => k.startsWith('education')) && !keys.some(k => !k.startsWith('education') && !['sss_number','philhealth_number','pagibig_number','tin'].includes(k)) ? 'education'
                            : keys.length && keys.every(k => ['sss_number','philhealth_number','pagibig_number','tin'].includes(k)) ? 'ids' : 'personal';
                        this.formOpen = true;
                    }
                },

                // Readable label for a stored employment status
                statusLabel(value) {
                    return this.statuses[value] || value || '—';
                },

                fullName(employee) {
                    if (!employee) return '';

                    const name = [
                        employee.first_name,
                        employee.middle_name,
                        employee.last_name,
                        employee.suffix,
                    ]
                        .map(part => String(part || '').trim())
                        .filter(Boolean)
                        .join(' ');

                    return name || employee.name || '';
                },

                // Fill the selected applicant's application details and employee login email.
                pickApplicant() {
                    const a = this.applicants[this.form.user_id];
                    Object.assign(this.form, a
                        ? {
                            first_name: a.first_name || this.splitName(a.name).first_name,
                            middle_name: a.middle_name || this.splitName(a.name).middle_name,
                            last_name: a.last_name || this.splitName(a.name).last_name,
                            suffix: a.suffix || this.splitName(a.name).suffix,
                        }
                        : this.splitName(''));
                    this.form.email = a?.email || '';
                    this.form.birthday = a?.birthday || '';
                    this.form.civil_status = a?.civil_status || '';
                    this.form.gender = a?.gender || '';
                    Object.assign(this.form, a?.address || this.splitAddress(''));
                },

                // Only used as a fallback (applicants without an application, or old rows
                // that have no saved first/middle/last name yet).
                splitName(value) {
                    const parts = String(value || '').trim().split(/\s+/).filter(Boolean);
                    let suffix = '';
                    if (parts.length > 1 && /^(jr\.?|sr\.?|ii|iii|iv|v)$/i.test(parts[parts.length - 1].replace(/,$/, ''))) {
                        suffix = parts.pop().replace(/,$/, '');
                    }

                    return {
                        first_name: parts.shift() || '',
                        middle_name: parts.length > 1 ? parts.slice(0, -1).join(' ') : '',
                        last_name: parts.length ? parts[parts.length - 1] : '',
                        suffix,
                    };
                },

                splitAddress(value) {
                    const parts = String(value || '').split(',').map(part => part.trim());
                    if (parts.length === 6 && /^\d{4}$/.test(parts[5])) {
                        return {
                            address_line: parts[0],
                            address_landmark: parts[1],
                            barangay: parts[2],
                            city_municipality: parts[3],
                            province: parts[4],
                            zip_code: parts[5],
                        };
                    }

                    return {
                        address_line: String(value || ''),
                        address_landmark: '',
                        barangay: '',
                        city_municipality: '',
                        province: '',
                        zip_code: '',
                    };
                },

                selectSource(source) {
                    if (this.form.employee_source === source) return;
                    this.form.employee_source = source;
                    this.form.user_id = '';
                    Object.assign(this.form, this.splitName(''));
                    this.form.email = '';
                    this.form.birthday = '';
                    this.form.date_hired = new Date().toISOString().slice(0, 10);
                    this.form.civil_status = '';
                    this.form.gender = '';
                },

                openDetail(id) { this.detailId = id; },

                closeAll() {
                    this.detailId = null;
                    this.formOpen = false;
                },

                // A required field on a hidden tab failed: show that tab so the browser can point at it
                showTabOf(el) {
                    const panel = el.closest('[data-ftab]');
                    if (!panel || panel.dataset.ftab === this.ftab) return;
                    this.ftab = panel.dataset.ftab;
                    panel.style.display = '';
                },

                openCreate() {
                    this.ftab = 'personal';
                    this.editingId = null;
                    this.form = blank();
                    this.detailId = null;
                    this.formOpen = true;
                },

                openEdit() {
                    const e = this.detail;
                    if (!e) return;

                    // Use the saved first/middle/last/suffix columns.
                    // Fall back to splitting `name` only if the row has no saved parts yet.
                    const hasParts = e.first_name || e.last_name;
                    const nameParts = hasParts
                        ? {
                            first_name: e.first_name || '',
                            middle_name: e.middle_name || '',
                            last_name: e.last_name || '',
                            suffix: e.suffix || '',
                        }
                        : this.splitName(e.name);

                    this.form = {
                        employee_source: 'existing',
                        user_id: '',
                        zkteco_user_id: e.zkteco_user_id || '', ...nameParts, position: e.position,
                        department: e.department, role_type: e.role_type,
                        employment_status: e.employment_status || 'regular',
                        // Use the employee's saved step (needs 'step_no' in the card data)
                        step_no: String(e.step_no ?? defaultStep),
                        email: e.email,
                        birthday: e.birthday, date_hired: e.date_hired || '', civil_status: e.civil_status || '', gender: e.gender || '',
                        ...this.splitAddress(e.address),
                        emergency_name: e.emergency_name, emergency_contact: e.emergency_contact,
                        education: withEducation(e.education_raw),
                        ...Object.fromEntries(['sss_number', 'philhealth_number', 'pagibig_number', 'tin']
                            .map((k, i) => [k, (e.gov_ids || [])[i]?.value || ''])),
                    };
                    this.ftab = 'personal';
                    this.editingId = e.id;
                    this.detailId = null;
                    this.formOpen = true;
                },

                formAction() {
                    return this.editingId ? cfg.updateUrl.replace('__ID__', this.editingId) : cfg.storeUrl;
                },

                deleteAction() {
                    return cfg.destroyUrl.replace('__ID__', this.detailId);
                },
            };
        }
    </script>