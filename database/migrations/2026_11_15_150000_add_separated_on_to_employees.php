<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Resigned or separated employees are archived: after their last working day
 * they only appear in Offboarding (Archive). separated_on is set when Admin
 * gives the offboarding case final approval.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'separated_on')) {
                $table->date('separated_on')->nullable()->index();
            }
        });

        // Cases Admin already approved before this change.
        $closed = DB::table('offboarding_cases')
            ->where('admin_final_approved', true)
            ->get(['employee_id', 'last_working_day']);

        foreach ($closed as $case) {
            DB::table('employees')->where('id', $case->employee_id)->whereNull('separated_on')
                ->update(['separated_on' => $case->last_working_day]);
        }
    }

    public function down(): void
    {
        Schema::table('employees', fn (Blueprint $t) => $t->dropColumn('separated_on'));
    }
};
