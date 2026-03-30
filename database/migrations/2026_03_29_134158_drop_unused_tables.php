<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('field_jobs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // These tables are not used by the project, no need to recreate them.
    }
};
