<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Group;
use App\Models\Intern;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class InternController extends Controller
{
    public function index(Request $request)
    {
        $this->expireFinishedInterns();
        $search = $request->query('search');
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');

        $allowedSorts = ['created_at', 'start_date', 'end_date', 'name'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = Intern::with(['user', 'group', 'department'])
            ->where(function ($q) {
                $q->where('active', true)
                  ->orWhereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            });

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sorting
        if ($sort === 'name') {
            $column = 'name';
            $query->orderBy(
                User::select($column)->whereColumn('users.id', 'interns.user_id'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $interns = $query->paginate(10)->withQueryString();

        $inactiveInterns = Intern::with(['user', 'group', 'department'])
            ->where(function ($q) {
                $q->where('active', false)
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('end_date')
                          ->where('end_date', '<', now()->toDateString());
                  });
            })
            ->orderByDesc('end_date')
            ->limit(10)
            ->get();

        return view('admin.interns.index', compact('interns', 'search', 'sort', 'direction', 'inactiveInterns'));
    }

    public function create()
    {
        $departments = Department::all();
        $groups = Group::withCount('activeInterns')->get();

        return view('admin.interns.create', compact('departments', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'group_id'      => 'nullable|exists:groups,id',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'active'        => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'intern',
            ]);

        $groupId = $data['group_id'] ?? null;
        $group = $groupId ? Group::find($groupId) : null;

        if ($group) {
            $this->ensureGroupCapacity($group);
            $data['department_id'] = $data['department_id'] ?? $group->department_id;
        }

            $intern = new Intern();
            $intern->user_id = $user->id;
            $intern->department_id = $data['department_id'] ?? null;
            $intern->group_id = $groupId;
            $intern->start_date = $data['start_date'] ?? null;
            $intern->end_date = $data['end_date'] ?? null;
            $intern->active = $data['active'] ?? true;

            if ($intern->start_date && $intern->end_date) {
                $intern->duration_days = $intern->start_date->diffInDays($intern->end_date) + 1;
            }

            $intern->save();

            return redirect()->route('admin.interns.show', $intern)->with('status', __('Intern created.'));
        });
    }

    public function show(Intern $intern)
    {
        $intern->load([
            'user',
            'group.timetables',
            'department',
            'attendances' => fn ($q) => $q->orderByDesc('date'),
            'request',
        ]);

        return view('admin.interns.show', compact('intern'));
    }

    public function edit(Intern $intern)
    {
        $departments = Department::all();
        $groups = Group::withCount('activeInterns')->get();

        return view('admin.interns.edit', compact('intern', 'departments', 'groups'));
    }

    public function update(Request $request, Intern $intern)
    {
        $data = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'group_id'      => 'nullable|exists:groups,id',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'active'        => 'nullable|boolean',
            'admin_note'    => 'nullable|string|max:2000',
        ]);

        // Enforce group capacity limits
        $targetGroupId = $data['group_id'] ?? $intern->group_id;
        $targetGroup = $targetGroupId ? Group::find($targetGroupId) : null;

        if ($targetGroup) {
            $this->ensureGroupCapacity($targetGroup, $intern->id);
            $data['department_id'] = $data['department_id'] ?? $targetGroup->department_id;
        }

        $intern->fill($data);

        if ($intern->start_date && $intern->end_date) {
            $intern->duration_days = $intern->start_date->diffInDays($intern->end_date) + 1;
        }

        $intern->save();

        if ($intern->request) {
            $intern->request->update([
                'period_start' => $intern->start_date,
                'period_end'   => $intern->end_date,
            ]);
        }

        return redirect()->route('admin.interns.show', $intern)->with('status', __('Intern updated.'));
    }

    public function destroy(Intern $intern)
    {
        $intern->delete();

        return redirect()->route('admin.interns.index')->with('status', __('Intern deleted.'));
    }

    public function history(Request $request)
    {
        $this->expireFinishedInterns();
        $cohortStart = $request->query('cohort_start');
        $cohortEnd = $request->query('cohort_end');

        $formerInterns = Intern::with(['user', 'department', 'group'])
            ->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString())
            ->when($cohortStart, fn($q) => $q->whereDate('start_date', '>=', $cohortStart))
            ->when($cohortEnd, fn($q) => $q->whereDate('end_date', '<=', $cohortEnd))
            ->orderByDesc('end_date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.interns.history', compact('formerInterns', 'cohortStart', 'cohortEnd'));
    }

    public function exportHistory(Request $request)
    {
        $this->expireFinishedInterns();
        $cohortStart = $request->query('cohort_start');
        $cohortEnd = $request->query('cohort_end');

        $formerInternsQuery = Intern::with(['user', 'department', 'group'])
            ->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString())
            ->when($cohortStart, fn($q) => $q->whereDate('start_date', '>=', $cohortStart))
            ->when($cohortEnd, fn($q) => $q->whereDate('end_date', '<=', $cohortEnd))
            ->orderByDesc('end_date');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ndcpro-former-interns.csv"',
        ];

        $callback = function () use ($formerInternsQuery) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Department', 'Group', 'Start', 'End', 'Training notes']);

            $formerInternsQuery->chunk(100, function ($interns) use ($handle) {
                foreach ($interns as $intern) {
                    fputcsv($handle, [
                        $intern->user->name,
                        $intern->user->email,
                        $intern->department?->name ?? 'N/A',
                        $intern->group?->name ?? 'N/A',
                        $intern->start_date?->toDateString() ?? 'N/A',
                        $intern->end_date?->toDateString() ?? 'N/A',
                        $intern->admin_note ?? '—',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function expireFinishedInterns(): void
    {
        Intern::where('active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString())
            ->update(['active' => false]);
    }

    protected function ensureGroupCapacity(?Group $group, ?int $excludeInternId = null): void
    {
        if (!$group) {
            return;
        }

        $query = $group->activeInterns();
        if ($excludeInternId) {
            $query->where('id', '!=', $excludeInternId);
        }

        $currentCount = $query->count();
        if ($currentCount >= $group->max_interns) {
            throw ValidationException::withMessages([
                'group_id' => __('group.capacity_reached', ['group' => $group->name]),
            ]);
        }
    }
}
