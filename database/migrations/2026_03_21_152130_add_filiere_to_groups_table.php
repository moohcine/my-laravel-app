<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('filiere')->nullable()->after('name');
        });

        DB::table('groups')->whereNull('filiere')->update([
            'filiere' => DB::raw('name'),
        ]);

        Schema::table('groups', function (Blueprint $table) {
            $table->unique('filiere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropUnique(['filiere']);
            $table->dropColumn('filiere');
        });
    }
};
