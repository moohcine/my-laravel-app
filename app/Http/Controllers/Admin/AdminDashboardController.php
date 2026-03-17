<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Intern;
use App\Models\InternshipRequest;
use App\Models\Timetable;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Mark finished interns as inactive automatically
        Intern::where('active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString())
            ->update(['active' => false]);

        $totalInterns = Intern::active()->count();
        $acceptedInterns = InternshipRequest::where('status', 'accepted')
            ->where(function ($q) {
                $q->whereNull('period_end')
                  ->orWhere('period_end', '>=', now()->toDateString());
            })
            ->count();
        $pendingRequests = InternshipRequest::where('status', 'pending')->count();
        $rejectedInterns = InternshipRequest::where('status', 'rejected')->count();
        $totalRequests = InternshipRequest::count();

        $internsPerDept = Department::withCount(['interns as interns_count' => function ($q) {
            $q->active();
        }])->get();

        $totalPresent = Attendance::where('status', 'present')->count();
        $totalAttendanceRecords = Attendance::count();
        $attendanceRate = $totalAttendanceRecords > 0
            ? round(($totalPresent / $totalAttendanceRecords) * 100, 1)
            : 0;
        $timetableSlots = Timetable::count();
        $formerInterns = Intern::whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString())
            ->count();

        $attendanceTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $targetDate = now()->subDays($daysAgo)->toDateString();
            $records = Attendance::whereDate('date', $targetDate);
            $total = $records->count();
            $present = $total ? $records->where('status', 'present')->count() : 0;

            return [
                'label' => now()->subDays($daysAgo)->format('D'),
                'rate' => $total ? round(($present / $total) * 100, 0) : 0,
            ];
        });

        $completionBase = $formerInterns + $totalInterns;
        $completionRate = $completionBase > 0
            ? round(($formerInterns / $completionBase) * 100, 1)
            : 0;
        $weeklyAttendanceAverage = $attendanceTrend->avg('rate');

        return view('admin.dashboard', compact(
            'totalInterns',
            'acceptedInterns',
            'pendingRequests',
            'rejectedInterns',
            'internsPerDept',
            'attendanceRate',
            'timetableSlots',
            'formerInterns',
            'attendanceTrend',
            'totalRequests',
            'completionRate',
            'weeklyAttendanceAverage'
        ));
    }
}
