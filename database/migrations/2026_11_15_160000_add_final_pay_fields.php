<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Final pay on offboarding needs the employee's date hired (length of service)
 * and the cause of separation (it decides whether separation pay is due).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'date_hired')) {
                $table->date('date_hired')->nullable();
            }
        });

        Schema::table('offboarding_cases', function (Blueprint $table) {
            if (! Schema::hasColumn('offboarding_cases', 'separation_cause')) {
                $table->string('separation_cause', 30)->default('resignation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', fn (Blueprint $t) => $t->dropColumn('date_hired'));
        Schema::table('offboarding_cases', fn (Blueprint $t) => $t->dropColumn('separation_cause'));
    }
};
