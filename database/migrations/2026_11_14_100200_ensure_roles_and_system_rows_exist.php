<?php

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The system cannot work without its five roles (Admin, HR, Employee,
 * Accounting, Applicant) and the data-version counter row. On a database
 * built from scratch these were missing, so registration and every save
 * failed. Creating them here makes a fresh install usable right away; on a
 * database that already has them nothing changes.
 *
 * NOTE: this migration is deliberately left out of the list of already-run
 * migrations in database/schema/mysql-schema.sql, because that file holds
 * table structure only. If the schema file is regenerated with
 * `php artisan schema:dump`, remove this migration's line from it again.
 */
return new class extends Migration
{
    public function up(): void
    {
        (new RolePermissionSeeder)->run();

        DB::table('system_data_versions')->insertOrIgnore([
            'id' => 1,
            'version' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Roles and the counter row are required data; they are kept.
    }
};
