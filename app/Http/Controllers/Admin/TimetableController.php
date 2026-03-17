<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index()
    {
        $timetables = Timetable::with([
            'group.department',
            'group.interns.user',
        ])->orderBy('group_id')->orderBy('day_of_week')->get();

        $groupsCount = Group::count();
        $slotCount = $timetables->count();

        return view('admin.timetables.index', compact('timetables', 'groupsCount', 'slotCount'));
    }

    public function create()
    {
        $groups = Group::all();

        return view('admin.timetables.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id'   => 'required|exists:groups,id',
            'day_of_week'=> 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after:start_time',
        ]);

        Timetable::create($data);

        return redirect()->route('admin.timetables.index')->with('status', __('Timetable entry created.'));
    }
}
