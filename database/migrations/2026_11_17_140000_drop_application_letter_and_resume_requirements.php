<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Application letter and resume are already sent with the job application,
 * so they are no longer onboarding requirements (adviser revision, Nov 17 2026).
 */
return new class extends Migration
{
    private const NAMES = [
        'Application Letter (if applicable)',
        'Comprehensive Resume with recent and colored picture',
    ];

    public function up(): void
    {
        DB::table('submission_boxes')->where('is_requirement', true)->whereIn('name', self::NAMES)->update(['is_active' => false]);
    }

    public function down(): void
    {
        DB::table('submission_boxes')->where('is_requirement', true)->whereIn('name', self::NAMES)->update(['is_active' => true]);
    }
};
