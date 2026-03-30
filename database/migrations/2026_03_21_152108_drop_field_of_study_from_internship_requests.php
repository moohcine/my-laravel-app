<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('internship_requests', function (Blueprint $table) {
            if (Schema::hasColumn('internship_requests', 'field_of_study')) {
                $table->dropColumn('field_of_study');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internship_requests', function (Blueprint $table) {
            $table->string('field_of_study')->after('school');
        });
    }
};
