<?php

namespace App\Support;

/**
 * Payroll rules confirmed by the client (St. Mary Magdalene School).
 *
 * Earnings
 *  - Basic pay: days actually worked x daily wage.
 *  - Paid leave: each day = one full daily wage (its own row).
 *  - Holiday pay: each holiday = one full daily wage, regular and special alike (its own row).
 *  - Honorarium: fixed, administrative staff only.
 *
 * Deductions
 *  - Late and undertime: 1.80 per minute each.
 *  - MALASAKITAN: 50 every pay period.
 *  - SSS: employee share from the SSS bracket table (5% of the monthly salary credit).
 *  - PhilHealth: 2.5% of the monthly wage.  Pag-IBIG (HDMF): 200.
 *    All three are automatic (no manual amounts), 30th pay period only.
 *  - PERAA/CEAP: 4% of the monthly wage, 30th pay period only.
 *  - Withholding tax: only when yearly taxable pay is over 250,000 (BIR graduated
 *    income tax table, TRAIN law, 2023 onwards). Monthly tax, deducted on the 30th.
 *  - Loans and cash advance as entered.
 *  - Net pay never goes below zero: deductions are taken in order of priority until the
 *    pay runs out (government first, then attendance, then loans).
 */
final class PayrollCalculator
{
    public const MONTHLY_DAYS = 26;
    public const PER_MINUTE = 1.80;
    public const MALASAKITAN = 50.00;
    public const HONORARIUM = 1900.00;
    public const PERAA_RATE = 0.04;
    public const PHILHEALTH_RATE = 0.025;
    public const HDMF_SHARE = 200.00;
    public const TAX_FREE_YEARLY = 250000.00;

    /** Deduction keys in the order they are taken when pay is not enough. */
    public const ORDER = [
        'sss', 'philhealth', 'hdmf', 'withholding_tax', 'peraa_ceap',
        'late', 'undertime', 'malasakitan',
        'sss_loan', 'philhealth_loan', 'peraa_ceap_loan', 'hdmf_loan', 'cash_advance',
    ];

    /** SSS employee share used when none was entered for the employee. */
    public static function sssDefault(float $monthlyWage): float
    {
        // SSS table: the salary falls in a 500 peso bracket whose midpoint is the
        // monthly salary credit (MSC). The employee pays 5% of the MSC:
        // 12,250 to 12,749.99 pays 625.00, each higher bracket adds 25.00,
        // and 34,750 and over pays 1,750.00 (MSC 35,000). The lowest MSC is 5,000.
        if ($monthlyWage <= 0) {
            return 0.0;
        }
        $msc = max(5000, min(35000, floor(($monthlyWage + 250) / 500) * 500));

        return round($msc * 0.05, 2);
    }

    /**
     * Monthly government contributions for an employee.
     *
     * @return array{sss: float, sss_er: float, philhealth: float, philhealth_er: float, hdmf: float, hdmf_er: float, entered: bool}
     */
    public static function contributions(float $monthlyWage, ?object $employee = null): array
    {
        // Always the standard rates: amounts are no longer typed in per employee.
        $v = fn (string $key) => null;

        $sss = $v('sss_ee') ?? self::sssDefault($monthlyWage);
        $phil = $v('philhealth_ee') ?? round($monthlyWage * self::PHILHEALTH_RATE, 2);
        $hdmf = $v('pagibig_ee') ?? self::HDMF_SHARE;

        return [
            'sss' => $sss,
            'sss_er' => $v('sss_er') ?? $sss * 2,
            'philhealth' => $phil,
            'philhealth_er' => $v('philhealth_er') ?? $phil,
            'hdmf' => $hdmf,
            'hdmf_er' => $v('pagibig_er') ?? self::HDMF_SHARE,
            'entered' => $v('sss_ee') !== null || $v('philhealth_ee') !== null || $v('pagibig_ee') !== null,
        ];
    }

    /** BIR graduated income tax on yearly taxable pay (TRAIN law, 2023 onwards). */
    public static function yearlyTax(float $taxable): float
    {
        if ($taxable <= self::TAX_FREE_YEARLY) {
            return 0.0;
        }

        $brackets = [
            [8000000, 2202500, 0.35],
            [2000000, 402500, 0.30],
            [800000, 102500, 0.25],
            [400000, 22500, 0.20],
            [250000, 0, 0.15],
        ];

        foreach ($brackets as [$over, $base, $rate]) {
            if ($taxable > $over) {
                return round($base + ($taxable - $over) * $rate, 2);
            }
        }

        return 0.0;
    }

    /**
     * @param  array  $in  days_worked, hours_worked, late_minutes, undertime_minutes, leave_days, holiday_days,
     *                     sss_loan, philhealth_loan, peraa_ceap_loan, hdmf_loan, cash_advance
     */
    public static function compute(array $in, float $dailyWage, string $cutoff, bool $isAdmin, ?object $employee = null): array
    {
        $num = fn (string $k) => max(0.0, (float) ($in[$k] ?? 0));
        $is30 = $cutoff === '30th';

        $days = $num('days_worked');
        $leaveDays = $num('leave_days');
        $holidayDays = $num('holiday_days');
        $lateMinutes = $num('late_minutes');
        $undertimeMinutes = $num('undertime_minutes');

        $basic = round($dailyWage * $days, 2);
        $leavePay = round($dailyWage * $leaveDays, 2);
        $holidayPay = round($dailyWage * $holidayDays, 2);
        $honorarium = $isAdmin ? self::HONORARIUM : 0.0;
        $gross = round($basic + $leavePay + $holidayPay + $honorarium, 2);

        $monthlyWage = round($dailyWage * self::MONTHLY_DAYS, 2);
        $gov = self::contributions($monthlyWage, $employee);

        // Tax: monthly taxable pay = monthly wage + honorarium (both pay periods)
        // minus the employee's government contributions.
        $monthlyTaxable = $monthlyWage + ($isAdmin ? self::HONORARIUM * 2 : 0) - ($gov['sss'] + $gov['philhealth'] + $gov['hdmf']);
        $monthlyTax = round(self::yearlyTax(max(0, $monthlyTaxable) * 12) / 12, 2);

        $cashAdvance = $num('cash_advance');
        $firstInstallment = round($cashAdvance / 2, 2);

        $wanted = [
            'sss' => $is30 ? $gov['sss'] : 0.0,
            'philhealth' => $is30 ? $gov['philhealth'] : 0.0,
            'hdmf' => $is30 ? $gov['hdmf'] : 0.0,
            'withholding_tax' => $is30 ? $monthlyTax : 0.0,
            'peraa_ceap' => $is30 ? round($monthlyWage * self::PERAA_RATE, 2) : 0.0,
            'late' => round($lateMinutes * self::PER_MINUTE, 2),
            'undertime' => round($undertimeMinutes * self::PER_MINUTE, 2),
            'malasakitan' => self::MALASAKITAN,
            'sss_loan' => $num('sss_loan'),
            'philhealth_loan' => $num('philhealth_loan'),
            'peraa_ceap_loan' => $num('peraa_ceap_loan'),
            'hdmf_loan' => $num('hdmf_loan'),
            'cash_advance' => $is30 ? round($cashAdvance - $firstInstallment, 2) : $firstInstallment,
        ];

        // Take deductions in order until the pay runs out, so net pay is never negative.
        $left = $gross;
        $taken = [];
        $notTaken = 0.0;
        foreach (self::ORDER as $key) {
            $amount = round(min($wanted[$key], max(0, $left)), 2);
            $taken[$key] = $amount;
            $notTaken += $wanted[$key] - $amount;
            $left = round($left - $amount, 2);
        }

        $total = round(array_sum($taken), 2);

        return [
            'monthlyWage' => $monthlyWage,
            'dailyWage' => $dailyWage,
            'days' => $days,
            'hours' => $num('hours_worked'),
            'lateMinutes' => $lateMinutes,
            'undertimeMinutes' => $undertimeMinutes,
            'leaveDays' => $leaveDays,
            'holidayDays' => $holidayDays,
            'basicPay' => $basic,
            'leavePay' => $leavePay,
            'holidayPay' => $holidayPay,
            'honorarium' => $honorarium,
            'grossPay' => $gross,
            'wanted' => $wanted,
            'breakdown' => $taken,
            'notDeducted' => round($notTaken, 2),
            'employer' => [
                'sss' => $is30 ? round($gov['sss_er'], 2) : 0.0,
                'philhealth' => $is30 ? round($gov['philhealth_er'], 2) : 0.0,
                'hdmf' => $is30 ? round($gov['hdmf_er'], 2) : 0.0,
            ],
            'contributions' => $gov,
            'totalDeductions' => $total,
            'netPay' => round($gross - $total, 2),
        ];
    }
}
