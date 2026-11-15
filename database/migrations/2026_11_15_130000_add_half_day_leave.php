<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Half-day leave: a request can be for half a day (morning or afternoon) and
 * then counts as 0.5 against the chosen leave type.
 *
 * attendance_records.leave_portion records how much of the day was approved
 * paid leave (0.5 or 1). The biometric sync never changes it, so an employee
 * who works the other half keeps both the worked half and the leave half.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('leave_requests', 'half_day')) {
                $table->boolean('half_day')->default(false);
                $table->string('half_day_period', 2)->nullable(); // am or pm
            }
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            if (! Schema::hasColumn('attendance_records', 'leave_portion')) {
                $table->decimal('leave_portion', 3, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', fn (Blueprint $t) => $t->dropColumn(['half_day', 'half_day_period']));
        Schema::table('attendance_records', fn (Blueprint $t) => $t->dropColumn('leave_portion'));
    }
};
