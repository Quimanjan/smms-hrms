<?php

namespace App\Support;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Throwable;

/**
 * Audit trail. Records who changed which record and when, plus sign-ins and
 * sign-outs. Only changes made by a signed-in person are recorded (the
 * biometric sync and scheduled jobs are not). Secret values such as passwords
 * and government ID numbers are never stored, only the fact that they changed.
 */
final class AuditTrail
{
    /** Model class => readable name. */
    public const SUBJECTS = [
        \App\Models\Employee::class => 'Employee',
        \App\Models\SchoolEmployee::class => 'Employee',
        \App\Models\User::class => 'User account',
        \App\Models\Application::class => 'Job application',
        \App\Models\JobOpening::class => 'Job opening',
        \App\Models\LeaveRequest::class => 'Leave request',
        \App\Models\TimeCorrection::class => 'Time correction',
        \App\Models\Attendance::class => 'Attendance',
        \App\Models\AttendanceRecord::class => 'Attendance',
        \App\Models\NonWorkingDay::class => 'Non-working day',
        \App\Models\ProctoringDay::class => 'Proctoring day',
        \App\Models\DtrConfirmation::class => 'DTR confirmation',
        \App\Models\Payslip::class => 'Payslip',
        \App\Models\PayrollStep::class => 'Salary step',
        \App\Models\LoanRequest::class => 'Loan request',
        \App\Models\EmployeeDocument::class => 'Employee document',
        \App\Models\OffboardingCase::class => 'Offboarding case',
        \App\Models\OffboardingDocument::class => 'Offboarding document',
        \App\Models\Banner::class => 'Banner',
    ];

    private const SECRET = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
        'sss_number', 'philhealth_number', 'pagibig_number', 'tin',
    ];

    private const IGNORE = ['updated_at', 'created_at'];

    public static function register(): void
    {
        foreach (self::SUBJECTS as $class => $label) {
            if (! class_exists($class)) {
                continue;
            }
            $class::created(fn (Model $m) => self::model('created', $m, $label));
            $class::updated(fn (Model $m) => self::model('updated', $m, $label));
            $class::deleted(fn (Model $m) => self::model('deleted', $m, $label));
        }

        Event::listen(Login::class, fn (Login $e) => self::write('signed in', $e->user, null, null, 'Signed in'));
        Event::listen(Logout::class, fn (Logout $e) => $e->user ? self::write('signed out', $e->user, null, null, 'Signed out') : null);
    }

    private static function model(string $action, Model $model, string $label): void
    {
        $user = Auth::user();
        if (! $user) {
            return; // system jobs (biometric sync, schedules) are not user activity
        }

        $changes = null;
        if ($action === 'updated') {
            $changes = [];
            foreach ($model->getChanges() as $field => $new) {
                if (in_array($field, self::IGNORE, true)) {
                    continue;
                }
                $changes[$field] = in_array($field, self::SECRET, true)
                    ? ['from' => '(hidden)', 'to' => '(hidden)']
                    : ['from' => self::short($model->getOriginal($field)), 'to' => self::short($new)];
            }
            if ($changes === []) {
                return;
            }
        }

        $name = $model->getAttribute('name') ?? $model->getAttribute('employee_name') ?? $model->getAttribute('title') ?? null;
        if (! $name && $model->getAttribute('employee_id') && method_exists($model, 'employee')) {
            try {
                $name = $model->employee?->name;
            } catch (Throwable) {
                $name = null;
            }
        }
        $description = ucfirst($action).' '.strtolower($label).($name ? ': '.$name : ' #'.$model->getKey());

        self::write($action, $user, $label, $model->getKey(), $description, $changes);
    }

    private static function short($value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i');
        }
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        $value = (string) $value;

        return mb_strlen($value) > 120 ? mb_substr($value, 0, 117).'...' : $value;
    }

    private static function write(string $action, $user, ?string $type, $id, string $description, ?array $changes = null): void
    {
        try {
            DB::table('activity_logs')->insert([
                'user_id' => $user?->getKey(),
                'user_name' => $user?->name,
                'role' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : null,
                'action' => $action,
                'subject_type' => $type,
                'subject_id' => $id,
                'description' => mb_substr($description, 0, 255),
                'changes' => $changes ? json_encode($changes) : null,
                'ip' => request()?->ip(),
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // The audit trail must never stop the real action.
        }
    }
}
