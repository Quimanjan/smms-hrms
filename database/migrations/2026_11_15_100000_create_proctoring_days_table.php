<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Exam proctoring days. They are working days (attendance and pay are
 * unchanged); they are only marked so the DTR can show them in purple.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proctoring_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proctoring_days');
    }
};
