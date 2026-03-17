<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        // Single department tied to NDC PRO only
        \App\Models\Department::where('code', '!=', 'NDC-PRO')->delete();
        $departments = [
            ['name' => 'NDC PRO', 'code' => 'NDC-PRO'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
