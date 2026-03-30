@extends('layouts.app')

@section('title', __('Request details – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Internship request') }}</h3>
        <a href="{{ route('admin.requests.index') }}" class="small text-secondary text-decoration-none">
            <i class="bi bi-arrow-left"></i> {{ __('Back to list') }}
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ndc-card p-4 h-100">
                <h5 class="text-white mb-3">{{ __('Intern profile') }}</h5>
                <div class="mb-2 small">
                    <span class="text-secondary">{{ __('Name:') }}</span>
                    <span class="fw-semibold text-white">{{ $request->user->name }}</span>
                </div>
                <div class="mb-2 small">
                    <span class="text-secondary">{{ __('Email:') }}</span>
                    <span class="fw-semibold text-white">{{ $request->user->email }}</span>
                </div>
                <div class="mb-2 small">
                    <span class="text-secondary">{{ __('Phone:') }}</span>
                    <span class="fw-semibold text-white">{{ $request->phone }}</span>
                </div>
                <div class="mb-2 small">
                    <span class="text-secondary">{{ __('School:') }}</span>
                    <span class="fw-semibold text-white">{{ $request->school }}</span>
                </div>
                <div class="mb-2 small">
                    <span class="text-secondary">{{ __('Filière:') }}</span>
                    <span class="fw-semibold text-white">{{ $request->filiere ?? '—' }}</span>
                </div>
                <div class="mb-2 small">
                    <span class="text-secondary">{{ __('Period:') }}</span>
                    <span class="fw-semibold text-white">
                        {{ $request->period_start?->format('d M Y') }} – {{ $request->period_end?->format('d M Y') }}
                    </span>
                </div>
                <div class="mb-2 small">
                    <span class="text-secondary">{{ __('Status:') }}</span>
                    <span class="badge bg-opacity-25 border small
                        @if($request->status === 'pending') bg-warning text-warning border-warning
                        @elseif($request->status === 'accepted') bg-success text-success border-success
                        @else bg-danger text-danger border-danger @endif">
                        {{ __('status.' . $request->status) }}
                    </span>
                </div>
                <div class="mt-3 small">
                    <span class="text-secondary">{{ __('CV:') }}</span>
                    <a href="{{ asset('storage/'.$request->cv_path) }}" target="_blank" class="text-info text-decoration-none">
                        <i class="bi bi-file-earmark-pdf me-1"></i> {{ __('Download CV') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ndc-card p-4 h-100">
                <h5 class="text-white mb-3">{{ __('Review decision') }}</h5>
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

                <form method="POST" action="{{ route('admin.requests.accept', $request) }}" class="mb-3">
                    @csrf
                    <button class="btn btn-sm ndc-btn ndc-btn-primary w-100" {{ $request->status === 'accepted' ? 'disabled' : '' }}>
                        <i class="bi bi-check2-circle me-1"></i> {{ __('Accept request') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.requests.reject', $request) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small text-secondary">{{ __('Admin notes (optional)') }}</label>
                        <textarea name="admin_notes" rows="3" class="form-control form-control-sm">{{ old('admin_notes', $request->admin_notes) }}</textarea>
                    </div>
                        <button class="btn btn-sm btn-outline-danger w-100" {{ $request->status === 'rejected' ? 'disabled' : '' }}>
                            <i class="bi bi-x-circle me-1"></i> {{ __('Reject request') }}
                        </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
