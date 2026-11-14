<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table): void {
            $table->string('interview_response', 32)->nullable()->after('interview_location');
            $table->text('interview_response_reason')->nullable()->after('interview_response');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table): void {
            $table->dropColumn(['interview_response', 'interview_response_reason']);
        });
    }
};
