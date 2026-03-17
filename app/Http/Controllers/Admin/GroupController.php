<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('department')->withCount('activeInterns')->paginate(10);

        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        $departments = Department::all();

        return view('admin.groups.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'max_interns'   => 'required|integer|min:1',
            'days_of_week'  => 'nullable|array',
            'days_of_week.*'=> 'in:monday,tuesday,wednesday,thursday,friday,saturday',
            'color'         => 'nullable|string|max:20',
            'description'   => 'nullable|string',
        ]);

        Group::create($data);

        return redirect()->route('admin.groups.index')->with('status', __('Group created.'));
    }

    public function show(Group $group)
    {
        $group->load([
            'department',
            'timetables',
            'activeInterns.user',
        ]);
        $group->loadCount('activeInterns');

        $activeInterns = $group->activeInterns;

        return view('admin.groups.show', compact('group', 'activeInterns'));
    }

    public function edit(Group $group)
    {
        $departments = Department::all();

        return view('admin.groups.edit', compact('group', 'departments'));
    }

    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'max_interns'   => 'required|integer|min:1',
            'days_of_week'  => 'nullable|array',
            'days_of_week.*'=> 'in:monday,tuesday,wednesday,thursday,friday,saturday',
            'color'         => 'nullable|string|max:20',
            'description'   => 'nullable|string',
        ]);

        $group->update($data);

        return redirect()->route('admin.groups.index')->with('status', __('Group updated.'));
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('admin.groups.index')->with('status', __('Group deleted.'));
    }
}
