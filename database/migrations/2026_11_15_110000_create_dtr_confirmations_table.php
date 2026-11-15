<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An employee's confirmation that their DTR for one pay period is correct.
 * Accounting can transfer a payslip to Salary Monitoring only after this.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dtr_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('period', 7);   // Y-m
            $table->string('cutoff', 4);   // 15th or 30th
            $table->timestamp('confirmed_at');
            $table->timestamps();

            $table->unique(['employee_id', 'period', 'cutoff']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dtr_confirmations');
    }
};
