<?php

use App\Models\Attendance;
use App\Support\AttendanceSchedule;
use Illuminate\Support\Carbon;

it('allows Saturday attendance only for maintenance staff role types', function () {
    $saturday = Carbon::parse('2026-03-07');
    $sunday = Carbon::parse('2026-03-08');

    expect(AttendanceSchedule::isWorkingDay('Maintenance Staff', $saturday))->toBeTrue()
        ->and(AttendanceSchedule::isWorkingDay('maintenance', $saturday))->toBeTrue()
        ->and(AttendanceSchedule::isWorkingDay('Teaching Staff', $saturday))->toBeFalse()
        ->and(AttendanceSchedule::isWorkingDay('Administrative Staff', $saturday))->toBeFalse()
        ->and(AttendanceSchedule::isWorkingDay('Support Staff', $saturday))->toBeFalse()
        ->and(AttendanceSchedule::isWorkingDay('Maintenance Staff', $sunday))->toBeFalse()
        ->and(AttendanceSchedule::isWorkingDay('Teaching Staff', Carbon::parse('2026-03-06')))->toBeTrue();
});

it('uses the 6 AM to 3 PM schedule for maintenance staff in every month', function () {
    foreach (['2026-03-02', '2026-05-04'] as $dateString) {
        $date = Carbon::parse($dateString);
        $schedule = AttendanceSchedule::for('Maintenance Staff', $date);
        $timeIn = AttendanceSchedule::toCarbon($date, '06:00:00');
        $timeOut = AttendanceSchedule::toCarbon($date, '15:00:00');

        expect($schedule->label())->toBe('6:00 AM - 3:00 PM')
            ->and($schedule->hoursWorked($timeIn, $timeOut))->toBe(8.0)
            ->and($schedule->lateMinutes($timeIn))->toBe(0);
    }
});

it('calculates required hours from the employee shift schedule', function () {
    $regularDate = Carbon::parse('2026-03-02');
    $adjustedDate = Carbon::parse('2026-05-04');
    $maintenanceDate = Carbon::parse('2026-03-07');

    expect(AttendanceSchedule::for('teacher', $regularDate)->requiredHours($regularDate))->toBe(8.0)
        ->and(AttendanceSchedule::for('teacher', $adjustedDate)->requiredHours($adjustedDate))->toBe(6.5)
        ->and(AttendanceSchedule::for('maintenance', $maintenanceDate)->requiredHours($maintenanceDate))->toBe(8.0);
});

it('labels attendance shift types for work, leave, and non-working days', function () {
    expect(Attendance::shiftTypeForStatus(Attendance::PRESENT))->toBe('Working Day')
        ->and(Attendance::shiftTypeForStatus(Attendance::PAID_LEAVE))->toBe('Paid Leave')
        ->and(Attendance::shiftTypeForStatus(Attendance::UNPAID_LEAVE))->toBe('Unpaid Leave')
        ->and(Attendance::shiftTypeForStatus(Attendance::PAID_HOLIDAY))->toBe('Paid Holiday')
        ->and(Attendance::shiftTypeForStatus(Attendance::NWD_REVIEW))->toBe('Non-Working Day Review');
});

it('recognizes maintenance staff from the employee position when the role type differs', function () {
    $date = Carbon::parse('2026-11-05');
    $schedule = AttendanceSchedule::forEmployee('nonteaching', 'Maintenance Staff', $date);
    $timeIn = AttendanceSchedule::toCarbon($date, '07:00:00');
    $timeOut = AttendanceSchedule::toCarbon($date, '15:00:00');

    expect($schedule->label())->toBe('6:00 AM - 3:00 PM')
        ->and($schedule->hoursWorked($timeIn, $timeOut))->toBe(7.0)
        ->and($schedule->status($timeIn, $timeOut))->toBe('Late');
});

it('credits full scheduled hours when an employee clocks in within the ten-minute grace period', function () {
    $date = Carbon::parse('2026-03-02');
    $schedule = AttendanceSchedule::for('teacher', $date);

    $timeIn = AttendanceSchedule::toCarbon($date, '07:10:00');
    $timeOut = AttendanceSchedule::toCarbon($date, '16:30:00');

    expect($schedule->lateMinutes($timeIn))->toBe(0)
        ->and($schedule->hoursWorked($timeIn, $timeOut))->toBe(8.0)
        ->and($schedule->status($timeIn, $timeOut))->toBe('Present');
});

it('counts lateness and reduces worked hours when an employee exceeds the grace period', function () {
    $date = Carbon::parse('2026-03-02');
    $schedule = AttendanceSchedule::for('teacher', $date);

    $timeIn = AttendanceSchedule::toCarbon($date, '07:11:00');
    $timeOut = AttendanceSchedule::toCarbon($date, '16:30:00');

    expect($schedule->lateMinutes($timeIn))->toBe(11)
        ->and($schedule->hoursWorked($timeIn, $timeOut))->toBe(7.82)
        ->and($schedule->status($timeIn, $timeOut))->toBe('Late');
});

it('does not count overtime after the scheduled end time', function () {
    $date = Carbon::parse('2026-03-02');
    $schedule = AttendanceSchedule::for('teacher', $date);
    $timeIn = AttendanceSchedule::toCarbon($date, '07:00:00');
    $scheduledEnd = AttendanceSchedule::toCarbon($date, '16:30:00');
    $overtimeEnd = AttendanceSchedule::toCarbon($date, '18:00:00');

    expect($schedule->hoursWorked($timeIn, $scheduledEnd))->toBe(8.0)
        ->and($schedule->hoursWorked($timeIn, $overtimeEnd))->toBe(8.0);
});

it('marks clock-outs from 2:00 PM through 4:29 PM as undertime', function () {
    $date = Carbon::parse('2026-03-02');
    $schedule = AttendanceSchedule::for('teacher', $date);
    $timeIn = AttendanceSchedule::toCarbon($date, '07:00:00');

    expect($schedule->status($timeIn, AttendanceSchedule::toCarbon($date, '13:59:00')))
        ->toBe('Present')
        ->and($schedule->status($timeIn, AttendanceSchedule::toCarbon($date, '14:00:00')))
        ->toBe('Undertime')
        ->and($schedule->status($timeIn, AttendanceSchedule::toCarbon($date, '16:29:00')))
        ->toBe('Undertime')
        ->and($schedule->status($timeIn, AttendanceSchedule::toCarbon($date, '16:30:00')))
        ->toBe('Present');
});

it('counts all minutes between an early clock-out and the scheduled end', function () {
    $date = Carbon::parse('2026-03-02');
    $schedule = AttendanceSchedule::for('teacher', $date);

    expect($schedule->undertimeMinutes(AttendanceSchedule::toCarbon($date, '13:22:00')))
        ->toBe(188)
        ->and($schedule->undertimeMinutes(AttendanceSchedule::toCarbon($date, '12:00:00')))
        ->toBe(270)
        ->and($schedule->undertimeMinutes(AttendanceSchedule::toCarbon($date, '16:30:00')))
        ->toBe(0);

    $adjustedDate = Carbon::parse('2026-05-04');
    $adjustedSchedule = AttendanceSchedule::for('teacher', $adjustedDate);

    expect($adjustedSchedule->undertimeMinutes(AttendanceSchedule::toCarbon($adjustedDate, '15:30:00')))
        ->toBe(30);
});

it('marks clock-outs from noon through 1 PM as half day', function () {
    $date = Carbon::parse('2026-03-02');
    $schedule = AttendanceSchedule::for('teacher', $date);
    $timeIn = AttendanceSchedule::toCarbon($date, '07:00:00');

    expect($schedule->status($timeIn, AttendanceSchedule::toCarbon($date, '12:00:00')))
        ->toBe('Half Day')
        ->and($schedule->status($timeIn, AttendanceSchedule::toCarbon($date, '12:30:00')))
        ->toBe('Half Day')
        ->and($schedule->status($timeIn, AttendanceSchedule::toCarbon($date, '13:00:00')))
        ->toBe('Half Day')
        ->and($schedule->status($timeIn, AttendanceSchedule::toCarbon($date, '13:01:00')))
        ->toBe('Present');
});
