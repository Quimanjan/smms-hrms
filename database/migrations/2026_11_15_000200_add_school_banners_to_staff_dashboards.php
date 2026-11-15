<?php

use App\Models\Banner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds the school's eight banners to the built-in banners shown on the
 * staff dashboards (Admin, HR, Accounting, Employee). Banners that an
 * administrator uploaded are kept and shown after the built-in ones.
 */
return new class extends Migration
{
    public function up(): void
    {
        $defaults = Banner::DEFAULTS['admin'];

        DB::table('banners')->where('placement', 'admin')->where('is_default', true)->delete();
        DB::table('banners')->where('placement', 'admin')->increment('sort_order', count($defaults));

        foreach ($defaults as $i => $default) {
            DB::table('banners')->insert([
                'placement' => 'admin',
                'path' => $default['path'],
                'alt' => $default['alt'],
                'is_default' => true,
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // The previous built-in banners are not restored automatically.
    }
};
