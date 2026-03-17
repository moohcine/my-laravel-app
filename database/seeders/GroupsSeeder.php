<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupsSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::where('code', 'NDC-PRO')->first();

        if (! $department) {
            return;
        }

        $groups = [
            [
                'name'          => 'Group of Programmers',
                'max_interns'   => 12,
                'days_of_week'  => ['monday', 'wednesday', 'friday'],
                'color'         => '#22d3ee',
                'description'   => 'Programmers track handling full-stack projects.',
            ],
            [
                'name'          => 'Group of Réseaux Cloud',
                'max_interns'   => 8,
                'days_of_week'  => ['tuesday', 'thursday'],
                'color'         => '#22c55e',
                'description'   => 'Cloud and networking internships.',
            ],
            [
                'name'          => 'Group of RH',
                'max_interns'   => 6,
                'days_of_week'  => ['monday', 'tuesday', 'thursday'],
                'color'         => '#fb923c',
                'description'   => 'Human resources support interns.',
            ],
        ];

        foreach ($groups as $group) {
            Group::firstOrCreate(
                ['name' => $group['name']],
                array_merge($group, ['department_id' => $department->id])
            );
        }
    }
}
