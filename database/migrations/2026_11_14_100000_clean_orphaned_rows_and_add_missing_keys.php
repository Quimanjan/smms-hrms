<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Data integrity clean-up.
 *
 * - Removes role assignments and notifications that point to user accounts
 *   which no longer exist (left behind when accounts were deleted).
 * - Links each loan repayment to its payslip with a real foreign key.
 * - Clears expired login-throttle rows from the cache table.
 *
 * Nothing that belongs to an existing user or employee is removed.
 */
return new class extends Migration
{
    public function up(): void
    {
        $userModel = 'App\\Models\\User';

        foreach (['model_has_roles' => 'model', 'model_has_permissions' => 'model'] as $table => $prefix) {
            DB::table($table)
                ->where("{$prefix}_type", $userModel)
                ->whereNotIn("{$prefix}_id", DB::table('users')->select('id'))
                ->delete();
        }

        DB::table('notifications')
            ->where('notifiable_type', $userModel)
            ->whereNotIn('notifiable_id', DB::table('users')->select('id'))
            ->delete();

        DB::table('cache')->where('expiration', '<', now()->getTimestamp())->delete();

        // Repayments whose payslip was deleted keep their amount but lose the link.
        DB::table('loan_repayments')
            ->whereNotNull('payslip_id')
            ->whereNotIn('payslip_id', DB::table('payslips')->select('id'))
            ->update(['payslip_id' => null]);

        if (! $this->hasForeignKey('loan_repayments', 'payslip_id')) {
            Schema::table('loan_repayments', function (Blueprint $table) {
                $table->foreign('payslip_id')->references('id')->on('payslips')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if ($this->hasForeignKey('loan_repayments', 'payslip_id')) {
            Schema::table('loan_repayments', function (Blueprint $table) {
                $table->dropForeign(['payslip_id']);
            });
        }
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        foreach (Schema::getForeignKeys($table) as $key) {
            if (in_array($column, $key['columns'], true)) {
                return true;
            }
        }

        return false;
    }
};
