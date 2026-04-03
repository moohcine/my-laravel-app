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
        Schema::table('interns', function (Blueprint $table) {
            if (Schema::hasColumn('interns', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
            $table->string('department')->default('NDC PRO')->after('internship_request_id');
        });

        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
            $table->string('department')->default('NDC PRO')->after('filiere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
