<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('adding an existing employee creates an employee login with the default password', function () {
    foreach (['HR', 'Employee'] as $r) {
        Role::findOrCreate($r);
    }
    DB::table('payroll_steps')->insertOrIgnore(['step_no' => 1, 'name' => 'Step 1', 'daily_wage' => 550]);
    $hr = User::factory()->create();
    $hr->assignRole('HR');

    $response = $this->actingAs($hr)->post(route('employees.store'), [
        'employee_source' => 'existing',
        'first_name' => 'Paul Angelo', 'middle_name' => 'Cruz', 'last_name' => 'Reyes',
        'position' => 'Teacher I', 'department' => 'Elementary', 'role_type' => 'Teaching Staff',
        'employment_status' => 'regular', 'step_no' => 1,
        'email' => 'paul.reyes@example.com',
        'date_hired' => '2020-06-01', 'birthday' => '1990-01-01',
        'civil_status' => 'Single', 'gender' => 'Male',
        'address_line' => '12 Rizal St', 'barangay' => 'Poblacion', 'city_municipality' => 'Kawit',
        'province' => 'Cavite', 'zip_code' => '4104',
        'emergency_name' => 'Maria Reyes', 'emergency_contact' => '09171234567',
    ]);

    $response->assertSessionHasNoErrors();
    $user = User::where('email', 'paul.reyes@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasRole('Employee'))->toBeTrue()
        ->and(Hash::check('password123', $user->password))->toBeTrue()
        ->and($user->first_name)->toBe('Paul Angelo')
        ->and($user->employee?->email)->toBe('paul.reyes@example.com');
});
