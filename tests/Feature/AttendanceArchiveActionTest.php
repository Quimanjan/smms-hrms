<?php

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

function createAttendanceArchiveTestEmployee(string $suffix): int
{
    return DB::table('employees')->insertGetId([
        'employee_id' => 'ARCHIVE-'.$suffix,
        'name' => 'Archive Test '.$suffix,
        'position' => 'Teacher',
        'department' => 'elementary',
        'role_type' => 'teaching',
        'email' => 'archive-'.$suffix.'@example.com',
        'birthday' => '1990-01-01',
        'address' => 'Test address',
        'emergency_name' => 'Emergency Contact',
        'emergency_contact' => '09123456789',
        'employment_status' => 'regular',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

it('archives selected attendance into the archive view without changing delete behavior', function () {
    Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
    $hr = User::factory()->create();
    $hr->assignRole('HR');

    $archivedEmployeeId = createAttendanceArchiveTestEmployee('move');
    $deletedEmployeeId = createAttendanceArchiveTestEmployee('delete');

    $attendanceToArchive = Attendance::create([
        'employee_id' => $archivedEmployeeId,
        'date' => '2026-11-05',
        'status' => Attendance::PRESENT,
        'archived' => false,
    ]);
    $attendanceToDelete = Attendance::create([
        'employee_id' => $deletedEmployeeId,
        'date' => '2026-11-05',
        'status' => Attendance::PRESENT,
        'archived' => false,
    ]);

    $this->actingAs($hr)
        ->patch(route('attendance.archive'), ['ids' => [$attendanceToArchive->id]])
        ->assertRedirect(route('attendance.index', ['view' => 'archive']))
        ->assertSessionHas('success', '1 record(s) archived.');

    expect($attendanceToArchive->fresh()->archived)->toBeTrue();

    $this->actingAs($hr)
        ->delete(route('attendance.destroyMany'), ['ids' => [$attendanceToDelete->id]]);

    expect(Attendance::whereKey($attendanceToDelete->id)->exists())->toBeFalse()
        ->and(Attendance::whereKey($attendanceToArchive->id)->exists())->toBeTrue();
});

it('replaces an auto-generated absence when HR adds attendance and shows the entry in the list', function () {
    Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
    $hr = User::factory()->create();
    $hr->assignRole('HR');

    $employeeId = createAttendanceArchiveTestEmployee('manual');
    $date = now()->subDay()->startOfDay();
    while (! \App\Support\AttendanceSchedule::isWorkingDay('teaching', $date)) {
        $date->subDay();
    }

    $attendance = Attendance::create([
        'employee_id' => $employeeId,
        'date' => $date->toDateString(),
        'status' => Attendance::ABSENT,
        'archived' => false,
    ]);

    $this->actingAs($hr)
        ->post(route('attendance.store'), [
            'employee_id' => $employeeId,
            'date' => $date->toDateString(),
            'status' => Attendance::PRESENT,
            'time_in' => '07:00',
            'time_out' => '16:30',
        ])
        ->assertRedirect(route('attendance.index', ['view' => 'current']))
        ->assertSessionHas('success', 'Attendance saved.');

    $this->actingAs($hr)
        ->get(route('attendance.index', ['view' => 'current']))
        ->assertOk()
        ->assertSee('Archive Test manual')
        ->assertSee($date->format('M d, Y'))
        ->assertSee('8.00');

    expect(Attendance::where('employee_id', $employeeId)->whereDate('date', $date)->count())
        ->toBe(1)
        ->and($attendance->fresh()->status)->toBe(Attendance::PRESENT)
        ->and((float) $attendance->fresh()->hours_worked)->toBe(8.0)
        ->and($attendance->fresh()->archived)->toBeFalse();
});

it('updates an existing employee attendance row when HR submits the same date', function () {
    Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
    $hr = User::factory()->create();
    $hr->assignRole('HR');

    $employeeId = createAttendanceArchiveTestEmployee('update');
    $date = now()->subDay()->startOfDay();
    while (! \App\Support\AttendanceSchedule::isWorkingDay('teaching', $date)) {
        $date->subDay();
    }

    $attendance = Attendance::create([
        'employee_id' => $employeeId,
        'date' => $date->toDateString(),
        'time_in' => '09:00',
        'time_out' => '13:00',
        'hours_worked' => 3,
        'status' => Attendance::UNDERTIME,
        'archived' => false,
    ]);

    $this->actingAs($hr)
        ->post(route('attendance.store'), [
            'employee_id' => $employeeId,
            'date' => $date->toDateString(),
            'status' => Attendance::PRESENT,
            'time_in' => '07:00',
            'time_out' => '16:30',
        ])
        ->assertRedirect(route('attendance.index', ['view' => 'current']))
        ->assertSessionHas('success', 'Attendance record updated.');

    $this->actingAs($hr)
        ->get(route('attendance.index', ['view' => 'current']))
        ->assertOk()
        ->assertSee('Archive Test update')
        ->assertSee('07:00');

    expect(Attendance::where('employee_id', $employeeId)->whereDate('date', $date)->count())
        ->toBe(1)
        ->and($attendance->fresh()->status)->toBe(Attendance::PRESENT)
        ->and((float) $attendance->fresh()->hours_worked)->toBe(8.0);
});
