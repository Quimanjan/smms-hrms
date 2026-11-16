<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Keep the name parts typed at sign-up, so the application form can fill
 * First / Middle / Last name exactly (a two-word first name like "Paul Angelo"
 * no longer spills into the middle name).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 75)->nullable()->after('name');
            $table->string('middle_name', 75)->nullable()->after('first_name');
            $table->string('last_name', 75)->nullable()->after('middle_name');
            $table->string('suffix', 20)->nullable()->after('last_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name', 'suffix']);
        });
    }
};
