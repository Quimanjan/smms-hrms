<?php
use Illuminate\Support\Facades\Schedule;

if (! config('zkteco.push.enabled')) {
    Schedule::command('zkteco:sync')
        ->everyMinute()
        ->withoutOverlapping();
}

// Runs all day; the 4:30 PM cutoff is enforced inside AttendanceAbsenceService.
// Include Saturdays too because maintenance staff work Monday to Saturday.
Schedule::command('attendance:mark-absent')
    ->everyFiveMinutes()
    ->withoutOverlapping();
