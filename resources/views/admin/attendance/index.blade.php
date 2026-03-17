@extends('layouts.app')

@section('title', __('Attendance – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-secondary small mb-1">{{ __('Attendance') }}</p>
            <h3 class="fw-bold text-white mb-0">{{ __('Daily presence board') }}</h3>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm ndc-btn-outline">
            <i class="bi bi-arrow-left"></i> {{ __('Dashboard') }}
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">
            {{ session('status') }}
        </div>
    @endif

    <div class="ndc-card p-4 mb-4 attendance-filter">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small text-secondary">{{ __('Date') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-date"></i></span>
                    <input type="date" name="date" value="{{ $date }}" class="form-control border-start-0 rounded-end">
                </div>
            </div>
            <div class="col-md-5">
                <label class="form-label small text-secondary">{{ __('Group') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-people"></i></span>
                    <select name="group_id" class="form-select border-start-0 rounded-end">
                        <option value="">{{ __('All groups') }}</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" @selected($groupId == $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn ndc-btn ndc-btn-primary w-100 text-uppercase small">
                    <i class="bi bi-funnel-fill me-1"></i> {{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>

    @foreach ($groups as $group)
        @if ($groupId && $group->id != $groupId)
            @continue
        @endif
        <div class="mb-4">
            <div class="group-header py-3 px-3 bg-white rounded-top d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-dark">{{ $group->name }}</h5>
                    <p class="mb-0 text-muted small">{{ __('Working days:') }} {{ implode(', ', $group->days_of_week ?? []) }}</p>
                </div>
                <span class="badge bg-light text-secondary">{{ __(':count interns', ['count' => $group->interns->count()]) }}</span>
            </div>
            <div class="ndc-card p-0 rounded-top-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 attendance-table">
                        <thead>
                        <tr class="small text-secondary text-uppercase">
                            <th>{{ __('Intern') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th class="text-end">{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($group->interns as $intern)
                            @php $record = $attendance->get($intern->id); @endphp
                            <tr>
                                <td class="fw-semibold">{{ $intern->user->name }}</td>
                                <td class="small text-secondary">{{ $intern->user->email }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.attendance.mark') }}" class="d-inline-flex">
                                        @csrf
                                        <input type="hidden" name="intern_id" value="{{ $intern->id }}">
                                        <input type="hidden" name="date" value="{{ $date }}">
                                        <div class="btn-group btn-group-sm attendance-toggle" role="group">
                                            <button name="status" value="present" class="btn {{ $record && $record->status === 'present' ? 'btn-present' : 'btn-outline-present' }}">
                                                {{ __('Present') }}
                                            </button>
                                            <button name="status" value="absent" class="btn {{ $record && $record->status === 'absent' ? 'btn-absent' : 'btn-outline-absent' }}">
                                                {{ __('Absent') }}
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center small text-secondary py-3">
                                    {{ __('No interns in this group.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('styles')
<style>
    .attendance-filter {
        background: linear-gradient(135deg, rgba(59,130,246,0.12), rgba(59,130,246,0));
        border: 1px solid rgba(59,130,246,0.25);
    }
    .group-header {
        border: 1px solid rgba(15,23,42,0.08);
        border-bottom: none;
    }
    .ndc-card.attendance-table + .table-responsive table,
    .attendance-table {
        border-spacing: 0;
    }
    .attendance-table thead {
        background: #f7fafc;
    }
    .attendance-table th, .attendance-table td {
        padding: 1rem;
    }
    .attendance-toggle .btn {
        min-width: 92px;
        border-radius: 999px;
    }
    .btn-present, .btn-outline-present, .btn-absent, .btn-outline-absent {
        border: none;
    }
    .btn-present {
        background: #22c55e;
        color: #fff;
    }
    .btn-outline-present {
        border: 1px solid #22c55e;
        color: #15803d;
        background: transparent;
    }
    .btn-absent {
        background: #ef4444;
        color: #fff;
    }
    .btn-outline-absent {
        border: 1px solid #ef4444;
        color: #b91c1c;
        background: transparent;
    }
</style>
@endpush
