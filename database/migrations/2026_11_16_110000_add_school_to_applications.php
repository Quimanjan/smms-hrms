<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Applicants state the school where they finished their highest education
 * (panel revision, Nov 16 2026).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'education_school')) {
                $table->string('education_school', 160)->nullable()->after('education');
            }
            if (! Schema::hasColumn('applications', 'education_year')) {
                $table->string('education_year', 24)->nullable()->after('education_school');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', fn (Blueprint $t) => $t->dropColumn(['education_school', 'education_year']));
    }
};
