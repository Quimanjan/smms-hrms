<?php

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

it('shows attendance added for the selected employee in their linked employee view', function () {
    Carbon::setTestNow('2026-11-14 10:00:00');

    foreach (['HR', 'Employee'] as $name) {
        Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    $employeeUser = User::factory()->create(['email' => 'selected.employee@example.com']);
    $employeeUser->assignRole('Employee');
    $hr = User::factory()->create();
    $hr->assignRole('HR');

    $employeeId = DB::table('employees')->insertGetId([
        'employee_id' => 'SELECTED-001',
        'name' => 'Selected Employee',
        'position' => 'Teacher',
        'department' => 'elementary',
        'role_type' => 'teaching',
        'email' => $employeeUser->email,
        'birthday' => '1990-01-01',
        'address' => 'Test address',
        'emergency_name' => 'Emergency Contact',
        'emergency_contact' => '09123456789',
        'employment_status' => 'regular',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($hr)
        ->post(route('attendance.store'), [
            'employee_id' => $employeeId,
            'date' => '2026-11-13',
            'status' => Attendance::PRESENT,
            'time_in' => '07:00',
            'time_out' => '16:30',
        ])
        ->assertRedirect(route('attendance.index', ['view' => 'current']))
        ->assertSessionHas('success', 'Attendance saved.');

    $this->actingAs($employeeUser)
        ->get(route('attendance.attendance-employee.index'))
        ->assertOk()
        ->assertSee('Nov 13, 2026')
        // The DTR follows the client's sample, which calls a working day "Regular".
        ->assertSee('Regular')
        ->assertSee('07:00 AM')
        ->assertSee('8.00');

    expect(DB::table('employees')->where('id', $employeeId)->value('user_id'))
        ->toBe($employeeUser->id);
});
