<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns for the payroll rules the client confirmed:
 *  - government contributions entered once per employee (employee and employer share)
 *  - paid leave and holiday pay as their own earnings rows
 *  - undertime and withholding tax as their own deductions
 *  - employer shares on each payslip, for the remittances report
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            foreach (['sss', 'philhealth', 'pagibig'] as $agency) {
                if (! Schema::hasColumn('employees', $agency.'_ee')) {
                    $table->decimal($agency.'_ee', 10, 2)->nullable();
                    $table->decimal($agency.'_er', 10, 2)->nullable();
                }
            }
        });

        Schema::table('payroll_records', function (Blueprint $table) {
            if (! Schema::hasColumn('payroll_records', 'leave_days')) {
                $table->decimal('leave_days', 5, 2)->default(0);
                $table->decimal('holiday_days', 5, 2)->default(0);
            }
        });

        Schema::table('payslips', function (Blueprint $table) {
            if (! Schema::hasColumn('payslips', 'leave_days')) {
                $table->decimal('leave_days', 5, 2)->default(0);
                $table->decimal('leave_pay', 12, 2)->default(0);
                $table->decimal('holiday_days', 5, 2)->default(0);
                $table->decimal('holiday_pay', 12, 2)->default(0);
                $table->decimal('withholding_tax', 12, 2)->default(0);
                $table->decimal('sss_er', 12, 2)->default(0);
                $table->decimal('philhealth_er', 12, 2)->default(0);
                $table->decimal('hdmf_er', 12, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn(['leave_days', 'leave_pay', 'holiday_days', 'holiday_pay', 'withholding_tax', 'sss_er', 'philhealth_er', 'hdmf_er']);
        });
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn(['leave_days', 'holiday_days']);
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['sss_ee', 'sss_er', 'philhealth_ee', 'philhealth_er', 'pagibig_ee', 'pagibig_er']);
        });
    }
};
