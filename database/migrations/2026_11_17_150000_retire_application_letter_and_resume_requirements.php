<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// The application letter and resume are already sent with the job application,
// so they are no longer onboarding requirements. Uploaded files are kept.
return new class extends Migration
{
    private const NAMES = [
        'Application Letter (if applicable)',
        'Comprehensive Resume with recent and colored picture',
    ];

    public function up(): void
    {
        DB::table('submission_boxes')->whereIn('name', self::NAMES)->where('is_requirement', true)->update(['is_active' => false]);
    }

    public function down(): void
    {
        DB::table('submission_boxes')->whereIn('name', self::NAMES)->where('is_requirement', true)->update(['is_active' => true]);
    }
};
