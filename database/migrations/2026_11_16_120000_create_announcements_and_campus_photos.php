<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Homepage content HR and Admin can change (panel revision, Nov 16 2026):
 * text announcements and the campus photos.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title', 160);
                $table->text('body');
                $table->date('posted_on');
                $table->boolean('is_published')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('campus_photos')) {
            Schema::create('campus_photos', function (Blueprint $table) {
                $table->id();
                $table->string('path');               // shown in the grid
                $table->string('full_path')->nullable(); // larger image when clicked
                $table->string('caption', 200);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });

            // Start with the three photos already on the homepage.
            $now = now();
            foreach ([
                1 => 'Classroom buildings around the school court',
                2 => 'The main school building',
                3 => 'A classroom building seen through the trees',
            ] as $n => $caption) {
                DB::table('campus_photos')->insert([
                    'path' => "image/campus-$n.webp",
                    'full_path' => "image/facility$n.jpg",
                    'caption' => $caption,
                    'sort_order' => $n,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_photos');
        Schema::dropIfExists('announcements');
    }
};
