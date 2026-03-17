<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskUserStatus;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $intern = $request->user()->intern;
        $taskAssignments = collect();
        $statusMap = collect();

        if ($intern && $intern->group) {
            $taskAssignments = Task::with('group')->where('group_id', $intern->group_id)->get();
            $taskIds = $taskAssignments->pluck('id');
            $statusMap = TaskUserStatus::whereIn('task_id', $taskIds)
                ->where('user_id', $request->user()->id)
                ->pluck('status', 'task_id');
        }

        return view('intern.tasks.index', compact('taskAssignments', 'statusMap'));
    }

    public function markCompleted(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:not_completed,completed',
        ]);

        $status = TaskUserStatus::updateOrCreate(
            ['task_id' => $task->id, 'user_id' => $request->user()->id],
            [
                'status' => $request->input('status'),
                'completed_at' => $request->input('status') === 'completed' ? now() : null,
            ]
        );

        $task->status = TaskUserStatus::where('task_id', $task->id)
            ->where('status', 'completed')
            ->exists() ? 'completed' : 'not_completed';
        $task->save();

        return back()->with('status', __('Task status updated.'));
    }
}
