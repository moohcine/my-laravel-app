<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Intern;
use App\Models\InternshipRequest;

class HomeController extends Controller
{
    public function index()
    {
        $department = Department::where('code', 'NDC-PRO')
            ->orWhere('name', 'NDC PRO')
            ->with('groups')
            ->first();

        $activeDays = collect();

        if ($department) {
            $activeDays = $department->groups
                ->flatMap(function ($group) {
                    return $group->days_of_week ?? [];
                })
                ->unique()
                ->values();
        }

        $weekDays = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);

        // Show only active interns in the hero card.
        $totalInterns = Intern::where('active', true)->count();
        $totalPresent = Attendance::where('status', 'present')->count();
        $totalAttendanceRecords = Attendance::count();
        $attendanceRate = $totalAttendanceRecords ? round(($totalPresent / $totalAttendanceRecords) * 100, 1) : 0;

        return view('home', [
            'department' => $department,
            'weekDays'   => $weekDays,
            'activeDays' => $activeDays,
            'totalInterns' => $totalInterns,
            'attendanceRate' => $attendanceRate,
        ]);
    }
}
