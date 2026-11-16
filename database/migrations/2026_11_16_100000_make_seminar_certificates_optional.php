<?php

use App\Support\OnboardingRequirements;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seminar and Training Certificates are optional (client answer, Nov 16 2026).
 * Re-applies the optional flag of every onboarding requirement from the list.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('submission_boxes', 'is_optional')) {
            return;
        }
        foreach (OnboardingRequirements::all() as $i => $req) {
            DB::table('submission_boxes')
                ->where('is_requirement', true)
                ->where('sort_order', $i + 1)
                ->update(['is_optional' => $req['optional']]);
        }
    }

    public function down(): void
    {
        DB::table('submission_boxes')
            ->where('is_requirement', true)
            ->where('name', 'Seminar and Training Certificates (photocopy)')
            ->update(['is_optional' => false]);
    }
};
