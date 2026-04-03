@extends('layouts.app')

@section('title', __('Create intern – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Create intern') }}</h3>
        <a href="{{ route('admin.interns.index') }}" class="small text-secondary text-decoration-none">
            <i class="bi bi-arrow-left"></i> {{ __('Back to list') }}
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
        <form method="POST" action="{{ route('admin.interns.store') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Full name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Password') }}</label>
                <input type="password" name="password" class="form-control form-control-sm" required>
                <small class="text-secondary">{{ __('Min 8 characters.') }}</small>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Department') }}</label>
                <input type="text" class="form-control form-control-sm" value="NDC PRO" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('Filière (auto-group)') }}</label>
                <input type="text" name="filiere" value="{{ old('filiere') }}" class="form-control form-control-sm" required>
                <small class="text-secondary">{{ __('Interns sharing this filière are grouped together automatically.') }}</small>
            </div>

            <div class="col-md-6 col-lg-4">
                <label class="form-label small text-secondary">{{ __('Start date') }}</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label small text-secondary">{{ __('End date') }}</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control form-control-sm">
            </div>

            <div class="col-12 d-flex justify-content-end mt-3">
                <button type="submit" class="btn ndc-btn ndc-btn-primary btn-sm">
                    {{ __('Create intern') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
