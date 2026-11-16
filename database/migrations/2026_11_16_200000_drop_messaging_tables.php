<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/** Client revision: the system has no messaging, so the chat and its saved messages are removed. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
    }

    public function down(): void
    {
        // Messaging was removed on purpose; nothing to restore.
    }
};
