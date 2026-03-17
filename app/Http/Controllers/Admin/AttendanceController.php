<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Group;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $groupId = $request->query('group_id');
        $date = $request->query('date', now()->toDateString());

        $groups = Group::with(['interns' => function ($q) {
            $q->where(function ($q2) {
                $q2->whereNull('end_date')
                   ->orWhere('end_date', '>=', now()->toDateString());
            })->where('active', true)->with('user');
        }])->get();

        $attendance = Attendance::whereDate('date', $date)->get()->keyBy('intern_id');

        return view('admin.attendance.index', compact('groups', 'attendance', 'groupId', 'date'));
    }

    public function mark(Request $request)
    {
        $data = $request->validate([
            'intern_id' => 'required|integer',
            'date'      => 'required|date',
            'status'    => 'required|in:present,absent',
        ]);

        Attendance::updateOrCreate(
            [
                'intern_id' => $data['intern_id'],
                'date'      => $data['date'],
            ],
            [
                'status' => $data['status'],
            ]
        );

        return back()->with('status', __('Attendance updated.'));
    }
}
