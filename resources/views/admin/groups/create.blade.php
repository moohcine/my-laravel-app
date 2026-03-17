@extends('layouts.app')

@section('title', __('Create group – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Create group') }}</h3>
        <a href="{{ route('admin.groups.index') }}" class="small text-secondary text-decoration-none">
            <i class="bi bi-arrow-left"></i> {{ __('Back to groups') }}
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
        <form method="POST" action="{{ route('admin.groups.store') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Department') }}</label>
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">—</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" @selected(old('department_id') == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Max interns') }}</label>
                <input type="number" name="max_interns" value="{{ old('max_interns', 10) }}" min="1" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Color (optional)') }}</label>
                <input type="text" name="color" value="{{ old('color', '#22d3ee') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Days of week') }}</label>
                <select name="days_of_week[]" class="form-select form-select-sm" multiple>
                    @foreach (['monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                        <option value="{{ $day }}" @selected(collect(old('days_of_week'))->contains($day))>
                            {{ ucfirst($day) }}
                        </option>
                    @endforeach
                </select>
                <small class="text-secondary">{{ __('Hold Ctrl to select multiple.') }}</small>
            </div>

            <div class="col-12">
                <label class="form-label small text-secondary">{{ __('Description') }}</label>
                <textarea name="description" rows="3" class="form-control form-control-sm">{{ old('description') }}</textarea>
            </div>

            <div class="col-12 d-flex justify-content-end mt-3">
                        <button type="submit" class="btn ndc-btn ndc-btn-primary btn-sm">
                            {{ __('Create group') }}
                        </button>
            </div>
        </form>
    </div>
</div>
@endsection
