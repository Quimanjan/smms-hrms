<?php

namespace App\Support;

use App\Models\OffboardingCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Final pay amounts when an employee leaves.
 *
 * Separation pay follows the Labor Code of the Philippines (renumbered, DOLE
 * Department Advisory No. 01, s. 2015):
 *  - Art. 298 (formerly 283), installation of labor-saving devices or redundancy:
 *    one month pay, or one month pay per year of service, whichever is higher.
 *  - Art. 298 (formerly 283), retrenchment to prevent losses, or closure not due to
 *    serious losses: one month pay, or one-half month pay per year of service,
 *    whichever is higher.
 *  - Art. 299 (formerly 284), disease: one month pay, or one-half month pay per year
 *    of service, whichever is higher.
 *  - In all three, a fraction of at least six months counts as one whole year.
 *  - Voluntary resignation (Art. 300) and dismissal for a just cause (Art. 297)
 *    carry no separation pay under the Labor Code.
 *
 * Retirement pay follows Republic Act No. 7641 (Labor Code Art. 302, formerly 287):
 * at age 60 or older with at least five years of service, at least one-half month
 * salary per year of service, where one-half month salary is 22.5 days
 * (15 days + 1/12 of the 13th month pay + 5 days of service incentive leave).
 *
 * Gratuity pay is, by the school's own rule, the total PERAA/CEAP deducted on the
 * employee's released payslips.
 */
final class FinalPay
{
    public const CAUSES = [
        'resignation' => 'Voluntary resignation',
        'redundancy' => 'Redundancy or labor-saving devices',
        'retrenchment' => 'Retrenchment or closure (not due to serious losses)',
        'disease' => 'Disease',
        'retirement' => 'Retirement',
        'just_cause' => 'Dismissal for a just cause',
    ];

    public static function for(OffboardingCase $case): array
    {
        $employee = $case->employee;
        $end = Carbon::parse($case->last_working_day)->startOfDay();
        $hiredOn = $employee?->date_hired ? Carbon::parse($employee->date_hired) : ($employee?->created_at ? Carbon::parse($employee->created_at) : null);
        $hiredIsEstimate = ! $employee?->date_hired;

        $months = $hiredOn ? max(0, (int) floor($hiredOn->floatDiffInMonths($end))) : 0;
        $fullYears = intdiv($months, 12);
        $extraMonths = $months % 12;
        // A fraction of at least six months counts as one whole year.
        $creditedYears = $fullYears + ($extraMonths >= 6 ? 1 : 0);

        $dailyWage = (float) (DB::table('payroll_steps')->where('step_no', $employee?->step_no ?: 1)->value('daily_wage') ?? 0);
        $monthlyPay = round($dailyWage * PayrollCalculator::MONTHLY_DAYS, 2);
        $age = $employee?->birthday ? (int) floor(abs(Carbon::parse($employee->birthday)->diffInYears($end))) : null;

        $cause = array_key_exists($case->separation_cause, self::CAUSES) ? $case->separation_cause : 'resignation';

        [$separation, $separationRule] = match ($cause) {
            'redundancy' => [max($monthlyPay, $monthlyPay * $creditedYears), 'Labor Code Art. 298: one month pay per year of service, at least one month.'],
            'retrenchment' => [max($monthlyPay, $monthlyPay / 2 * $creditedYears), 'Labor Code Art. 298: one-half month pay per year of service, at least one month.'],
            'disease' => [max($monthlyPay, $monthlyPay / 2 * $creditedYears), 'Labor Code Art. 299: one-half month pay per year of service, at least one month.'],
            'just_cause' => [0.0, 'Labor Code Art. 297: no separation pay for dismissal with a just cause.'],
            'retirement' => [0.0, 'Retirement is paid as retirement pay below, not separation pay.'],
            default => [0.0, 'Labor Code: no separation pay for voluntary resignation, unless school policy grants it.'],
        };

        $retirementEligible = $cause === 'retirement' && $age !== null && $age >= 60 && $fullYears >= 5;
        $retirement = $retirementEligible ? round($dailyWage * 22.5 * $creditedYears, 2) : 0.0;
        $retirementRule = match (true) {
            $cause !== 'retirement' => 'Applies only when the cause is retirement.',
            $retirementEligible => 'RA 7641: 22.5 days of pay for every year of service.',
            default => 'RA 7641 needs age 60 or older and at least 5 years of service (age '.($age ?? 'unknown').', '.$fullYears.' full years).',
        };

        $gratuity = round((float) DB::table('payslips')
            ->where('employee_id', $case->employee_id)
            ->whereNotNull('released_at')
            ->sum('peraa_ceap'), 2);

        return [
            'cause' => $cause,
            'cause_label' => self::CAUSES[$cause],
            'hired_on' => $hiredOn?->format('M j, Y'),
            'hired_is_estimate' => $hiredIsEstimate,
            'service' => $fullYears.' '.($fullYears === 1 ? 'year' : 'years').', '.$extraMonths.' '.($extraMonths === 1 ? 'month' : 'months'),
            'credited_years' => $creditedYears,
            'age' => $age,
            'daily_wage' => $dailyWage,
            'monthly_pay' => $monthlyPay,
            'lines' => [
                ['label' => 'Separation pay', 'amount' => round($separation, 2), 'rule' => $separationRule],
                ['label' => 'Retirement pay', 'amount' => $retirement, 'rule' => $retirementRule],
                ['label' => 'Gratuity pay', 'amount' => $gratuity, 'rule' => 'Total PERAA/CEAP deducted on released payslips.'],
            ],
            'total' => round($separation + $retirement + $gratuity, 2),
        ];
    }
}
