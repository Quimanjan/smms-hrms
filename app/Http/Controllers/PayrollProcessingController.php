<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Payslip;
use App\Models\User;
use App\Services\CashAdvancePayrollService;
use App\Support\PayrollCalculator;
use App\Support\AttendanceSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollProcessingController extends Controller
{
    private const MONTHLY_DAYS       = 26;        // monthly wage = daily wage x 26
    private const LATE_PER_MINUTE    = 1.80;
    private const MALASAKITAN        = 50.00;     // every 15th and 30th pay period
    private const HONORARIUM         = 1900.00;   // administrative role only

    /** Contribution rates, deducted on the 30th pay period only. */
    private const RATES = [
        'philhealth' => 0.025,
        'peraa_ceap' => 0.04,
    ];
    private const HDMF_CONTRIBUTION = 200.00;

    /** Deduction columns of payroll_records that Accounting still enters manually. */
    private const DED = ['sss_loan', 'philhealth_loan', 'peraa_ceap_loan', 'hdmf_loan', 'cash_advance'];

    /** Columns that are auto-computed; stored as 0 in payroll_records. */
    private const AUTO = ['sss', 'philhealth', 'peraa_ceap', 'hdmf'];

    private const DEFAULT_WAGES = [
        550.00, 577.50, 606.38, 636.70, 668.53, 701.96, 737.06, 773.91, 812.60, 853.23,
        895.89, 940.69, 987.72, 1037.11, 1088.96, 1142.13, 1199.24, 1259.20, 1322.16, 1388.27,
        1457.68, 1530.56, 1607.09, 1687.45, 1771.82, 1860.41, 1953.43, 2051.10, 2153.66, 2261.34,
    ];

    /** Accounting only: payroll calculation for one month + pay period. */
    public function index(Request $request)
    {
        $this->authorizeAccounting();

        [$period, $cutoff] = $this->periodAndCutoff($request);
        $inputs = $this->inputs($period, $cutoff);
        $this->persistPayrollRecords($inputs, $period, $cutoff);

        $confirmed = $this->dtrConfirmedIds($period, $cutoff);

        $steps = $this->steps();

        $employees = $inputs->map(fn ($x) => [
            // Calculated on the server with App\Support\PayrollCalculator; the browser only
            // swaps in the loan amounts being edited and takes deductions in the same order.
            'pay'            => $this->screenPay($this->compute($x['in'], $steps, $cutoff, $x['employee'])),
            'contributions'  => [
                'sss_ee' => $x['employee']->sss_ee, 'sss_er' => $x['employee']->sss_er,
                'philhealth_ee' => $x['employee']->philhealth_ee, 'philhealth_er' => $x['employee']->philhealth_er,
                'pagibig_ee' => $x['employee']->pagibig_ee, 'pagibig_er' => $x['employee']->pagibig_er,
            ],
            'undertimeMinutes' => (float) $x['in']['undertime_minutes'],
            'leaveDays'      => (float) $x['in']['leave_days'],
            'holidayDays'    => (float) $x['in']['holiday_days'],
            'id'             => $x['employee']->id,
            'dtrConfirmed'   => $confirmed->contains($x['employee']->id),
            'name'           => $x['employee']->name,
            // The ZKTeco user ID is the employee ID (falls back to the old employee_id)
            'employeeNumber' => $x['employee']->zkteco_user_id ?? $x['employee']->employee_id,
            'department'     => Employee::DEPARTMENTS[$x['employee']->department] ?? $x['employee']->department,
            'position'       => $x['employee']->position,
            'isAdmin'        => $this->isAdministrative($x['employee']),
            'step'           => (int) $x['in']['step_no'],
            'daysWorked'     => (float) $x['in']['days_worked'],
            'hoursWorked'    => (float) $x['in']['hours_worked'],
            'lateMinutes'    => (float) $x['in']['late_minutes'],
            'unpaidLeaveDays' => (float) $x['in']['unpaid_leave_days'],
            'honorarium'     => $this->isAdministrative($x['employee']) ? self::HONORARIUM : 0.0,
            'deductions'     => [
                'sssLoan'        => (float) $x['in']['sss_loan'],
                'philhealthLoan' => (float) $x['in']['philhealth_loan'],
                'peraaCeapLoan'  => (float) $x['in']['peraa_ceap_loan'],
                'hdmfLoan'       => (float) $x['in']['hdmf_loan'],
                'cashAdvance'    => (float) $x['in']['cash_advance'],   // prior month's approved total
            ],
        ])->values();

        [$rangeStart, $rangeEnd] = $this->cutoffDateRange($period, $cutoff);

        return view('payroll-processing', [
            'employees' => $employees,
            'steps'     => $this->steps(),
            'period'    => $period,
            'cutoff'    => $cutoff,
            // Dates of the attendance records that are counted for this pay period
            'attendanceRange' => Carbon::parse($rangeStart)->format('M j')
                . ' - ' . Carbon::parse($rangeEnd)->format('M j, Y'),
        ]);
    }

    /** Autosave of the table (called with fetch). Writes payroll_records only. */
    public function save(Request $request): JsonResponse
    {
        $this->authorizeAccounting();

        [$period, $cutoff] = $this->periodAndCutoff($request);

        $data = $request->validate([
            'rows'                    => ['required', 'array', 'min:1'],
            'rows.*.employee_id'      => ['required', 'integer', 'exists:employees,id'],
            'rows.*.step_no'          => ['required', 'integer', 'min:1', 'max:200'],
            'rows.*.days_worked'      => ['required', 'numeric', 'min:0', 'max:31'],
            'rows.*.hours_worked'     => ['required', 'numeric', 'min:0', 'max:744'],
            'rows.*.late_minutes'     => ['required', 'numeric', 'min:0', 'max:99999'],
            'rows.*.sss_loan'         => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'rows.*.philhealth_loan'  => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'rows.*.peraa_ceap_loan'  => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'rows.*.hdmf_loan'        => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            // Government contributions, entered once per employee (monthly amounts).
            'rows.*.contributions'               => ['nullable', 'array'],
            'rows.*.contributions.sss_ee'        => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'rows.*.contributions.sss_er'        => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'rows.*.contributions.philhealth_ee' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'rows.*.contributions.philhealth_er' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'rows.*.contributions.pagibig_ee'    => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'rows.*.contributions.pagibig_er'    => ['nullable', 'numeric', 'min:0', 'max:999999'],
        ]);

        // Save the one-time contribution amounts on the employee record.
        foreach ($data['rows'] as $r) {
            if (! array_key_exists('contributions', $r) || ! is_array($r['contributions'])) {
                continue;
            }
            $values = collect(['sss_ee', 'sss_er', 'philhealth_ee', 'philhealth_er', 'pagibig_ee', 'pagibig_er'])
                ->mapWithKeys(fn ($k) => [$k => ($r['contributions'][$k] ?? null) === null || $r['contributions'][$k] === '' ? null : round((float) $r['contributions'][$k], 2)])
                ->all();
            DB::table('employees')->where('id', $r['employee_id'])->update($values);
        }

        $requestedEmployeeIds = collect($data['rows'])->pluck('employee_id')->map(fn ($id) => (int) $id);
        $employees = Employee::excludeInternalStaff()
            ->whereIn('id', $requestedEmployeeIds)
            ->get()
            ->keyBy('id');

        if ($requestedEmployeeIds->diff($employees->keys())->isNotEmpty()) {
            return response()->json([
                'message' => 'One or more selected employees are not eligible for payroll processing.',
            ], 422);
        }

        $attendance = $this->attendanceRecordsFor($employees, $period, $cutoff);
        $saved = DB::table('payroll_records')
            ->where(['period' => $period, 'cutoff' => $cutoff])
            ->whereIn('employee_id', $employees->keys())
            ->get()
            ->keyBy('employee_id');
        $cashAdvancePayroll = app(CashAdvancePayrollService::class);
        $cashAdvances = $cashAdvancePayroll->loansForPeriod($period, $employees->keys())
            ->groupBy('employee_id');
        $now       = now();

        $rows = collect($data['rows'])->map(function ($r) use ($employees, $attendance, $saved, $cashAdvances, $cashAdvancePayroll, $period, $cutoff, $now) {
            $employee = $employees[$r['employee_id']];
            // Attendance records decide days / hours / late; the browser values are ignored
            $metrics = $this->metricsFor(
                $employee,
                $attendance->get($employee->id, collect()),
                $saved->get($employee->id)
            );
            $row = [
                'employee_id'  => $r['employee_id'],
                'step_no'      => $r['step_no'],
                'days_worked'  => $metrics['days_worked'],
                'hours_worked' => $metrics['hours_worked'],
                'late_minutes' => $metrics['late_minutes'],
                'undertime_minutes' => $metrics['undertime_minutes'],
                'leave_days'   => $metrics['leave_days'],
                'holiday_days' => $metrics['holiday_days'],
                // honorarium is fixed by role; the browser value is ignored
                'honorarium'   => $this->isAdministrative($employee) ? self::HONORARIUM : 0,
                'period'       => $period,
                'cutoff'       => $cutoff,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
            foreach (self::DED as $k) {
                $row[$k] = $k === 'cash_advance'
                    ? $cashAdvancePayroll->total($cashAdvances->get($employee->id, collect()))
                    : ($r[$k] ?? 0);
            }
            foreach (self::AUTO as $k) {
                $row[$k] = 0;   // auto-computed on the fly, nothing to store
            }
            return $row;
        })->all();

        $steps = $this->steps();
        $transferDate = Carbon::createFromFormat('Y-m-d', $period . '-01')->toDateString();

        DB::transaction(function () use ($rows, $employees, $steps, $cutoff, $transferDate) {
            DB::table('payroll_records')->upsert(
                $rows,
                ['period', 'cutoff', 'employee_id'],
                array_merge(
                    ['step_no', 'days_worked', 'hours_worked', 'late_minutes', 'undertime_minutes', 'leave_days', 'holiday_days', 'honorarium', 'updated_at'],
                    self::DED,
                    self::AUTO
                )
            );

            foreach ($rows as $row) {
                $employee = $employees[$row['employee_id']];
                $payslip = Payslip::where([
                    'employee_id' => $row['employee_id'],
                    'cutoff' => $cutoff,
                    'transfer_date' => $transferDate,
                ])->first();

                if (! $payslip || $payslip->released_at) {
                    continue;
                }

                $values = $this->payslipValues($row, $steps, $cutoff, $employee);
                $changed = collect($values)->contains(function ($value, $key) use ($payslip) {
                    $current = $payslip->getAttribute($key);

                    if (is_numeric($value) && is_numeric($current)) {
                        return round((float) $value, 2) !== round((float) $current, 2);
                    }

                    return $value !== $current;
                });

                if (! $changed) {
                    continue;
                }

                $payslip->update($values + [
                    'sent_to_admin_at'       => null,
                    'admin_status'           => Payslip::PENDING,
                    'admin_rejection_reason' => null,
                    'admin_reviewed_at'      => null,
                    'admin_reviewed_by'      => null,
                    'hr_status'              => Payslip::PENDING,
                    'hr_rejection_reason'    => null,
                    'hr_reviewed_at'         => null,
                    'hr_reviewed_by'         => null,
                ]);
            }
        });

        return response()->json(['saved' => count($rows)]);
    }

    /** Save the daily wage table (payroll_steps). */
    public function saveSteps(Request $request): JsonResponse
    {
        $this->authorizeAccounting();

        $data = $request->validate([
            'steps'             => ['required', 'array', 'min:1', 'max:60'],
            'steps.*.name'      => ['nullable', 'string', 'max:60'],
            'steps.*.dailyWage' => ['required', 'numeric', 'min:0', 'max:99999999'],
        ]);

        DB::transaction(function () use ($data) {
            DB::table('payroll_steps')->delete();
            $now = now();
            foreach (array_values($data['steps']) as $i => $s) {
                DB::table('payroll_steps')->insert([
                    'step_no'    => $i + 1,
                    'name'       => trim($s['name'] ?? '') ?: 'Step ' . ($i + 1),
                    'daily_wage' => round((float) $s['dailyWage'], 2),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        return response()->json(['steps' => $this->steps()]);
    }

    /**
     * Transfer the SELECTED employees to Salary Monitoring. Reads the SAVED payroll_records
     * and recalculates everything on the server; nothing the browser calculated is trusted.
     */
    public function transfer(Request $request): RedirectResponse
    {
        $this->authorizeAccounting();

        [$period, $cutoff] = $this->periodAndCutoff($request);

        $ids = collect($request->validate([
            'employee_ids'   => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ], [
            'employee_ids.required' => 'Select at least one employee to transfer.',
        ])['employee_ids'])->map(fn ($id) => (int) $id);

        $steps        = $this->steps();
        $transferDate = Carbon::createFromFormat('Y-m-d', $period . '-01')->toDateString();
        $sent = 0;

        $inputs = $this->inputs($period, $cutoff);
        $eligibleEmployeeIds = $inputs->pluck('employee.id');

        if ($ids->diff($eligibleEmployeeIds)->isNotEmpty()) {
            return back()->withErrors([
                'employee_ids' => 'One or more selected employees are not eligible for payroll processing.',
            ]);
        }

        // The employee must confirm their DTR for this pay period first.
        $confirmed = $this->dtrConfirmedIds($period, $cutoff);
        $unconfirmed = $inputs
            ->filter(fn ($x) => $ids->contains($x['employee']->id) && ! $confirmed->contains($x['employee']->id))
            ->map(fn ($x) => $x['employee']->name);

        if ($unconfirmed->isNotEmpty()) {
            return back()->withErrors([
                'employee_ids' => 'These employees have not confirmed their DTR for this pay period yet: '
                    .$unconfirmed->join(', ', ' and ').'.',
            ]);
        }

        $selected = $inputs
            ->filter(fn ($x) => $ids->contains($x['employee']->id));

        DB::transaction(function () use ($selected, $cutoff, $steps, $transferDate, &$sent) {
            foreach ($selected as $x) {
                $existing = Payslip::where([
                    'employee_id' => $x['employee']->id, 'cutoff' => $cutoff, 'transfer_date' => $transferDate,
                ])->first();

                if ($existing) {
                    $existing->response()->delete();
                }

                Payslip::updateOrCreate(
                    ['employee_id' => $x['employee']->id, 'cutoff' => $cutoff, 'transfer_date' => $transferDate],
                    $this->payslipValues($x['in'], $steps, $cutoff, $x['employee']) + [

                        // a fresh transfer restarts the approval chain
                        'sent_to_admin_at'       => null,
                        'admin_status'           => Payslip::PENDING,
                        'admin_rejection_reason' => null,
                        'admin_reviewed_at'      => null,
                        'admin_reviewed_by'      => null,
                        'hr_status'              => Payslip::PENDING,
                        'hr_rejection_reason'    => null,
                        'hr_reviewed_at'         => null,
                        'hr_reviewed_by'         => null,
                        'released_at'            => null,
                    ]
                );
                $sent++;
            }
        });

        $msg = "Transferred {$sent} payslip" . ($sent === 1 ? '' : 's') . " ({$cutoff} pay period, {$period}) to Salary Monitoring.";

        return redirect()->route('salary-monitoring.index')->with('success', $msg);
    }

    // ------------------------------------------------------------------

    /** Every employee with their saved payroll_record, or defaults when none exists yet. */
    private function inputs(string $period, string $cutoff)
    {
        $records = DB::table('payroll_records')
            ->where(['period' => $period, 'cutoff' => $cutoff])
            ->get()->keyBy('employee_id');

        $defaults = array_merge(
            ['step_no' => 1, 'days_worked' => 0, 'hours_worked' => 0, 'late_minutes' => 0, 'undertime_minutes' => 0,
                'leave_days' => 0, 'holiday_days' => 0, 'honorarium' => 0],
            array_fill_keys(array_merge(self::DED, self::AUTO), 0)
        );

        $employees = Employee::excludeInternalStaff()->orderBy('name')->get();
        $attendance = $this->attendanceRecordsFor($employees, $period, $cutoff);
        $cashAdvancePayroll = app(CashAdvancePayrollService::class);
        $cashAdvances = $cashAdvancePayroll->loansForPeriod($period, $employees->pluck('id'))
            ->groupBy('employee_id');

        return $employees->map(function (Employee $employee) use ($records, $defaults, $attendance, $cashAdvances, $cashAdvancePayroll) {
            $inputs = array_merge($defaults, (array) ($records[$employee->id] ?? []));
            $inputs['step_no'] = (int) $employee->step_no;
            $inputs['honorarium'] = $this->isAdministrative($employee) ? self::HONORARIUM : 0;
            $inputs['cash_advance'] = $cashAdvancePayroll->total(
                $cashAdvances->get($employee->id, collect())
            );
            $inputs = array_merge(
                $inputs,
                $this->metricsFor(
                    $employee,
                    $attendance->get($employee->id, collect()),
                    $records->get($employee->id)
                )
            );

            return [
                'employee' => $employee,
                'in'       => $inputs,
            ];
        });
    }

    private function persistPayrollRecords(Collection $employees, string $period, string $cutoff): void
    {
        $now = now();
        $rows = $employees->map(function (array $entry) use ($period, $cutoff, $now) {
            $inputs = $entry['in'];

            return [
                'employee_id' => $entry['employee']->id,
                'period' => $period,
                'cutoff' => $cutoff,
                'step_no' => $inputs['step_no'],
                'days_worked' => $inputs['days_worked'],
                'hours_worked' => $inputs['hours_worked'],
                'late_minutes' => $inputs['late_minutes'],
                'undertime_minutes' => $inputs['undertime_minutes'],
                'leave_days' => $inputs['leave_days'],
                'holiday_days' => $inputs['holiday_days'],
                'honorarium' => $inputs['honorarium'],
                ...array_intersect_key($inputs, array_flip(array_merge(self::DED, self::AUTO))),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        if ($rows === []) {
            return;
        }

        DB::table('payroll_records')->upsert(
            $rows,
            ['period', 'cutoff', 'employee_id'],
            array_merge(
                ['step_no', 'days_worked', 'hours_worked', 'late_minutes', 'undertime_minutes', 'leave_days', 'holiday_days', 'honorarium', 'updated_at'],
                self::DED,
                self::AUTO
            )
        );
    }

    /**
     * Attendance records (attendance_records) of the employees inside the pay period window.
     * Archived records are INCLUDED: archiving only moves a record to the Archive tab of
     * the attendance page, it must not remove the hours from payroll.
     */
    private function attendanceRecordsFor(Collection $employees, string $period, string $cutoff): Collection
    {
        if ($employees->isEmpty()) {
            return collect();
        }

        [$start, $end] = $this->cutoffDateRange($period, $cutoff);

        return AttendanceRecord::query()
            ->whereIn('employee_id', $employees->pluck('id'))
            ->whereBetween('date', [$start, $end])
            ->with('nonWorkingDay')
            ->get()
            ->groupBy('employee_id');
    }

    /** Employees who confirmed their DTR for this pay period. */
    private function dtrConfirmedIds(string $period, string $cutoff): Collection
    {
        return \App\Models\DtrConfirmation::query()
            ->where('period', $period)
            ->where('cutoff', $cutoff)
            ->pluck('employee_id')
            ->map(fn ($id) => (int) $id);
    }

    /**
     * 15th pay period = 21st of the previous month .. 5th of the period month
     * 30th pay period = 6th .. 20th of the period month
     */
    private function cutoffDateRange(string $period, string $cutoff): array
    {
        $month = Carbon::createFromFormat('Y-m-d', $period.'-01');

        return $cutoff === '15th'
            ? [$month->copy()->subMonthNoOverflow()->day(21)->toDateString(), $month->copy()->day(5)->toDateString()]
            : [$month->copy()->day(6)->toDateString(), $month->copy()->day(20)->toDateString()];
    }

    /**
     * Days / hours / late come ONLY from attendance_records inside the pay period window.
     * Nothing is taken from older payroll_records values: no attendance record = 0.
     */
    private function metricsFor(Employee $employee, Collection $records, ?object $saved = null): array
    {
        return $this->attendanceMetrics($employee, $records);
    }

    private function attendanceMetrics(Employee $employee, iterable $records): array
    {
        $daysWorked = 0.0;
        $hoursWorked = 0.0;
        $lateMinutes = 0;
        $undertimeMinutes = 0;
        $leaveDays = 0.0;
        $holidayDays = 0.0;
        $unpaidLeaveDays = 0.0;

        // The client: every holiday is paid one daily wage, regular or special, even
        // without reporting. School suspensions follow the pay setting HR chose.
        $holidayOf = function ($record) {
            $day = $record instanceof AttendanceRecord
                ? ($record->relationLoaded('nonWorkingDay') ? $record->getRelation('nonWorkingDay') : null)
                : ($record->nonWorkingDay ?? null);

            return $day;
        };
        $isHoliday = fn ($day) => $day && in_array($day->category, ['National Holiday', 'Local Holiday'], true);

        foreach ($records as $record) {
            $date = Carbon::parse($record->date)->startOfDay();
            $schedule = AttendanceSchedule::forEmployee($employee->role_type, $employee->position, $date);
            $timeIn = AttendanceSchedule::toCarbon($date, $record->time_in);
            $timeOut = AttendanceSchedule::toCarbon($date, $record->time_out);
            $computedHours = $schedule->hoursWorked($timeIn, $timeOut);
            $day = $holidayOf($record);

            if ($record->status === Attendance::NWD_REVIEW) {
                // Came in on a non-working day: the work counts, and a holiday is still paid.
                if ($timeIn && $timeOut) {
                    $hoursWorked += $computedHours;
                    $daysWorked += $computedHours / 8;
                }
                if ($isHoliday($day)) {
                    $holidayDays += 1;
                }

                continue;
            }

            if ($record->status === Attendance::PAID_LEAVE) {
                $leaveDays += 1;

                continue;
            }

            if ($record->status === Attendance::PAID_HOLIDAY) {
                $holidayDays += $isHoliday($day) ? 1 : (float) ($day?->paid_days ?? 1);

                continue;
            }

            if ($record->status === Attendance::NON_WORKING_DAY) {
                if ($isHoliday($day)) {
                    $holidayDays += 1;
                }

                continue;
            }

            if ($record->status === Attendance::UNPAID_LEAVE) {
                // Unpaid leave contributes no paid days or hours, reducing basic pay.
                $unpaidLeaveDays += 1;

                continue;
            }

            $daysWorked += Attendance::computeDaysWorked($record->status);
            $hoursWorked += $timeIn && $timeOut
                ? $computedHours
                : (float) $record->hours_worked;
            $lateMinutes += $schedule->lateMinutes($timeIn);

            // A half day is already paid as half; only full days count undertime.
            if ($record->status !== Attendance::HALF_DAY && $timeOut) {
                $undertimeMinutes += $schedule->undertimeMinutes($timeOut);
            }
        }

        return [
            'days_worked' => round($daysWorked, 2),
            'hours_worked' => round($hoursWorked, 2),
            'late_minutes' => $lateMinutes,
            'undertime_minutes' => $undertimeMinutes,
            'leave_days' => round($leaveDays, 2),
            'holiday_days' => round($holidayDays, 2),
            'unpaid_leave_days' => round($unpaidLeaveDays, 2),
        ];
    }

    /** Payroll rules live in App\Support\PayrollCalculator. */
    private function payslipValues(array $inputs, array $steps, string $cutoff, Employee $employee): array
    {
        $pay = $this->compute($inputs, $steps, $cutoff, $employee);
        $d = $pay['breakdown'];

        return [
            'step_name'           => $pay['step']['name'],
            'days_worked'         => $pay['days'],
            'hours_worked'        => $pay['hours'],
            'late_minutes'        => (int) round($pay['lateMinutes']),
            'undertime_minutes'   => (int) round($pay['undertimeMinutes']),
            'leave_days'          => $pay['leaveDays'],
            'holiday_days'        => $pay['holidayDays'],
            'basic_pay'           => $pay['basicPay'],
            'leave_pay'           => $pay['leavePay'],
            'holiday_pay'         => $pay['holidayPay'],
            'daily_wage'          => $pay['step']['dailyWage'],
            'honorarium'          => $pay['honorarium'],
            'gross_pay'           => $pay['grossPay'],
            'malasakitan'         => $d['malasakitan'],
            'late_deduction'      => $d['late'],
            'undertime_deduction' => $d['undertime'],
            'absences_deduction'  => 0.0,
            'sss'                 => $d['sss'],
            'sss_loan'            => $d['sss_loan'],
            'philhealth'          => $d['philhealth'],
            'philhealth_loan'     => $d['philhealth_loan'],
            'peraa_ceap'          => $d['peraa_ceap'],
            'peraa_ceap_loan'     => $d['peraa_ceap_loan'],
            'hdmf'                => $d['hdmf'],
            'hdmf_loan'           => $d['hdmf_loan'],
            'cash_advance'        => $d['cash_advance'],
            'withholding_tax'     => $d['withholding_tax'],
            'sss_er'              => $pay['employer']['sss'],
            'philhealth_er'       => $pay['employer']['philhealth'],
            'hdmf_er'             => $pay['employer']['hdmf'],
            'total_deductions'    => $pay['totalDeductions'],
            'net_pay'             => $pay['netPay'],
        ];
    }

    /** The parts of a calculation the payroll screen needs. */
    private function screenPay(array $pay): array
    {
        return [
            'monthlyWage' => $pay['monthlyWage'],
            'basicPay' => $pay['basicPay'],
            'leavePay' => $pay['leavePay'],
            'holidayPay' => $pay['holidayPay'],
            'honorarium' => $pay['honorarium'],
            'grossPay' => $pay['grossPay'],
            'wanted' => $pay['wanted'],
            'employer' => $pay['employer'],
            'contributionsEntered' => $pay['contributions']['entered'],
            'order' => PayrollCalculator::ORDER,
        ];
    }

    private function compute(array $r, array $steps, string $cutoff, Employee $employee): array
    {
        $step = $steps[((int) $r['step_no']) - 1] ?? ['name' => 'Unassigned', 'dailyWage' => 0];
        $pay = PayrollCalculator::compute($r, (float) $step['dailyWage'], $cutoff, $this->isAdministrative($employee), $employee);
        $pay['step'] = ['name' => $step['name'], 'dailyWage' => (float) $step['dailyWage']];

        return $pay;
    }

    private function sssContribution(float $monthlyWage): float
    {
        if ($monthlyWage < 12750) {
            return 0;
        }

        return min(1000, 650 + floor(($monthlyWage - 12750) / 500) * 25);
    }

    /** Administrative task/role gets the fixed honorarium. Adjust to your employees table. */
    private function isAdministrative(Employee $e): bool
    {
        $roleType = strtolower(trim((string) $e->role_type));
        $position = strtolower(trim((string) $e->position));

        return $roleType === 'administrative staff'
            || str_contains($position, 'administrat')
            || strtolower(trim((string) $e->department)) === 'administrative';
    }

    /**
     * Period + pay period from the request. When the page is opened with neither,
     * it opens on the pay period that is running today, so the latest attendance shows up:
     *   day 1-5   -> this month, 15th   (window: 21st last month .. 5th)
     *   day 6-20  -> this month, 30th   (window: 6th .. 20th)
     *   day 21-31 -> next month, 15th   (window: 21st this month .. 5th next month)
     */
    private function periodAndCutoff(Request $request): array
    {
        $v = validator($request->all(), [
            'period' => ['nullable', 'date_format:Y-m'],
            'cutoff' => ['nullable', 'in:15th,30th'],
        ])->validate();

        if (empty($v['period']) && empty($v['cutoff'])) {
            return $this->currentPayPeriod();
        }

        return [$v['period'] ?? now()->format('Y-m'), $v['cutoff'] ?? '15th'];
    }

    private function currentPayPeriod(): array
    {
        $today = now();

        if ($today->day <= 5) {
            return [$today->format('Y-m'), '15th'];
        }

        if ($today->day <= 20) {
            return [$today->format('Y-m'), '30th'];
        }

        return [$today->copy()->addMonthNoOverflow()->format('Y-m'), '15th'];
    }

    /** Steps from the DB, seeded with the default wage table the first time. */
    private function steps(): array
    {
        if (DB::table('payroll_steps')->count() === 0) {
            $now = now();
            foreach (self::DEFAULT_WAGES as $i => $w) {
                DB::table('payroll_steps')->insert([
                    'step_no' => $i + 1, 'name' => 'Step ' . ($i + 1), 'daily_wage' => $w,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        return DB::table('payroll_steps')->orderBy('step_no')->get()
            ->map(fn ($s) => ['name' => $s->name, 'dailyWage' => (float) $s->daily_wage])
            ->all();
    }

    private function authorizeAccounting(): void
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User && $user->hasRole('Accounting'),
            403,
            'Only Accounting can process payroll.'
        );
    }
}