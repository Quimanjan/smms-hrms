<?php

namespace App\Services;

use App\Support\AttendanceSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceRecordService
{
    public const MIN_GAP_SECONDS = 60;      // ignore double-scans within this gap

    /**
     * Build/refresh the daily attendance_records row for one biometric user and date.
     * Returns 'created', 'updated' or 'unmatched'.
     */
    public function upsert(string $zkUserId, string $date): string
    {
        $employee = DB::table('employees')
            ->where('zkteco_user_id', $zkUserId)
            ->first(['id', 'role_type', 'position']);

        if (!$employee) {
            return 'unmatched';
        }

        $bounds = DB::table('zkteco_attendances')
            ->where('user_id', $zkUserId)
            ->whereDate('punched_at', $date)
            ->selectRaw('MIN(punched_at) as first_punch, MAX(punched_at) as last_punch')
            ->first();

        if (!$bounds || !$bounds->first_punch) {
            return 'unmatched';
        }

        $first = Carbon::parse($bounds->first_punch);
        $last  = Carbon::parse($bounds->last_punch);

        $existing = DB::table('attendance_records')
            ->where('employee_id', $employee->id)
            ->where('date', $date)
            ->first();

        $timeIn  = $first->format('H:i:s');
        $timeOut = abs($last->getTimestamp() - $first->getTimestamp()) >= self::MIN_GAP_SECONDS
            ? $last->format('H:i:s')
            : null;

        // Merge with an existing row so we never shrink a manual/approved time.
        if ($existing) {
            if ($existing->time_in && $existing->time_in < $timeIn) {
                $timeIn = $existing->time_in;
            }
            if ($existing->time_out && ($timeOut === null || $existing->time_out > $timeOut)) {
                $timeOut = $existing->time_out;
            }
        }

        $dateStart = Carbon::parse($date)->startOfDay();
        $schedule = AttendanceSchedule::forEmployee($employee->role_type, $employee->position, $dateStart);
        $timeInCarbon = AttendanceSchedule::toCarbon($dateStart, $timeIn);
        $timeOutCarbon = AttendanceSchedule::toCarbon($dateStart, $timeOut);
        $hours = $timeInCarbon && $timeOutCarbon
            ? $schedule->hoursWorked($timeInCarbon, $timeOutCarbon)
            : null;

        $data = [
            'time_in'      => $timeIn,
            'time_out'     => $timeOut,
            'hours_worked' => $hours,
            'status'       => $schedule->status($timeInCarbon, $timeOutCarbon),
            'updated_at'   => now(),
        ];

        if ($existing) {
            DB::table('attendance_records')->where('id', $existing->id)->update($data);

            return 'updated';
        }

        DB::table('attendance_records')->insert($data + [
            'employee_id' => $employee->id,
            'date'        => $date,
            'archived'    => 0,
            'created_at'  => now(),
        ]);

        return 'created';
    }
}