<?php

use App\Models\Banner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Replaces the two old built-in homepage banners with the school's new
 * announcement banners. Banners that an administrator uploaded are kept
 * and shown after the built-in ones.
 */
return new class extends Migration
{
    public function up(): void
    {
        $defaults = Banner::DEFAULTS['homepage'];

        DB::table('banners')->where('placement', 'homepage')->where('is_default', true)->delete();

        // Make room at the front for the built-in banners.
        DB::table('banners')->where('placement', 'homepage')->increment('sort_order', count($defaults));

        foreach ($defaults as $i => $default) {
            DB::table('banners')->insert([
                'placement' => 'homepage',
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
