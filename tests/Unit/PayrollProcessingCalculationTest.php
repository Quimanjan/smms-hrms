<?php

use App\Http\Controllers\PayrollProcessingController;
use App\Models\Employee;
use App\Models\LoanRequest;
use App\Models\NonWorkingDay;
use App\Services\CashAdvancePayrollService;
use Illuminate\Support\Collection;

function payrollTestEmployee(array $attributes = []): Employee
{
    return new Employee($attributes + ['role_type' => 'Teaching Staff', 'position' => 'teacher', 'department' => 'highschool']);
}

function calculatePayrollProcessingRow(
    string $cutoff,
    float $lateMinutes = 0,
    float $hoursWorked = 176,
    float $cashAdvance = 0,
    float $undertimeMinutes = 0,
    float $dailyWage = 650,
    float $daysWorked = 22,
    ?Employee $employee = null,
): array {
    $controller = new PayrollProcessingController;
    $calculate = new ReflectionMethod($controller, 'compute');

    return $calculate->invoke($controller, [
        'step_no' => 1,
        'days_worked' => $daysWorked,
        'hours_worked' => $hoursWorked,
        'late_minutes' => $lateMinutes,
        'undertime_minutes' => $undertimeMinutes,
        'sss_loan' => 0,
        'philhealth_loan' => 0,
        'peraa_ceap_loan' => 0,
        'hdmf_loan' => 0,
        'cash_advance' => $cashAdvance,
    ], [['name' => 'Step 1', 'dailyWage' => $dailyWage]], $cutoff, $employee ?? payrollTestEmployee());
}

test('HDMF contribution is fixed at 200 on the 30th pay period only', function () {
    $fifteenth = calculatePayrollProcessingRow('15th');
    $thirtieth = calculatePayrollProcessingRow('30th');

    expect($fifteenth['breakdown']['hdmf'])->toBe(0.0)
        ->and($thirtieth['breakdown']['hdmf'])->toBe(200.0);
});

test('administrative staff receive a 1900 peso honorarium in payroll', function () {
    $controller = new PayrollProcessingController;
    $isAdministrative = new ReflectionMethod($controller, 'isAdministrative');
    $compute = new ReflectionMethod($controller, 'compute');
    $employee = new Employee([
        'role_type' => 'Administrative Staff',
        'position' => 'Office Staff',
        'department' => 'highschool',
    ]);

    $pay = $compute->invoke($controller, [
        'step_no' => 1,
        'days_worked' => 0,
        'hours_worked' => 0,
        'late_minutes' => 0,
    ], [['name' => 'Step 1', 'dailyWage' => 650]], '15th', $employee);

    expect($isAdministrative->invoke($controller, $employee))->toBeTrue()
        ->and($pay['honorarium'])->toBe(1900.0)
        ->and($pay['grossPay'])->toBe(1900.0);
});

test('cash advances are deducted in equal installments on both payroll cutoffs', function () {
    $fifteenth = calculatePayrollProcessingRow('15th', cashAdvance: 2000);
    $thirtieth = calculatePayrollProcessingRow('30th', cashAdvance: 2000);

    expect($fifteenth['breakdown']['cash_advance'])->toBe(1000.0)
        ->and($thirtieth['breakdown']['cash_advance'])->toBe(1000.0);
});

// Client rule: undertime is deducted at 1.80 per minute, like late.
test('undertime is deducted at 1.80 per minute', function () {
    $withUndertime = calculatePayrollProcessingRow('15th', undertimeMinutes: 188);
    $withoutUndertime = calculatePayrollProcessingRow('15th');

    expect($withUndertime['breakdown']['undertime'])->toBe(338.4)
        ->and(round($withoutUndertime['netPay'] - $withUndertime['netPay'], 2))->toBe(338.4);
});

test('cash advance installments reconcile to the monthly total when there are cents', function () {
    $fifteenth = calculatePayrollProcessingRow('15th', cashAdvance: 2000.01);
    $thirtieth = calculatePayrollProcessingRow('30th', cashAdvance: 2000.01);

    expect($fifteenth['breakdown']['cash_advance'])->toBe(1000.01)
        ->and($thirtieth['breakdown']['cash_advance'])->toBe(1000.0)
        ->and($fifteenth['breakdown']['cash_advance'] + $thirtieth['breakdown']['cash_advance'])
        ->toBe(2000.01);
});

// Client rule: withholding tax only when yearly pay is over 250,000 (BIR table).
test('withholding tax applies only above 250,000 a year', function () {
    $low = calculatePayrollProcessingRow('30th', dailyWage: 650);
    $high = calculatePayrollProcessingRow('30th', dailyWage: 1388.27);
    $highFifteenth = calculatePayrollProcessingRow('15th', dailyWage: 1388.27);

    // 1388.27 x 26 = 36,095.02 a month; less SSS 1,000, PhilHealth 902.38, Pag-IBIG 200
    // = 33,992.64 x 12 = 407,911.68 a year => 22,500 + 20% of 7,911.68 = 24,082.34 a year.
    expect($low['breakdown']['withholding_tax'])->toBe(0.0)
        ->and($high['breakdown']['withholding_tax'])->toBe(2006.86)
        ->and($highFifteenth['breakdown']['withholding_tax'])->toBe(0.0);
});

test('contributions entered once for the employee replace the standard rates', function () {
    $employee = payrollTestEmployee();
    $employee->sss_ee = 900;
    $employee->pagibig_ee = 300;

    $pay = calculatePayrollProcessingRow('30th', employee: $employee);

    expect($pay['breakdown']['sss'])->toBe(900.0)
        ->and($pay['breakdown']['hdmf'])->toBe(300.0)
        ->and($pay['employer']['sss'])->toBe(1800.0);
});

test('net pay never goes below zero', function () {
    $pay = calculatePayrollProcessingRow('30th', daysWorked: 0, hoursWorked: 0, cashAdvance: 5000);

    expect($pay['grossPay'])->toBe(0.0)
        ->and($pay['netPay'])->toBe(0.0)
        ->and($pay['notDeducted'])->toBeGreaterThan(0.0);
});

test('cash advance repayment allocations split the total across cutoff records', function () {
    $service = new CashAdvancePayrollService;
    $firstLoan = new LoanRequest;
    $firstLoan->id = 10;
    $firstLoan->payroll_balance = 1500.0;
    $secondLoan = new LoanRequest;
    $secondLoan->id = 11;
    $secondLoan->payroll_balance = 500.0;
    $loans = new Collection([$firstLoan, $secondLoan]);

    $fifteenth = $service->installmentAllocations($loans, '15th');
    $thirtieth = $service->installmentAllocations($loans, '30th');

    expect(array_sum($fifteenth))->toBe(1000.0)
        ->and(array_sum($thirtieth))->toBe(1000.0);
});

test('payroll attendance metrics use attendance records and the employee schedule', function () {
    $controller = new PayrollProcessingController;
    $summarize = new ReflectionMethod($controller, 'attendanceMetrics');
    $employee = new Employee(['position' => 'teacher']);
    $records = new Collection([
        (object) [
            'date' => '2026-03-02',
            'time_in' => '07:11:00',
            'time_out' => '16:30:00',
            'status' => 'Late',
            'hours_worked' => null,
        ],
        (object) [
            'date' => '2026-03-03',
            'time_in' => '08:00:00',
            'time_out' => '12:00:00',
            'status' => 'Half Day',
            'hours_worked' => null,
        ],
    ]);

    expect($summarize->invoke($controller, $employee, $records))
        ->toBe(['days_worked' => 1.5, 'hours_worked' => 11.82, 'late_minutes' => 71, 'undertime_minutes' => 0, 'leave_days' => 0.0, 'holiday_days' => 0.0, 'unpaid_leave_days' => 0.0]);
});

test('payroll credits a full day when an employee clocks in within grace and out at 4:30 PM', function () {
    $controller = new PayrollProcessingController;
    $summarize = new ReflectionMethod($controller, 'attendanceMetrics');
    $employee = new Employee(['position' => 'teacher']);
    $records = new Collection([
        (object) [
            'date' => '2026-03-02',
            'time_in' => '07:10:00',
            'time_out' => '16:30:00',
            'status' => 'Present',
            'hours_worked' => null,
        ],
    ]);

    expect($summarize->invoke($controller, $employee, $records))
        ->toBe(['days_worked' => 1.0, 'hours_worked' => 8.0, 'late_minutes' => 0, 'undertime_minutes' => 0, 'leave_days' => 0.0, 'holiday_days' => 0.0, 'unpaid_leave_days' => 0.0]);
});

test('payroll does not credit time worked after the scheduled end', function () {
    $controller = new PayrollProcessingController;
    $summarize = new ReflectionMethod($controller, 'attendanceMetrics');
    $employee = new Employee(['position' => 'teacher']);
    $records = new Collection([
        (object) [
            'date' => '2026-03-02',
            'time_in' => '07:00:00',
            'time_out' => '18:00:00',
            'status' => 'Present',
            'hours_worked' => 11,
        ],
    ]);

    expect($summarize->invoke($controller, $employee, $records))
        ->toBe(['days_worked' => 1.0, 'hours_worked' => 8.0, 'late_minutes' => 0, 'undertime_minutes' => 0, 'leave_days' => 0.0, 'holiday_days' => 0.0, 'unpaid_leave_days' => 0.0]);
});

test('payroll counts hours worked on a non-working day review record', function () {
    $controller = new PayrollProcessingController;
    $summarize = new ReflectionMethod($controller, 'attendanceMetrics');
    $employee = new Employee(['position' => 'teacher']);
    $records = new Collection([
        (object) [
            'date' => '2026-03-02',
            'time_in' => '07:00:00',
            'time_out' => '16:30:00',
            'status' => 'Non-Working Day Review',
            'hours_worked' => null,
        ],
    ]);

    expect($summarize->invoke($controller, $employee, $records))
        ->toBe(['days_worked' => 1.0, 'hours_worked' => 8.0, 'late_minutes' => 0, 'undertime_minutes' => 0, 'leave_days' => 0.0, 'holiday_days' => 0.0, 'unpaid_leave_days' => 0.0]);
});

// A paid non-working day that is not a holiday (e.g. a school suspension) follows HR's setting.
test('payroll credits configured paid holiday days as holiday pay', function () {
    $controller = new PayrollProcessingController;
    $summarize = new ReflectionMethod($controller, 'attendanceMetrics');
    $employee = new Employee(['position' => 'teacher']);
    $holiday = (object) [
        'date' => '2026-03-02',
        'time_in' => null,
        'time_out' => null,
        'hours_worked' => 4,
        'status' => 'Paid Holiday',
        'nonWorkingDay' => new NonWorkingDay([
            'pay_type' => NonWorkingDay::PAID,
            'paid_days' => 0.5,
            'paid_hours' => 4,
        ]),
    ];

    expect($summarize->invoke($controller, $employee, new Collection([$holiday])))
        ->toBe(['days_worked' => 0.0, 'hours_worked' => 0.0, 'late_minutes' => 0, 'undertime_minutes' => 0, 'leave_days' => 0.0, 'holiday_days' => 0.5, 'unpaid_leave_days' => 0.0]);
});

// Client rule: paid leave is its own earnings row worth one daily wage; unpaid leave earns nothing.
test('paid leave is paid as its own row and unpaid leave earns nothing', function () {
    $controller = new PayrollProcessingController;
    $summarize = new ReflectionMethod($controller, 'attendanceMetrics');
    $compute = new ReflectionMethod($controller, 'compute');
    $employee = payrollTestEmployee();
    $records = new Collection([
        (object) [
            'date' => '2026-03-02',
            'time_in' => null,
            'time_out' => null,
            'status' => 'Paid Leave',
            'hours_worked' => 0,
        ],
        (object) [
            'date' => '2026-03-03',
            'time_in' => null,
            'time_out' => null,
            'status' => 'Unpaid Leave',
            'hours_worked' => 0,
        ],
    ]);

    $metrics = $summarize->invoke($controller, $employee, $records);
    $pay = $compute->invoke($controller, ['step_no' => 1] + $metrics, [['name' => 'Step 1', 'dailyWage' => 650]], '15th', $employee);

    expect($metrics)
        ->toBe([
            'days_worked' => 0.0,
            'hours_worked' => 0.0,
            'late_minutes' => 0,
            'undertime_minutes' => 0,
            'leave_days' => 1.0,
            'holiday_days' => 0.0,
            'unpaid_leave_days' => 1.0,
        ])
        ->and($pay['basicPay'])->toBe(0.0)
        ->and($pay['leavePay'])->toBe(650.0)
        ->and($pay['grossPay'])->toBe(650.0)
        ->and($pay['breakdown'])->not->toHaveKey('unpaid_leave');
});

test('payroll attendance ranges follow the 5th and 20th cutoffs', function () {
    $controller = new PayrollProcessingController;
    $range = new ReflectionMethod($controller, 'cutoffDateRange');

    expect($range->invoke($controller, '2026-02', '15th'))
        ->toBe(['2026-01-21', '2026-02-05'])
        ->and($range->invoke($controller, '2026-02', '30th'))
        ->toBe(['2026-02-06', '2026-02-20']);
});

test('15th payroll attendance range crosses year boundary correctly', function () {
    $controller = new PayrollProcessingController;
    $range = new ReflectionMethod($controller, 'cutoffDateRange');

    expect($range->invoke($controller, '2026-01', '15th'))
        ->toBe(['2025-12-21', '2026-01-05']);
});

test('payroll deducts 1.80 for every recorded late minute', function () {
    $result = calculatePayrollProcessingRow('15th', 5, 175.98);

    expect($result['lateMinutes'])->toBe(5.0)
        ->and($result['breakdown']['late'])->toBe(9.0);
});
