<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_openings', function (Blueprint $table): void {
            $table->string('job_type')->nullable()->after('title');
            $table->text('description')->nullable()->after('job_type');
            $table->boolean('is_open')->default(true)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('job_openings', function (Blueprint $table): void {
            $table->dropColumn(['job_type', 'description', 'is_open']);
        });
    }
};
