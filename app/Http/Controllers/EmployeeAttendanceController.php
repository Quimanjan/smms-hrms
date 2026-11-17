<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\DtrConfirmation;
use App\Models\NonWorkingDay;
use App\Support\PayPeriod;
use App\Models\SchoolEmployee;
use App\Support\AttendanceSchedule;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeAttendanceController extends Controller
{
    /** Longest range one timesheet may cover. */
    private const MAX_DAYS = 93;

    /**
     * The logged-in employee's Daily Time Record (DTR) for a date range.
     * Every day in the range is listed, including rest days and holidays.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401, 'Please log in to continue.');

        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'pay'  => ['nullable', 'regex:/^\d{4}-\d{2}\|(15th|30th)$/'],
        ]);

        $today = now()->startOfDay();
        $payPeriods = PayPeriod::recent(6);

        // Choosing a pay period fills in its dates. Opening the page with
        // nothing chosen shows the pay period running today.
        $pay = $filters['pay'] ?? null;
        if (! $pay && ! isset($filters['from']) && ! isset($filters['to'])) {
            $pay = $payPeriods[0]['key'];
        }
        if ($pay) {
            [$p, $c] = explode('|', $pay);
            [$filters['from'], $filters['to']] = PayPeriod::range($p, $c);
        }

        $from = isset($filters['from']) ? Carbon::parse($filters['from'])->startOfDay() : $today->copy()->startOfMonth();
        $to = isset($filters['to']) ? Carbon::parse($filters['to'])->startOfDay() : $today->copy();

        if ($from->diffInDays($to) >= self::MAX_DAYS) {
            $to = $from->copy()->addDays(self::MAX_DAYS - 1);
        }

        $employee = $this->employeeFor($user);

        $data = [
            'employee' => $employee,
            'from'     => $from,
            'to'       => $to,
            'maxDays'  => self::MAX_DAYS,
            'rows'     => [],
            'payPeriods' => $payPeriods,
            'payPeriod'  => $this->matchingPayPeriod($from, $to),
            'confirmation' => null,
            'summary'  => ['present' => 0, 'late' => 0, 'late_minutes' => 0, 'half_day' => 0, 'absent' => 0, 'hours' => 0.0],
        ];

        if (! $employee) {
            return view('attendance', $data);
        }

        $records = AttendanceRecord::query()
            ->where('employee_id', $employee->id)
            ->where('archived', false)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->keyBy(fn ($r) => Carbon::parse($r->date)->toDateString());

        // Approved half-day leaves in the range: which half (morning or afternoon) was taken.
        $halfLeaves = $employee->user_id
            ? \App\Models\LeaveRequest::query()
                ->forUser($employee->user_id)
                ->approved()
                ->where('half_day', true)
                ->whereBetween('start_date', [$from->toDateString(), $to->toDateString()])
                ->get()
                ->keyBy(fn ($l) => Carbon::parse($l->start_date)->toDateString())
            : collect();

        $nonWorking = NonWorkingDay::query()
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->keyBy(fn ($d) => Carbon::parse($d->date)->toDateString());

        $proctoring = \App\Models\ProctoringDay::query()
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->keyBy(fn ($d) => Carbon::parse($d->date)->toDateString());

        $role = AttendanceSchedule::isMaintenanceEmployee($employee->role_type, $employee->position)
            ? 'maintenance'
            : $employee->role_type;

        $rows = [];
        $summary = $data['summary'];

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $key = $date->toDateString();
            $record = $records->get($key);
            $holiday = $nonWorking->get($key);
            $schedule = AttendanceSchedule::forEmployee($employee->role_type, $employee->position, $date);
            $status = $record?->status;

            // What kind of day this is, for the Shift Type column and the row color.
            if (in_array($status, [Attendance::PAID_LEAVE, Attendance::UNPAID_LEAVE], true)) {
                [$type, $shiftType] = ['leave', $status];
            } elseif ($holiday) {
                $type = $holiday->category === 'National Holiday' ? 'holiday' : 'special';
                $shiftType = $holiday->name;
            } elseif (! AttendanceSchedule::isWorkingDay($role, $date)) {
                [$type, $shiftType] = ['rest', 'Rest Day'];
            } elseif ($proctoring->has($key)) {
                // A working day, only labelled and colored differently.
                [$type, $shiftType] = ['proctoring', 'Proctoring'];
            } else {
                [$type, $shiftType] = ['regular', 'Regular'];
            }

            $scheduled = in_array($type, ['regular', 'proctoring', 'leave'], true);
            $timeIn = $record ? AttendanceSchedule::toCarbon($date, $record->time_in) : null;
            $timeOut = $record ? AttendanceSchedule::toCarbon($date, $record->time_out) : null;

            $required = match (true) {
                $scheduled => $schedule->requiredHours($date),
                in_array($type, ['holiday', 'special'], true) && $holiday?->isPaid() => (float) $holiday->paid_hours,
                default => 0.0,
            };

            // Half-day leave: only half the day is leave (paid as half a day); the other half
            // counts only if the employee came in.
            [$shiftIn, $shiftOut] = $scheduled ? [$this->clock($schedule->start), $this->clock($schedule->end)] : ['', ''];
            $halfLeave = $halfLeaves->get($key);
            if ($halfLeave && $record && (float) $record->leave_portion > 0 && (float) $record->leave_portion < 1) {
                $pm = $halfLeave->half_day_period === 'pm';
                $halfLabel = $pm ? 'afternoon' : 'morning';
                if ($type === 'leave') {
                    // Did not come in for the other half: only the leave half is on the DTR.
                    $shiftType = $status.' (half day, '.$halfLabel.')';
                    $required = round($required / 2, 2);
                    [$shiftIn, $shiftOut] = $pm
                        ? [$this->clock($schedule->lunchEnd), $this->clock($schedule->end)]
                        : [$this->clock($schedule->start), $this->clock($schedule->lunchStart)];
                } else {
                    $shiftType .= ' + half-day leave ('.$halfLabel.')';
                }
            }

            $onHalfLeave = $record && (float) $record->leave_portion > 0;
            $late = match (true) {
                ! in_array($type, ['regular', 'proctoring'], true) || ! $timeIn => 0,
                $onHalfLeave => $schedule->lateMinutesOnHalfLeave($timeIn),
                default => (int) $schedule->lateMinutes($timeIn),
            };
            // No undertime on a half day or a day with half-day leave.
            $isHalf = $record && ($record->status === Attendance::HALF_DAY || (float) $record->leave_portion > 0);
            $under = in_array($type, ['regular', 'proctoring'], true) && $timeOut && ! $isHalf ? (int) $schedule->undertimeMinutes($timeOut) : 0;
            $hours = $record ? $this->hoursForRecord($record, $schedule) : 0.0;

            if (! $status) {
                $status = match (true) {
                    $date->gt($today) => 'Upcoming',
                    in_array($type, ['regular', 'proctoring'], true) => $date->eq($today) ? 'No punch yet' : 'No record',
                    default => '',
                };
            }

            $rows[] = [
                'date'        => $key,
                'date_label'  => $date->format('M d, Y'),
                'day'         => $date->format('l'),
                'type'        => $type,
                'shift_type'  => $shiftType,
                'shift_in'    => $shiftIn,
                'shift_out'   => $shiftOut,
                'time_in'     => $timeIn ? $timeIn->format('h:i A') : '',
                'time_out'    => $timeOut ? $timeOut->format('h:i A') : '',
                'required'    => number_format($required, 2),
                'tardiness'   => $this->duration($late),
                'undertime'   => $this->duration($under),
                // Shown when the row is expanded.
                'hours'       => number_format($hours, 2),
                'status'      => $status,
                'lunch'       => $scheduled ? $this->clock($schedule->lunchStart).' to '.$this->clock($schedule->lunchEnd) : '',
                'grace'       => $scheduled && $schedule->graceMinutes ? $schedule->graceMinutes.' mins' : '',
                'holiday_pay' => $holiday ? ($holiday->isPaid() ? 'Paid ('.rtrim(rtrim((string) $holiday->paid_hours, '0'), '.').' hrs)' : 'Unpaid') : '',
                'category'    => $holiday?->category ?? ($proctoring->get($key)?->name ?? ''),
            ];

            // Summary cards for the range.
            if ($record) {
                $summary['present'] += match ($record->status) {
                    Attendance::PRESENT, Attendance::LATE, Attendance::UNDERTIME => 1,
                    Attendance::HALF_DAY => 0.5,
                    default => 0,
                };
                $summary['late'] += $record->status === Attendance::LATE ? 1 : 0;
                $summary['half_day'] += $record->status === Attendance::HALF_DAY ? 1 : 0;
                $summary['absent'] += $record->status === Attendance::ABSENT ? 1 : 0;
                $summary['late_minutes'] += $late;
                $summary['hours'] += $hours;
            }
        }

        $data['rows'] = $rows;
        $data['summary'] = $summary;

        if ($data['payPeriod']) {
            $data['confirmation'] = DtrConfirmation::query()
                ->where('employee_id', $employee->id)
                ->where('period', $data['payPeriod']['period'])
                ->where('cutoff', $data['payPeriod']['cutoff'])
                ->first();
        }

        return view('attendance', $data);
    }

    /**
     * The employee confirms that their DTR for a finished pay period is correct.
     * Accounting needs this before moving the payslip to Salary Monitoring.
     */
    public function confirm(Request $request)
    {
        $data = $request->validate([
            'period' => ['required', 'date_format:Y-m'],
            'cutoff' => ['required', 'in:15th,30th'],
        ]);

        $employee = $this->employeeFor($request->user());
        abort_unless($employee, 403, 'Your account is not linked to an employee record yet.');

        [$start, $end] = PayPeriod::range($data['period'], $data['cutoff']);

        if (Carbon::parse($end)->endOfDay()->isFuture()) {
            return back()->withErrors([
                'confirm' => 'You can confirm this DTR once the pay period ends on '.Carbon::parse($end)->format('F j, Y').'.',
            ]);
        }

        DtrConfirmation::updateOrCreate(
            ['employee_id' => $employee->id, 'period' => $data['period'], 'cutoff' => $data['cutoff']],
            ['confirmed_at' => now()]
        );

        return redirect()
            ->route('attendance.attendance-employee.index', ['pay' => $data['period'].'|'.$data['cutoff']])
            ->with('success', 'DTR confirmed for the '.PayPeriod::label($data['period'], $data['cutoff']).'.');
    }

    /** The pay period whose dates exactly match the chosen range, if any. */
    private function matchingPayPeriod(Carbon $from, Carbon $to): ?array
    {
        [$period, $cutoff] = PayPeriod::at($to);
        [$start, $end] = PayPeriod::range($period, $cutoff);

        if ($start !== $from->toDateString() || $end !== $to->toDateString()) {
            return null;
        }

        return [
            'period' => $period,
            'cutoff' => $cutoff,
            'key' => $period.'|'.$cutoff,
            'label' => PayPeriod::label($period, $cutoff),
            'end' => Carbon::parse($end),
            'ended' => ! Carbon::parse($end)->endOfDay()->isFuture(),
        ];
    }

    /** Find the employee record linked to this user, linking by email once if needed. */
    private function employeeFor($user): ?SchoolEmployee
    {
        $employee = SchoolEmployee::query()->where('user_id', $user->id)->first();

        if (! $employee && filled($user->email)) {
            $matches = SchoolEmployee::query()
                ->whereNull('user_id')
                ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($user->email))])
                ->limit(2)
                ->get();

            if ($matches->count() === 1) {
                $employee = $matches->first();
                $employee->user()->associate($user);
                $employee->save();
            }
        }

        return $employee;
    }

    private function clock(string $time): string
    {
        return Carbon::createFromFormat('H:i', substr($time, 0, 5))->format('h:i A');
    }

    /** 141 => "2 hrs 21 mins", 25 => "25 mins", 0 => "". */
    private function duration(int $minutes): string
    {
        if ($minutes <= 0) {
            return '';
        }

        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        $parts = [];
        if ($h) {
            $parts[] = $h.' '.($h === 1 ? 'hr' : 'hrs');
        }
        if ($m) {
            $parts[] = $m.' '.($m === 1 ? 'min' : 'mins');
        }

        return implode(' ', $parts);
    }

    private function hoursForRecord(AttendanceRecord $record, AttendanceSchedule $schedule): float
    {
        if (
            $record->status === Attendance::PAID_HOLIDAY
            && ! $record->time_in
            && ! $record->time_out
            && $record->nonWorkingDay?->isPaid()
        ) {
            return (float) $record->nonWorkingDay->paid_hours;
        }

        $date = Carbon::parse($record->date)->startOfDay();
        $timeIn = AttendanceSchedule::toCarbon($date, $record->time_in);
        $timeOut = AttendanceSchedule::toCarbon($date, $record->time_out);

        return $timeIn && $timeOut
            ? $schedule->hoursWorked($timeIn, $timeOut)
            : (float) $record->hours_worked;
    }
}