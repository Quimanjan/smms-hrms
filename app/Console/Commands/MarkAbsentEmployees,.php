<?php

namespace App\Console\Commands;

use App\Services\AttendanceAbsenceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MarkAbsentEmployees extends Command
{
    protected $signature = 'attendance:mark-absent';

    protected $description = 'Mark employees who have no biometric punch as Absent once the 4:30 PM cutoff has passed.';

    public function handle(AttendanceAbsenceService $absenceService): int
    {
        // Same timezone the Attendance page uses (the ZKTeco device timezone)
        $device = (string) config('zkteco.default', 'main');
        $timezone = (string) config("zkteco.devices.{$device}.timezone", config('app.timezone', 'UTC'));

        $absenceService->markUnpunchedEmployeesAbsent(Carbon::now($timezone));

        $this->info('Absence check done.');

        return self::SUCCESS;
    }
}