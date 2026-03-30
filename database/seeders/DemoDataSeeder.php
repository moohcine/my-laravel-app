<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Intern;
use App\Models\InternshipRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $groups = Group::with('department')->get();

        if ($groups->isEmpty()) {
            $groups = collect([
                Group::forFiliere('Software Engineering'),
                Group::forFiliere('Network'),
            ]);
        }

        // Generate a handful of demo interns + requests
        foreach (range(1, 6) as $i) {
            $group = $groups[$i % $groups->count()];

            $user = User::firstOrCreate(
                ['email' => "intern{$i}@ndc-pro.local"],
                [
                    'name'     => "Intern {$i}",
                    'password' => Hash::make('password'),
                    'role'     => 'intern',
                ]
            );

            $request = InternshipRequest::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone'          => '0600' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                    'school'         => 'Tech University',
                    'filiere'        => 'Software Engineering',
                    'period_start'   => now()->addDays($i)->toDateString(),
                    'period_end'     => now()->addDays($i + 60)->toDateString(),
                    'cv_path'        => 'cvs/demo.pdf',
                    'status'         => 'accepted',
                    'reviewed_by'    => 1,
                    'reviewed_at'    => now(),
                ]
            );

            Intern::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'internship_request_id' => $request->id,
                    'department_id'         => $group->department_id,
                    'group_id'              => $group->id,
                    'start_date'            => $request->period_start,
                    'end_date'              => $request->period_end,
                    'duration_days'         => $request->period_start && $request->period_end
                        ? $request->period_start->diffInDays($request->period_end) + 1
                        : null,
                    'active'                => true,
                ]
            );
        }
    }
}
