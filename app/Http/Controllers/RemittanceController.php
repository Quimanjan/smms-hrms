<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Government remittances for one month: what the school sends to SSS,
 * PhilHealth, Pag-IBIG and the BIR. Amounts come from the month's payslips
 * that Admin and HR approved (contributions are deducted on the 30th).
 */
class RemittanceController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['month' => ['nullable', 'date_format:Y-m']]);
        $month = Carbon::createFromFormat('Y-m-d', ($data['month'] ?? now()->format('Y-m')).'-01')->startOfDay();

        $payslips = Payslip::query()
            ->with('employee')
            ->whereDate('transfer_date', $month->toDateString())
            ->whereNotNull('sent_to_admin_at')
            ->where('admin_status', Payslip::APPROVED)
            ->where('hr_status', Payslip::APPROVED)
            ->get();

        $last4 = function ($value) {
            $digits = preg_replace('/\D+/', '', (string) $value);

            return $digits === '' ? '' : '•••• '.substr($digits, -4);
        };

        $rows = $payslips
            ->groupBy('employee_id')
            ->map(function ($slips) use ($last4) {
                $e = $slips->first()->employee;
                $sum = fn (string $col) => round((float) $slips->sum($col), 2);

                return [
                    'name' => $e?->name ?? 'Unknown employee',
                    'employee_id' => $e?->employee_id,
                    'sss_no' => $last4($e?->sss_number),
                    'philhealth_no' => $last4($e?->philhealth_number),
                    'pagibig_no' => $last4($e?->pagibig_number),
                    'tin' => $last4($e?->tin),
                    'sss_ee' => $sum('sss'),
                    'sss_er' => $sum('sss_er'),
                    'philhealth_ee' => $sum('philhealth'),
                    'philhealth_er' => $sum('philhealth_er'),
                    'pagibig_ee' => $sum('hdmf'),
                    'pagibig_er' => $sum('hdmf_er'),
                    'tax' => $sum('withholding_tax'),
                ];
            })
            ->sortBy('name')
            ->values();

        $total = fn (string $k) => round((float) $rows->sum($k), 2);
        $totals = collect(['sss_ee', 'sss_er', 'philhealth_ee', 'philhealth_er', 'pagibig_ee', 'pagibig_er', 'tax'])
            ->mapWithKeys(fn ($k) => [$k => $total($k)])
            ->all();

        return view('remittances', [
            'month' => $month,
            'rows' => $rows,
            'totals' => $totals,
            'agencyTotals' => [
                ['SSS', $totals['sss_ee'] + $totals['sss_er']],
                ['PhilHealth', $totals['philhealth_ee'] + $totals['philhealth_er']],
                ['Pag-IBIG', $totals['pagibig_ee'] + $totals['pagibig_er']],
                ['BIR (tax withheld)', $totals['tax']],
            ],
        ]);
    }
}
