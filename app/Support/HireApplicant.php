<?php

namespace App\Support;

use App\Models\Application;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Turns a hired applicant (approved by HR and then Admin) into an employee:
 * the account becomes an Employee login and an employee profile is created from
 * the application, so HR no longer has to add them by hand.
 * HR can still fix anything in Employee Management afterwards.
 */
final class HireApplicant
{
    /** Creates the employee profile once. Returns null when it already exists. */
    public static function fromApplication(Application $application): ?Employee
    {
        return DB::transaction(function () use ($application) {
            $user = $application->user_id ? User::find($application->user_id) : null;

            if ($user && Employee::where('user_id', $user->id)->exists()) {
                return null;
            }

            if ($user) {
                $user->syncRoles(['Employee']);
            }

            [$first, $middle, $last, $suffix] = self::nameParts($application);
            [$roleType, $department] = self::placement((string) $application->position);

            // Same locking as the Add Employee form, so two saves never share an ID.
            Employee::query()->lockForUpdate()->get(['id']);
            $employeeId = Employee::generateEmployeeId();

            return Employee::create([
                'user_id' => $user?->id,
                'employee_id' => $employeeId,
                'zkteco_user_id' => $employeeId,
                'first_name' => $first,
                'middle_name' => $middle,
                'last_name' => $last,
                'suffix' => $suffix,
                'position' => (string) $application->position,
                'department' => $department,
                'role_type' => $roleType,
                'employment_status' => 'non regular', // new hires start as Probationary
                'step_no' => (int) (DB::table('payroll_steps')->min('step_no') ?? 1),
                'email' => $user?->email ?? $application->email,
                'phone' => $application->phone,
                'birthday' => $application->birth_date,
                'gender' => $application->gender,
                'civil_status' => $application->civil_status,
                'address' => (string) $application->home_address,
                'education' => self::education($application),
                'emergency_name' => '',
                'emergency_contact' => '',
                'date_hired' => now('Asia/Manila')->toDateString(), // the school's own date
            ]);
        });
    }

    /** First, middle, last and suffix from the application (older ones only have the full name). */
    private static function nameParts(Application $a): array
    {
        if (filled($a->first_name) && filled($a->last_name)) {
            return [trim($a->first_name), self::blank($a->middle_name), trim($a->last_name), self::blank($a->suffix)];
        }

        $words = preg_split('/\s+/', trim((string) $a->fullname), -1, PREG_SPLIT_NO_EMPTY) ?: ['Applicant'];
        $last = count($words) > 1 ? array_pop($words) : '';

        return [implode(' ', $words), null, $last, null];
    }

    /**
     * Role type and department worked out from the position title, e.g.
     * "High School Teacher" -> Teaching Staff, High School.
     */
    public static function placement(string $position): array
    {
        $p = Str::lower($position);

        if (Str::contains($p, ['teacher', 'faculty', 'instructor', 'adviser', 'coach'])) {
            $department = match (true) {
                Str::contains($p, 'senior high') => 'Senior High School',
                Str::contains($p, ['high school', 'junior high']) => 'High School',
                default => 'Elementary',
            };

            return ['Teaching Staff', $department];
        }

        if (Str::contains($p, ['maintenance', 'utility', 'janitor', 'custodian', 'guard', 'driver', 'gardener', 'electrician', 'carpenter'])) {
            return ['Maintenance Staff', 'Maintenance'];
        }

        return match (true) {
            Str::contains($p, ['accounting', 'accountant', 'cashier', 'bookkeeper', 'finance']) => ['Administrative Staff', 'Accounting'],
            Str::contains($p, ['hr', 'human resource']) => ['Administrative Staff', 'HR'],
            Str::contains($p, ['admin', 'registrar', 'secretary', 'clerk', 'principal', 'librarian']) => ['Administrative Staff', 'Administrator'],
            default => ['Support Staff', 'Administrator'],
        };
    }

    /** Highest education from the application, in the employee profile format. */
    public static function education(?Application $app): ?array
    {
        if (! $app || blank($app->education_school ?? null)) {
            return null;
        }
        $level = match ($app->education) {
            'Elementary' => 'primary',
            'High School' => 'junior_high',
            'Senior High School' => 'senior_high',
            default => 'tertiary',
        };
        $entry = ['school' => $app->education_school, 'years' => (string) ($app->education_year ?? '')];
        if ($level === 'tertiary') {
            $entry['course'] = $app->education;
        }

        return [$level => $entry];
    }

    private static function blank($v): ?string
    {
        return filled($v) ? trim((string) $v) : null;
    }
}
