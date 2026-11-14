<?php

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeEmployeeWithId(string $id): Employee
{
    return Employee::forceCreate([
        'employee_id' => $id,
        'zkteco_user_id' => $id,
        'first_name' => 'Test',
        'last_name' => 'Person '.$id,
        'name' => 'Test Person '.$id,
        'department' => 'Elementary',
        'role_type' => 'Teaching Staff',
        'email' => "emp{$id}@example.com",
        'birthday' => '1990-01-01',
        'address' => 'Bacoor, Cavite',
        'emergency_name' => 'Contact',
        'emergency_contact' => '09170000000',
        'employment_status' => 'regular',
    ]);
}

test('the first generated employee id of a year is the year plus 0001', function () {
    expect(Employee::generateEmployeeId(2026))->toBe('20260001');
});

test('generated employee ids continue from the highest one used that year', function () {
    makeEmployeeWithId('20260001');
    makeEmployeeWithId('20260007');
    makeEmployeeWithId('1006');      // legacy manual id, ignored
    makeEmployeeWithId('20250044');  // another year, ignored

    expect(Employee::generateEmployeeId(2026))->toBe('20260008');
    expect(Employee::generateEmployeeId(2025))->toBe('20250045');
});
