<?php

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\SubmissionBox;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['Employee', 'HR'] as $name) {
        Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }
});

function createDocumentNotificationEmployee(User $user): Employee
{
    return Employee::create([
        'user_id' => $user->id,
        'employee_id' => 'DOCUMENT-'.$user->id,
        'name' => $user->name,
        'position' => 'Teacher',
        'department' => 'elementary',
        'role_type' => 'teaching',
        'email' => 'document-'.$user->id.'@example.com',
        'birthday' => '1990-01-01',
        'address' => 'Test address',
        'emergency_name' => 'Emergency Contact',
        'emergency_contact' => '09123456789',
    ]);
}

// HR no longer creates submission boxes: the client asked for a fixed list of
// onboarding requirements, so the old box-creation test was removed.

test('document upload notifies HR with employee, file, and submission date', function () {
    Storage::fake('local');
    $hr = User::factory()->create();
    $hr->assignRole('HR');
    $employeeUser = User::factory()->create();
    $employeeUser->assignRole('Employee');
    $employee = createDocumentNotificationEmployee($employeeUser);
    // Uploads now go to the school's fixed onboarding requirements.
    $box = SubmissionBox::create([
        'name' => 'NBI Clearance',
        'type' => 'NBI',
        'is_requirement' => true,
        'sort_order' => 99,
    ]);

    $this->actingAs($employeeUser)
        ->post(route('employee.documents.store'), [
            'submission_box_id' => $box->id,
            'file' => UploadedFile::fake()->create('clearance.pdf', 12, 'application/pdf'),
        ])
        ->assertSessionHasNoErrors();

    $message = $hr->notifications()->first()->data['message'];

    expect($hr->notifications()->count())->toBe(1)
        ->and($message)->toContain($employee->name)
        ->and($message)->toContain('clearance.pdf')
        ->and($message)->toContain('NBI Clearance')
        ->and($message)->toContain(now()->format('M d, Y'))
        ->and($hr->notifications()->first()->data['from'])->toBe($employeeUser->name);

    $document = EmployeeDocument::firstOrFail();
    $this->actingAs($hr)
        ->patch(route('hr.documents.decide'), [
            'document_id' => $document->id,
            'decision' => EmployeeDocument::APPROVED,
        ])
        ->assertSessionHasNoErrors();

    expect($employeeUser->notifications()->count())->toBe(1)
        ->and($employeeUser->notifications()->first()->data['title'])->toBe('Submitted document approved')
        ->and($employeeUser->notifications()->first()->data['message'])->toContain('DOC-'.$document->id)
        ->and($employeeUser->notifications()->first()->data['from'])->toBe($hr->name);
});
