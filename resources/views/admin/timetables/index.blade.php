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
                <p class="mb-0 text-white small">Click on any slot card to preview that group's members and department.</p>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="slot-grid">
        @forelse ($timetables as $slot)
            @php
                $startTime = $slot->start_time ? substr($slot->start_time, 0, 5) : null;
                $endTime = $slot->end_time ? substr($slot->end_time, 0, 5) : null;
                $memberCount = $slot->group->currentInterns->count();
            @endphp
            <div class="slot-column">
                <div class="slot-card slot-row"
                     role="button"
                     data-group-name="{{ $slot->group->name }}"
                     data-group-dept="{{ $slot->group->department?->name ?? '—' }}"
                     data-members='@json($slot->group->currentInterns->map(fn($member) => [
                         'name' => $member->user->name,
                         'email' => $member->user->email,
                     ]))'>
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ $slot->group->name }}</h6>
                            <div class="small text-muted">{{ $slot->group->department?->name ?? '—' }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2 slot-actions">
                            <span class="badge rounded-pill bg-light text-dark">{{ __('days.' . $slot->day_of_week) }}</span>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary slot-action-btn"
                                    data-slot-action
                                    data-bs-toggle="modal"
                                    data-bs-target="#slotEditModal"
                                    data-slot-id="{{ $slot->id }}"
                                    data-group-id="{{ $slot->group_id }}"
                                    data-day-of-week="{{ $slot->day_of_week }}"
                                    data-start-time="{{ $startTime }}"
                                    data-end-time="{{ $endTime }}"
                                    data-group-name="{{ $slot->group->name }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route('admin.timetables.destroy', $slot) }}"
                                  data-slot-action>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger slot-action-btn">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="time-block">
                            <p class="mb-1 text-muted small">{{ __('Start') }}</p>
                            <div class="time-pill">{{ $startTime ?? '—' }}</div>
                        </div>
                        <div class="time-block text-end">
                            <p class="mb-1 text-muted small">{{ __('End') }}</p>
                            <div class="time-pill">{{ $endTime ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">
                            <i class="bi bi-people me-1"></i>{{ __(':count interns', ['count' => $memberCount]) }}
                        </span>
                        <span class="small text-muted">
                            <i class="bi bi-eye me-1"></i>Preview
                        </span>
                    </div>
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

    <div class="modal fade" id="slotEditModal" tabindex="-1" aria-labelledby="slotEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
                <form id="slotEditForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h5 class="modal-title fw-bold" id="slotEditModalLabel">Edit slot</h5>
                            <p class="small text-muted mb-0" id="slotEditModalSub"></p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small text-secondary">{{ __('Group') }}</label>
                                <select name="group_id" id="slotEditGroup" class="form-select form-select-sm" required>
                                    <option value="">{{ __('Select group') }}</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-secondary">{{ __('Day of week') }}</label>
                                <select name="day_of_week" id="slotEditDay" class="form-select form-select-sm" required>
                                    @foreach (['monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                                        <option value="{{ $day }}">{{ __('days.' . $day) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-secondary">{{ __('Start time') }}</label>
                                <input type="time" name="start_time" id="slotEditStart" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-secondary">{{ __('End time') }}</label>
                                <input type="time" name="end_time" id="slotEditEnd" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="small text-muted mt-3">
                            Tip: click a slot card to preview members. Use the buttons to edit or delete.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-sm ndc-btn ndc-btn-outline" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-sm ndc-btn ndc-btn-primary">{{ __('Save changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .slot-row {
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }
    .slot-row:hover {
        transform: translateY(-2px);
    }
    .slot-row.active {
        border-color: rgba(37, 99, 235, 0.65);
        box-shadow: 0 16px 40px rgba(37,99,235,0.12);
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
        border: 1px solid rgba(15,23,42,0.12);
        border-radius: 1.25rem;
        padding: 1.25rem;
        background: #fff;
        color: #0f172a;
        box-shadow: 0 10px 26px rgba(15,23,42,0.08);
    }
    .slot-card .badge {
        font-size: 0.75rem;
    }
    .slot-actions .slot-action-btn {
        border-radius: 999px;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .time-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 92px;
        padding: 0.4rem 0.9rem;
        border-radius: 999px;
        background: rgba(59,130,246,0.08);
        border: 1px solid rgba(59,130,246,0.22);
        font-weight: 700;
        letter-spacing: 0.02em;
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
    const updateUrlTemplate = @json(route('admin.timetables.update', ['timetable' => '__ID__']));
    const preview = document.getElementById('group-preview');
    const nameEl = document.getElementById('preview-group-name');
    const deptEl = document.getElementById('preview-group-dept');
    const membersEl = document.getElementById('preview-members');

    function renderMembers(members) {
        membersEl.innerHTML = '';
        if (!members || members.length === 0) {
            membersEl.innerHTML = '<p class="small text-secondary mb-0">{{ __("No interns in this group") }}</p>';
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
        row.addEventListener('click', (event) => {
            if (event.target.closest('[data-slot-action]')) {
                return;
            }
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

    const editModal = document.getElementById('slotEditModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const slotId = trigger.dataset.slotId;
            const form = document.getElementById('slotEditForm');
            const groupSelect = document.getElementById('slotEditGroup');
            const daySelect = document.getElementById('slotEditDay');
            const startInput = document.getElementById('slotEditStart');
            const endInput = document.getElementById('slotEditEnd');
            const sub = document.getElementById('slotEditModalSub');

            if (!slotId || !form) return;

            form.action = updateUrlTemplate.replace('__ID__', slotId);
            if (groupSelect) groupSelect.value = trigger.dataset.groupId || '';
            if (daySelect) daySelect.value = trigger.dataset.dayOfWeek || 'monday';
            if (startInput) startInput.value = trigger.dataset.startTime || '';
            if (endInput) endInput.value = trigger.dataset.endTime || '';
            if (sub) {
                const groupName = trigger.dataset.groupName || '';
                sub.textContent = groupName ? `Slot #${slotId} • ${groupName}` : `Slot #${slotId}`;
            }
        });
    }
</script>
@endpush

@endsection
