@extends('layouts.app')

@section('title', __('Group details – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Group details') }}</h3>
        <a href="{{ route('admin.groups.index') }}" class="small text-secondary text-decoration-none">
            <i class="bi bi-arrow-left"></i> {{ __('Back to groups') }}
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="ndc-card p-4 h-100">
                <h5 class="text-white mb-1">{{ $group->filiere }}</h5>
                <p class="text-secondary small mb-3">{{ __('Auto-generated group based on filière') }}</p>
                <p class="small text-secondary mb-1">
                    {{ __('Department:') }} <span class="fw-semibold text-white">{{ $group->department?->name ?? '—' }}</span>
                </p>
                <p class="small text-secondary mb-1">
                    {{ __('Max interns:') }} <span class="fw-semibold text-white">{{ $group->max_interns }}</span>
                </p>
                <p class="small text-secondary mb-1">
                    {{ __('Assigned:') }}
                    <span class="fw-semibold text-white">{{ $group->active_interns_count ?? $group->interns->count() }}</span>
                    / {{ $group->max_interns }}
                </p>
                <p class="small text-secondary mb-1">
                    {{ __('Days:') }}
                    <span class="fw-semibold text-white">
                        @if($group->days_of_week)
                            {{ implode(', ', $group->days_of_week) }}
                        @else
                            —
                        @endif
                    </span>
                </p>
                <p class="small text-secondary mb-0">
                    {{ __('Description:') }}<br>
                    <span class="fw-semibold text-white">{{ $group->description ?? '—' }}</span>
                </p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ndc-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="text-white mb-0">{{ __('Interns') }}</h5>
                </div>
                @if ($activeInterns->isEmpty())
                    <p class="small text-secondary mb-0">
                        {{ __('No interns assigned to this group yet.') }}
                    </p>
                @else
                    <ul class="list-unstyled small mb-0">
                        @foreach ($activeInterns as $intern)
                            <li class="mb-1">
                                {{ $intern->user->name }} <span class="text-secondary">({{ $intern->user->email }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="ndc-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-white mb-0">{{ __('Timetable slots') }}</h5>
                    <a href="{{ route('admin.timetables.index') }}" class="small text-info text-decoration-none">{{ __('Manage all') }}</a>
                </div>
                @php
                    $daysOrder = ['monday','tuesday','wednesday','thursday','friday','saturday'];
                    $slotsByDay = $group->timetables->groupBy('day_of_week');
                @endphp
                <div class="table-responsive">
                    <table class="table table-sm table-dark-modern align-middle mb-0">
                        <thead>
                        <tr class="small text-secondary">
                            <th>{{ __('Day') }}</th>
                            <th>{{ __('Start') }}</th>
                            <th>{{ __('End') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($daysOrder as $day)
                            @forelse($slotsByDay->get($day, collect()) as $slot)
                                    <tr>
                                        <td class="text-capitalize small">{{ __('days.' . $day) }}</td>
                                    <td class="small text-secondary">{{ $slot->start_time ?? '—' }}</td>
                                    <td class="small text-secondary">{{ $slot->end_time ?? '—' }}</td>
                                </tr>
                            @empty
                            @endforelse
                        @empty
                            <tr>
                                <td colspan="3" class="text-center small text-secondary py-3">{{ __('No timetable slots yet.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ndc-card p-4 h-100">
                <h5 class="text-white mb-3">{{ __('Add a slot') }}</h5>
                <form method="POST" action="{{ route('admin.timetables.store') }}" class="row g-3">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <div class="col-md-6">
                        <label class="form-label small text-secondary">{{ __('Day of week') }}</label>
                        <select name="day_of_week" class="form-select form-select-sm" required>
                        @foreach (['monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}">{{ __('days.' . $day) }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-secondary">{{ __('Start') }}</label>
                        <input type="time" name="start_time" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-secondary">{{ __('End') }}</label>
                        <input type="time" name="end_time" class="form-control form-control-sm">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn ndc-btn ndc-btn-primary btn-sm" type="submit">{{ __('Add slot') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
