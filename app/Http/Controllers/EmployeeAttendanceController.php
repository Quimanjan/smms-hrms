<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\NonWorkingDay;
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
        ]);

        $today = now()->startOfDay();
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

            $late = in_array($type, ['regular', 'proctoring'], true) && $timeIn ? (int) $schedule->lateMinutes($timeIn) : 0;
            $under = in_array($type, ['regular', 'proctoring'], true) && $timeOut ? (int) $schedule->undertimeMinutes($timeOut) : 0;
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
                'shift_in'    => $scheduled ? $this->clock($schedule->start) : '',
                'shift_out'   => $scheduled ? $this->clock($schedule->end) : '',
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

        return view('attendance', $data);
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