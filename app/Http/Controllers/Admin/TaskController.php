<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    public function index()
    {
        $data = $this->getTaskContext();

        return view('admin.tasks.index', $data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
        ]);

        Task::create($data);

        if ($request->wantsJson()) {
            return $this->jsonTaskResponse(__('Task created.'));
        }

        return redirect()->route('admin.tasks.index')->with('status', __('Task created.'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
        ]);

        $task->update($data);

        if ($request->wantsJson()) {
            return $this->jsonTaskResponse(__('Task updated.'));
        }

        return redirect()->route('admin.tasks.index')->with('status', __('Task updated.'));
    }

    public function destroy(Request $request, Task $task)
    {
        $task->delete();

        if ($request->wantsJson()) {
            return $this->jsonTaskResponse(__('Task deleted.'));
        }

        return redirect()->route('admin.tasks.index')->with('status', __('Task deleted.'));
    }

    public function statusUpdates()
    {
        $tasks = Task::with('statuses.user')->get();

        return response()->json($tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'group_id' => $task->group_id,
                'statuses' => $task->statuses->map(fn ($status) => [
                    'user' => $status->user->name,
                    'status' => $status->status,
                ]),
            ];
        }));
    }

    private function getTaskContext(): array
    {
        $groups = Group::with([
            'tasks.statuses.user',
            'interns.user',
            'activeInterns',
        ])->withCount(['activeInterns as active_interns_count'])->get();

        $statistics = [
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'pending_tasks' => Task::where('status', 'not_completed')->count(),
            'groups_managed' => $groups->count(),
            'due_today' => Task::whereDate('due_at', Carbon::today())->count(),
        ];

        return compact('groups', 'statistics');
    }

    private function jsonTaskResponse(string $message)
    {
        $data = $this->getTaskContext();
        return response()->json([
            'success' => true,
            'message' => $message,
            'streamHtml' => view('admin.tasks.partials.stream', $data)->render(),
            'statsHtml' => view('admin.tasks.partials.stats', $data)->render(),
        ]);
    }
}
