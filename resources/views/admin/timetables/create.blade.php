@extends('layouts.app')

@section('title', __('Create timetable slot – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Create timetable slot') }}</h3>
        <a href="{{ route('admin.timetables.index') }}" class="small text-secondary text-decoration-none">
            <i class="bi bi-arrow-left"></i> {{ __('Back to timetables') }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ndc-card p-4">
        <form method="POST" action="{{ route('admin.timetables.store') }}" class="row g-3">
            @csrf

            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Group') }}</label>
                <select name="group_id" class="form-select form-select-sm" required>
                    <option value="">{{ __('Select group') }}</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected(old('group_id') == $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Day of week') }}</label>
                <select name="day_of_week" class="form-select form-select-sm" required>
                    @foreach (['monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                        <option value="{{ $day }}" @selected(old('day_of_week') == $day)>{{ __('days.' . $day) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary">{{ __('Start time') }}</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary">{{ __('End time') }}</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}" class="form-control form-control-sm">
            </div>

            <div class="col-12 d-flex justify-content-end mt-3">
                <button type="submit" class="btn ndc-btn ndc-btn-primary btn-sm">
                    {{ __('Save slot') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
