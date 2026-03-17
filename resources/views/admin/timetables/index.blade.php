@extends('layouts.app')

@section('title', __('Timetables – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-secondary small mb-1">{{ __('Schedule') }}</p>
            <h3 class="fw-bold text-white mb-0">{{ __('Group timetables') }}</h3>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-secondary small text-decoration-none d-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('admin.timetables.create') }}" class="btn btn-sm ndc-btn ndc-btn-primary">
                <i class="bi bi-plus-lg me-1"></i> {{ __('Add slot') }}
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ndc-card p-3 text-center bg-white border">
                <small class="text-secondary text-uppercase">Groups</small>
                <h2 class="fw-bold mb-0">{{ $groupsCount }}</h2>
                <p class="mb-0 text-muted small">Active schedules</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ndc-card p-3 text-center bg-white border">
                <small class="text-secondary text-uppercase">Slots</small>
                <h2 class="fw-bold mb-0">{{ $slotCount }}</h2>
                <p class="mb-0 text-muted small">Total timetable entries</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ndc-card p-3 text-center bg-gradient">
                <small class="text-secondary text-uppercase">Hint</small>
                <p class="mb-0 text-white small">Click on any slot row to preview that group’s members and department.</p>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">
            {{ session('status') }}
        </div>
    @endif

    <div class="slot-grid">
        @forelse ($timetables as $slot)
            <div class="slot-column">
                <div class="slot-card slot-row"
                     role="button"
                     data-group-name="{{ $slot->group->name }}"
                     data-group-dept="{{ $slot->group->department?->name ?? '—' }}"
                     data-members='@json($slot->group->interns->map(fn($member) => [
                         'name' => $member->user->name,
                         'email' => $member->user->email,
                     ]))'>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ $slot->group->name }}</h6>
                        <span class="badge rounded-pill bg-light text-dark">{{ __('days.' . $slot->day_of_week) }}</span>
                    </div>
                    <p class="small text-secondary mb-1">{{ __('Day') }}: {{ ucfirst($slot->day_of_week) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted small">{{ __('Start') }}</p>
                            <strong>{{ $slot->start_time ?? '—' }}</strong>
                        </div>
                        <div>
                            <p class="mb-1 text-muted small">{{ __('End') }}</p>
                            <strong>{{ $slot->end_time ?? '—' }}</strong>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Click to preview group members</p>
                </div>
            </div>
        @empty
            <div class="slot-column w-100">
                <div class="ndc-card p-4 text-center text-secondary small">
                    {{ __('No timetable slots defined.') }}
                </div>
            </div>
        @endforelse
    </div>

    <div class="ndc-card p-4 mt-4" id="group-preview" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-secondary small mb-1">{{ __('Selected group') }}</p>
                <h5 class="fw-bold mb-0" id="preview-group-name"></h5>
            </div>
            <small class="text-muted" id="preview-group-dept"></small>
        </div>
        <div id="preview-members" class="list-group list-group-flush"></div>
    </div>
    <div class="ndc-card p-4 mt-4" id="group-preview" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-secondary small mb-1">{{ __('Selected group') }}</p>
                <h5 class="fw-bold mb-0" id="preview-group-name"></h5>
            </div>
            <small class="text-muted" id="preview-group-dept"></small>
        </div>
        <div id="preview-members" class="list-group list-group-flush"></div>
    </div>
</div>

@push('styles')
<style>
    .slot-row {
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .slot-row:hover {
        background: rgba(59,130,246,0.08);
    }
    .slot-row.active {
        background: rgba(59,130,246,0.12);
    }
    .ndc-card.bg-gradient {
        border: 1px solid rgba(59,130,246,0.4);
        background: #fff;
        color: #0f172a;
    }
    .ndc-card.bg-gradient p {
        color: #475569;
    }
    .slot-card {
        border: 1px solid rgba(59,130,246,0.5);
        border-radius: 1.25rem;
        padding: 1.25rem;
        background: #fff;
        color: #0f172a;
        box-shadow: 0 5px 12px rgba(15,23,42,0.1);
    }
    .slot-card .badge {
        font-size: 0.75rem;
    }
    .slot-card strong {
        font-size: 1.1rem;
        color: #0f172a;
    }
    .slot-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
    }
    .slot-column {
        display: flex;
        flex: 1 1 calc(50% - 0.625rem);
        min-width: 280px;
    }
    .slot-card {
        min-height: 200px;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .slot-grid .slot-card + .slot-card {
        margin-top: 0;
    }
    @media (max-width: 768px) {
        .slot-column {
            flex: 1 1 100%;
        }
        .slot-card {
            min-height: 220px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const preview = document.getElementById('group-preview');
    const nameEl = document.getElementById('preview-group-name');
    const deptEl = document.getElementById('preview-group-dept');
    const membersEl = document.getElementById('preview-members');

    function renderMembers(members) {
        membersEl.innerHTML = '';
        if (!members || members.length === 0) {
            membersEl.innerHTML = '<p class="small text-secondary mb-0">{{ __("No interns assigned to this group yet.") }}</p>';
            return;
        }
        members.forEach(member => {
            const item = document.createElement('div');
            item.className = 'list-group-item border-0 px-0 d-flex justify-content-between align-items-center';
            item.innerHTML = `<span>${member.name}</span><small class="text-muted">${member.email}</small>`;
            membersEl.appendChild(item);
        });
    }

    document.querySelectorAll('.slot-card').forEach(row => {
        row.addEventListener('click', () => {
            nameEl.textContent = row.dataset.groupName;
            deptEl.textContent = row.dataset.groupDept;
            const members = row.dataset.members ? JSON.parse(row.dataset.members) : [];
            renderMembers(members);
            preview.style.display = '';
            preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
            document.querySelectorAll('.slot-card').forEach(r => r.classList.remove('active'));
            row.classList.add('active');
        });
    });
</script>
@endpush

@endsection
