<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Intern;
use App\Models\InternshipRequest;
use App\Models\Timetable;
use App\Services\GroupCleanupService;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(GroupCleanupService $cleanup)
    {
        // Mark finished interns as inactive automatically
        $toDeactivate = Intern::where('active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString());

        $affectedGroupIds = $toDeactivate->pluck('group_id');
        $toDeactivate->update(['active' => false]);

        // After deactivation, clean tasks for any groups that are now empty.
        $cleanup->deleteTasksForGroups($affectedGroupIds);

        // Safety net: ensure no empty groups keep stale tasks.
        $cleanup->pruneEmptyGroupTasks();

        // Total interns (active + inactive)
        $totalInterns = Intern::count();
        $activeInterns = Intern::active()->count();
        $completedInternships = Intern::whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString())
            ->count();
        $acceptedInterns = InternshipRequest::where(function ($q) {
                $q->where('status', 'accepted')
                  ->orWhereHas('intern');
            })
            ->count();
        $pendingRequests = InternshipRequest::where('status', 'pending')->count();
        $rejectedInterns = InternshipRequest::where('status', 'rejected')->count();
        $totalRequests = InternshipRequest::count();

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
        $internsPerDept = collect(); // placeholder to satisfy legacy view references

        // Filière chart stats
        $filiereStats = InternshipRequest::selectRaw("
                COALESCE(NULLIF(filiere, ''), 'Unknown') as filiere,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
            ")
            ->groupBy('filiere')
            ->havingRaw("COALESCE(NULLIF(filiere, ''), 'Unknown') <> ?", ['full stack'])
            ->orderBy('filiere')
            ->get();

        return view('admin.dashboard', compact(
            'totalInterns',
            'activeInterns',
            'completedInternships',
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
            'weeklyAttendanceAverage',
            'filiereStats'
        ));
    }
}
