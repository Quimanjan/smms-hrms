<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Safe to run: your database already has employees.civil_status and employees.gender.
 * This only adds a column if it is missing (for example on a fresh install).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'gender')) {
                $table->string('gender')->nullable()->after('birthday');
            }
            if (! Schema::hasColumn('employees', 'civil_status')) {
                $table->string('civil_status')->nullable()->after('birthday');
            }
        });
    }

    public function down(): void
    {
        // Intentionally empty: the columns existed before this migration.
    }
};
