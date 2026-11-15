<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\NonWorkingDay;
use App\Models\SchoolEmployee;
use App\Support\AttendanceSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AttendanceAbsenceService
{
    /** After this time (device timezone) anyone with no punch for today is marked Absent. */
    private const ABSENCE_CUTOFF = '16:30';

    public function markUnpunchedEmployeesAbsent(Carbon $now): int
    {
        // Before 4:30 PM employees can still punch in, so do nothing yet.
        if ($now->lt($now->copy()->setTimeFromTimeString(self::ABSENCE_CUTOFF))) {
            return 0;
        }

        $date = $now->toDateString();
        $cacheKey = "attendance:absences-marked:{$date}";

        // Already processed today.
        if (Cache::has($cacheKey)) {
            return 0;
        }

        $rows = [];

        if (! NonWorkingDay::whereDate('date', $date)->exists()) {
            // Employees that already have any record today (punched, absent, archived...)
            $existingEmployeeIds = Attendance::query()
                ->whereDate('date', $date)
                ->pluck('employee_id')
                ->flip();

            // Employees on approved leave today (leave_requests stores the name; user_id is often empty)
            $onLeaveNames = DB::table('leave_requests')
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->pluck('employee_name')
                ->map(fn ($name) => mb_strtolower(trim((string) $name)))
                ->flip();

            // Nobody is marked absent after their last working day.
            foreach (SchoolEmployee::excludeInternalStaff()->current($date)->get(['id', 'name', 'role_type', 'position']) as $employee) {
                $role = AttendanceSchedule::isMaintenanceEmployee($employee->role_type, $employee->position)
                    ? 'maintenance'
                    : $employee->role_type;

                if (
                    ! AttendanceSchedule::isWorkingDay($role, $now)
                    || $existingEmployeeIds->has($employee->id)
                    || $onLeaveNames->has(mb_strtolower(trim((string) $employee->name)))
                ) {
                    continue;
                }

                $rows[] = [
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'time_in' => null,
                    'time_out' => null,
                    'hours_worked' => null,
                    'status' => Attendance::ABSENT,
                    'archived' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // The (employee_id, date) unique key makes this safe to run repeatedly.
        $created = $rows ? Attendance::query()->insertOrIgnore($rows) : 0;

        Cache::put($cacheKey, true, $now->copy()->endOfDay());

        return $created;
    }
}