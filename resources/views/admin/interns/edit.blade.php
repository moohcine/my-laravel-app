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
                <input type="text" class="form-control form-control-sm" value="NDC PRO" readonly>
                <input type="hidden" name="department_id" value="{{ $intern->department_id }}">
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Filière (auto-group)') }}</label>
                <input type="text" name="filiere" value="{{ old('filiere', $intern->group?->filiere ?? $intern->request?->filiere) }}" class="form-control form-control-sm" required readonly>
            </div>

            <div class="col-md-6 col-lg-4">
                <label class="form-label small text-secondary">{{ __('Start date') }}</label>
                <input type="date" name="start_date" value="{{ old('start_date', optional($intern->start_date)->toDateString()) }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label small text-secondary">{{ __('End date') }}</label>
                <input type="date" name="end_date" value="{{ old('end_date', optional($intern->end_date)->toDateString()) }}" class="form-control form-control-sm">
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
