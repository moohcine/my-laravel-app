<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use Illuminate\Support\Facades\Auth;

class InternDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $request = $user->internshipRequests()->latest()->first();
        $intern = Intern::where('user_id', $user->id)
            ->with(['group.timetables', 'group.currentInterns.user', 'attendances'])
            ->first();

        $isActive = false;
        if ($intern) {
            $isActive = $intern->active && (! $intern->end_date || $intern->end_date->isFuture());
        }

        $attendanceCount = $intern?->attendances()->where('status', 'present')->count() ?? 0;
        $totalAttendanceRecords = $intern?->attendances()->count() ?? 0;
        $totalDays = $intern?->duration_days ?? null;
        $timetable = $intern?->group?->timetables?->groupBy('day_of_week') ?? collect();
        $groupMembers = $intern?->group?->currentInterns?->where('id', '!=', $intern->id) ?? collect();
        $certificate = $intern?->certificate;

        return view('intern.dashboard', [
            'user'            => $user,
            'request'         => $request,
            'intern'          => $intern,
            'isActive'        => $isActive,
            'attendanceCount' => $attendanceCount,
            'totalAttendanceRecords' => $totalAttendanceRecords,
            'totalDays'       => $totalDays,
            'timetable'       => $timetable,
            'groupMembers'    => $groupMembers,
            'certificate'     => $certificate,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $request = $user->internshipRequests()->latest()->first();
        $intern = Intern::where('user_id', $user->id)
            ->with(['group', 'attendances'])
            ->first();

        $attendanceCount = $intern?->attendances()->where('status', 'present')->count() ?? 0;
        $totalAttendanceRecords = $intern?->attendances()->count() ?? 0;
        $totalDays = $intern?->duration_days ?? null;
        $groupMembers = $intern?->group?->interns?->where('id', '!=', $intern->id) ?? collect();

        return view('intern.profile', [
            'user'            => $user,
            'request'         => $request,
            'intern'          => $intern,
            'attendanceCount' => $attendanceCount,
            'totalAttendanceRecords' => $totalAttendanceRecords,
            'totalDays'       => $totalDays,
            'groupMembers'    => $groupMembers,
        ]);
    }
}
