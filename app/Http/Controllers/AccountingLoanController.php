<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AccountingLoanController extends Controller
{
    /**
     * Accounting can review requests after HR approves them.
     */
    public function index()
    {
        $this->authorizeAccounting();

        $loans = LoanRequest::with('employee')
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $approvedLoans = LoanRequest::fullyApproved()
            ->with(['employee', 'repayments.payslip'])
            ->withSum('repayments', 'amount')
            ->orderByDesc('updated_at')
            ->get();

        $loanBalances = $approvedLoans->map(fn (LoanRequest $loan) => [
            'employee' => $loan->employee?->name ?? 'Employee',
            'employee_number' => $loan->employee?->employee_id ?? 'ID unavailable',
            'loan_type' => $loan->loan_type ?: 'Not provided',
            'approved_amount' => (float) $loan->amount,
            'repayments' => $loan->repayments->sortBy('paid_at')->values(),
            'outstanding' => $loan->outstandingBalance(),
        ]);
        $totalOutstanding = round($loanBalances->sum('outstanding'), 2);
        $awaitingAccounting = LoanRequest::where('hr_status', 'approved')
            ->where('accounting_status', 'pending')
            ->count();

        return view('loan', compact('loans', 'loanBalances', 'totalOutstanding', 'awaitingAccounting'));
    }

    private function authorizeAccounting(): void
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User && $user->hasRole('Accounting'),
            403,
            'Only Accounting can view loan requests here.'
        );
    }
}
