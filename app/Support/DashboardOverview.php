<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Figures and shortcuts for the top of the staff dashboards.
 *
 * Every figure is read straight from the database when the page loads, and
 * each one is computed on its own: if one query fails (for example a column
 * that a database has not been migrated to yet), that card is left out and
 * the rest of the dashboard still loads.
 */
class DashboardOverview
{
    public static function for(User $user): array
    {
        $role = $user->getRoleNames()->first();
        $now = now('Asia/Manila');

        $greeting = match (true) {
            $now->hour < 12 => 'Good morning',
            $now->hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        $cards = array_values(array_filter(match ($role) {
            'Admin' => [
                self::card('Active employees', 'fa-users', 'employees.index',
                    fn () => self::currentEmployees()->count(),
                    'On the school\'s roster'),
                self::card('Leave requests', 'fa-calendar-days', 'leaves.index',
                    fn () => self::pending('leave_requests', 'admin_status', ['hr_status' => 'approved']),
                    'Approved by HR, waiting for you'),
                self::card('Payslips to approve', 'fa-file-invoice-dollar', 'payroll.index',
                    fn () => self::pending('payslips', 'admin_status'),
                    'Prepared by Accounting'),
                self::card('Applicants', 'fa-user-plus', 'onboarding.index',
                    fn () => self::openApplications(),
                    'Waiting for a decision'),
            ],
            'HR' => [
                self::card('Active employees', 'fa-users', 'employees.index',
                    fn () => self::currentEmployees()->count(),
                    'On the school\'s roster'),
                self::card('Leave requests', 'fa-calendar-days', 'leaves.index',
                    fn () => self::pending('leave_requests', 'hr_status'),
                    'Waiting for your review'),
                self::card('Applicants', 'fa-user-plus', 'onboarding.index',
                    fn () => self::openApplications(),
                    'Waiting for a decision'),
                self::card('Time corrections', 'fa-clock-rotate-left', 'time-corrections.index',
                    fn () => self::pending('time_corrections', 'hr_status'),
                    'Waiting for your review'),
            ],
            'Accounting' => [
                self::card('Employees on payroll', 'fa-users', 'payroll-accounting.index',
                    fn () => self::currentEmployees()->count(),
                    'Paid each cutoff'),
                self::card('Payslips in review', 'fa-file-invoice-dollar', 'salary-monitoring.index',
                    fn () => self::pending('payslips', 'admin_status'),
                    'Waiting for HR or Admin'),
                self::card('Loan requests', 'fa-hand-holding-dollar', 'loans-accounting.index',
                    fn () => self::pending('loan_requests', 'accounting_status', ['hr_status' => 'approved']),
                    'Approved by HR, waiting for you'),
                self::card('Approved payslips', 'fa-circle-check', 'salary-records.index',
                    fn () => DB::table('payslips')->whereRaw('LOWER(admin_status) = ?', ['approved'])->count(),
                    'Released to employees'),
            ],
            default => [],
        }));

        $actions = array_values(array_filter(match ($role) {
            'Admin' => [
                self::action('Review leave requests', 'leaves.index'),
                self::action('Open payroll', 'payroll.index'),
                self::action('View reports', 'reports.index'),
            ],
            'HR' => [
                self::action('Manage employees', 'employees.index'),
                self::action('Review applicants', 'onboarding.index'),
                self::action('Check attendance', 'attendance.index'),
            ],
            'Accounting' => [
                self::action('Process payroll', 'payroll-accounting.index'),
                self::action('Salary records', 'salary-records.index'),
                self::action('View reports', 'reports.index'),
            ],
            default => [],
        }));

        return [
            'greeting' => $greeting.', '.$user->name,
            'role' => $role,
            'date' => $now->format('l, F j, Y'),
            'cards' => $cards,
            'actions' => $actions,
        ];
    }

    /** Employees still with the school (archived after their last working day). */
    private static function currentEmployees()
    {
        return DB::table('employees')->where(fn ($q) => $q
            ->whereNull('separated_on'));
    }

    /** People and workforce mix for the dashboard panels. */
    public static function team(): array
    {
        try {
            $people = self::currentEmployees()
                ->select('id', 'name', 'gender', 'position', 'role_type', 'department')
                ->orderBy('name')
                ->get();
        } catch (Throwable) {
            return ['people' => collect(), 'groups' => [], 'genders' => [], 'total' => 0];
        }

        $total = $people->count();

        $groups = $people
            ->groupBy(fn ($p) => trim((string) $p->role_type) ?: 'Not set')
            ->map(fn ($rows, $label) => ['label' => $label, 'count' => $rows->count()])
            ->sortByDesc('count')
            ->values()
            ->all();

        $genders = $people
            ->groupBy(fn ($p) => Avatar::gender($p->gender))
            ->map(fn ($rows, $g) => [
                'label' => ['female' => 'Women', 'male' => 'Men', 'neutral' => 'Not set'][$g],
                'key' => $g,
                'count' => $rows->count(),
            ])
            ->sortBy(fn ($row) => ['female' => 0, 'male' => 1, 'neutral' => 2][$row['key']])
            ->values()
            ->all();

        return ['people' => $people, 'groups' => $groups, 'genders' => $genders, 'total' => $total];
    }

    private static function card(string $label, string $icon, string $route, callable $count, string $note): ?array
    {
        try {
            $value = (int) $count();
        } catch (Throwable) {
            return null;
        }

        return [
            'label' => $label,
            'icon' => $icon,
            'value' => $value,
            'note' => $note,
            'url' => Route::has($route) ? route($route) : null,
        ];
    }

    private static function action(string $label, string $route): ?array
    {
        return Route::has($route) ? ['label' => $label, 'url' => route($route)] : null;
    }

    /** Rows whose $column is pending, optionally only where other steps are already done. */
    private static function pending(string $table, string $column, array $after = []): int
    {
        if (! Schema::hasColumn($table, $column)) {
            return 0;
        }

        $query = DB::table($table)->whereRaw("LOWER($column) = ?", ['pending']);

        foreach ($after as $otherColumn => $value) {
            $query->whereRaw("LOWER($otherColumn) = ?", [strtolower($value)]);
        }

        foreach (['archived_at', 'admin_archived'] as $archive) {
            if (Schema::hasColumn($table, $archive)) {
                $archive === 'archived_at' ? $query->whereNull($archive) : $query->where(fn ($q) => $q->whereNull($archive)->orWhere($archive, false));
            }
        }

        return $query->count();
    }

    private static function openApplications(): int
    {
        $query = DB::table('applications')->whereIn(DB::raw('LOWER(status)'), ['pending', 'scheduled', 'hr_approved']);

        if (Schema::hasColumn('applications', 'archived_at')) {
            $query->whereNull('archived_at');
        }

        return $query->count();
    }
}
