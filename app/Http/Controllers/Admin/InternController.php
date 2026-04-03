<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Group;
use App\Models\Intern;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class InternController extends Controller
{
    public function __construct(protected CertificateService $certificateService)
    {
    }

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

        $query = Intern::with(['user', 'group'])
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

        $inactiveInterns = Intern::with(['user', 'group'])
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

        return view('admin.interns.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',

            'filiere'       => 'required|string|max:255',
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

        $group = Group::forFiliere($data['filiere']);
        $this->ensureGroupCapacity($group);

            $intern = new Intern();
            $intern->user_id = $user->id;

            $intern->group_id = $group->id;
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
            'attendances' => fn ($q) => $q->orderByDesc('date'),
            'request',
            'certificate',
        ]);

        return view('admin.interns.show', compact('intern'));
    }

    public function edit(Intern $intern)
    {
        $departments = Department::all();

        return view('admin.interns.edit', compact('intern', 'departments'));
    }

    public function update(Request $request, Intern $intern)
    {
        $data = $request->validate([

            'filiere'       => 'required|string|max:255',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'active'        => 'nullable|boolean',
            'admin_note'    => 'nullable|string|max:2000',
        ]);

        $targetGroup = Group::forFiliere($data['filiere']);
        $this->ensureGroupCapacity($targetGroup, $intern->id);
        $data['group_id'] = $targetGroup->id;

        $intern->fill($data);

        if ($intern->start_date && $intern->end_date) {
            $intern->duration_days = $intern->start_date->diffInDays($intern->end_date) + 1;
        }

        $intern->save();

        if ($intern->request) {
            $intern->request->update([
                'period_start' => $intern->start_date,
                'period_end'   => $intern->end_date,
                'filiere'      => $targetGroup->filiere,
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

        $formerInterns = Intern::with(['user', 'group'])
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

        $formerInternsQuery = Intern::with(['user', 'group'])
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
                        $intern->department ?? 'N/A',
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
        $finishedInterns = Intern::where('active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->toDateString())
            ->get();

        foreach ($finishedInterns as $intern) {
            $intern->update(['active' => false]);
            $this->certificateService->generateForIntern($intern);
        }
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
