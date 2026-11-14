<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('first_name', 75)->nullable()->after('zkteco_user_id');
            $table->string('middle_name', 75)->nullable()->after('first_name');
            $table->string('last_name', 75)->nullable()->after('middle_name');
            $table->string('suffix', 20)->nullable()->after('last_name');

            $table->index(['last_name', 'first_name'], 'employees_last_first_name_index');
        });

        // Backfill existing rows by splitting the old `name` column.
        // The `name` column is kept (it is rebuilt from the parts on every save).
        DB::table('employees')
            ->select('id', 'name')
            ->whereNull('first_name')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('employees')
                        ->where('id', $row->id)
                        ->update($this->splitName((string) $row->name));
                }
            });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_last_first_name_index');
            $table->dropColumn(['first_name', 'middle_name', 'last_name', 'suffix']);
        });
    }

    /** "Juan Santos Dela Cruz Jr." -> first / middle / last / suffix */
    private function splitName(string $full): array
    {
        $parts = preg_split('/\s+/', trim($full), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $suffix = null;
        if (count($parts) > 1 && preg_match('/^(jr\.?|sr\.?|ii|iii|iv|v)$/i', rtrim(end($parts), ','))) {
            $suffix = rtrim(array_pop($parts), ',');
        }

        $first  = array_shift($parts);
        $last   = $parts ? array_pop($parts) : null;
        $middle = $parts ? implode(' ', $parts) : null;

        return [
            'first_name'  => $first,
            'middle_name' => $middle,
            'last_name'   => $last,
            'suffix'      => $suffix,
        ];
    }
};
