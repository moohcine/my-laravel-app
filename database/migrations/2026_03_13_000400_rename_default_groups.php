<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('groups')
            ->where('name', 'NDC PRO – Web Interns')
            ->update(['name' => 'Group of Programmers']);

        DB::table('groups')
            ->where('name', 'NDC PRO – Data Interns')
            ->update(['name' => 'Group of Réseaux Cloud']);
    }

    public function down(): void
    {
        DB::table('groups')
            ->where('name', 'Group of Programmers')
            ->update(['name' => 'NDC PRO – Web Interns']);

        DB::table('groups')
            ->where('name', 'Group of Réseaux Cloud')
            ->update(['name' => 'NDC PRO – Data Interns']);
    }
};
