<?php

use App\Support\OnboardingRequirements;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The client asked for a fixed list of onboarding requirements instead of
 * submission boxes that HR creates, and for no deadline on employee documents.
 *
 * The 29 requirements are stored as submission boxes flagged is_requirement,
 * so uploads, reviews and files keep working. Boxes HR made before are hidden
 * (is_active = 0), not deleted, so their files are still on record.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_boxes', function (Blueprint $table) {
            if (! Schema::hasColumn('submission_boxes', 'is_requirement')) {
                $table->boolean('is_requirement')->default(false);
                $table->unsignedSmallInteger('sort_order')->nullable();
                $table->boolean('is_optional')->default(false);
            }
        });

        DB::table('submission_boxes')->where('is_requirement', false)->update(['is_active' => false]);

        $now = now();
        foreach (OnboardingRequirements::all() as $i => $req) {
            $exists = DB::table('submission_boxes')->where('is_requirement', true)->where('sort_order', $i + 1)->exists();
            if ($exists) {
                continue;
            }
            DB::table('submission_boxes')->insert([
                'name' => $req['name'],
                'type' => 'Onboarding requirement',
                'deadline' => null,
                'instructions' => null,
                'is_active' => true,
                'is_requirement' => true,
                'is_optional' => $req['optional'],
                'sort_order' => $i + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // No deadlines on employee documents.
        DB::table('submission_boxes')->update(['deadline' => null]);
    }

    public function down(): void
    {
        DB::table('submission_boxes')->where('is_requirement', true)->delete();
        Schema::table('submission_boxes', fn (Blueprint $t) => $t->dropColumn(['is_requirement', 'sort_order', 'is_optional']));
    }
};
