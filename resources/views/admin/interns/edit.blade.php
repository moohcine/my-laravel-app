@extends('layouts.app')

@section('title', __('Edit intern – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Edit intern') }}</h3>
        <a href="{{ route('admin.interns.show', $intern) }}" class="small text-secondary text-decoration-none">
            <i class="bi bi-arrow-left"></i> {{ __('Back to profile') }}
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
        <form method="POST" action="{{ route('admin.interns.update', $intern) }}" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Department') }}</label>
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">—</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" @selected($intern->department_id == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Group') }}</label>
                <select name="group_id" class="form-select form-select-sm">
                    <option value="">—</option>
                    @foreach ($groups as $group)
                        @php
                            $count = $group->active_interns_count ?? 0;
                            $full = $group->max_interns > 0 && $count >= $group->max_interns;
                            $selectedId = old('group_id', $intern->group_id);
                            $selected = (int) $selectedId === $group->id;
                        @endphp
                        <option
                            value="{{ $group->id }}"
                            @selected($selected)
                            @if($full && !$selected) disabled @endif
                        >
                            {{ $group->name }}
                            ({{ __('group.capacity_status', ['count' => $count, 'capacity' => $group->max_interns]) }})
                            @if ($full)
                                – {{ __('group.full_label') }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <small class="text-secondary">{{ __('group.capacity_note') }}</small>
            </div>

            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Start date') }}</label>
                <input type="date" name="start_date" value="{{ old('start_date', optional($intern->start_date)->toDateString()) }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('End date') }}</label>
                <input type="date" name="end_date" value="{{ old('end_date', optional($intern->end_date)->toDateString()) }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-4 d-flex align-items-center mt-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="active" value="1" id="active" {{ $intern->active ? 'checked' : '' }}>
                    <label class="form-check-label small text-secondary" for="active">
                        {{ __('Active') }}
                    </label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label small text-secondary">{{ __('Admin note') }}</label>
                <textarea name="admin_note" rows="3" class="form-control form-control-sm">{{ old('admin_note', $intern->admin_note) }}</textarea>
            </div>

            <div class="col-12 d-flex justify-content-between mt-3">
                <button type="submit" class="btn ndc-btn ndc-btn-primary btn-sm">
                    {{ __('Save changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
