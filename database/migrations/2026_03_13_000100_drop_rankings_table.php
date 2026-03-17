<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('rankings');
    }

    public function down(): void
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('attendance_score')->default(0);
            $table->unsignedInteger('activity_score')->default(0);
            $table->unsignedInteger('admin_note_score')->default(0);
            $table->unsignedInteger('total_score')->default(0);
            $table->unsignedInteger('rank_position')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
        });
    }
};
