<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::withCount('activeInterns')
            ->orderBy('filiere')
            ->paginate(10);

        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        abort(403, __('Groups are created automatically per filiere.'));
    }

    public function store(Request $request)
    {
        abort(403, __('Groups are created automatically per filiere.'));
    }

    public function show(Group $group)
    {
        $group->load([
            'timetables',
            'activeInterns.user',
        ]);
        $group->loadCount('activeInterns');

        $activeInterns = $group->activeInterns;

        return view('admin.groups.show', compact('group', 'activeInterns'));
    }

    public function edit(Group $group)
    {
        abort(403, __('Groups are created automatically per filiere.'));
    }

    public function update(Request $request, Group $group)
    {
        abort(403, __('Groups are created automatically per filiere.'));
    }

    public function destroy(Group $group)
    {
        abort(403, __('Groups are created automatically per filiere.'));
    }
}
