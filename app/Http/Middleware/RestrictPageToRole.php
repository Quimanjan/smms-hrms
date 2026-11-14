<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Some pages share a URL group with other roles but only have a screen for
 * one of them. Opening such a page as the wrong role used to crash with a
 * server error; this turns it into a plain "403 Forbidden".
 */
class RestrictPageToRole
{
    /** Route name => roles that actually have a screen for it. */
    private const PAGES = [
        'attendance.attendance-employee.index' => ['Employee'],
        'employee.school-employees' => ['Employee'],
        'employee-dashboard' => ['Employee'],
        'time-corrections.employee.index' => ['Employee'],
        'leaves.employee.index' => ['Employee'],
        'loans.employee.index' => ['Employee'],
        'offboarding.employee.index' => ['Employee'],
        'salary-records.employee.index' => ['Employee'],
        'payslips.index' => ['Employee'],
        'documents.employee.index' => ['Employee'],
        'loans.index' => ['Admin', 'HR'],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $allowed = self::PAGES[$request->route()?->getName() ?? ''] ?? null;

        if ($allowed !== null && ! $request->user()?->hasAnyRole($allowed)) {
            abort(403, 'This page is not available for your role.');
        }

        return $next($request);
    }
}
