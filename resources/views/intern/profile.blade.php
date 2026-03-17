@extends('layouts.app')

@section('title', __('My Profile – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <p class="text-secondary small mb-1">{{ __('Intern profile') }}</p>
                    <h2 class="fw-bold text-white mb-0">{{ $user->name }}</h2>
                </div>
                <a href="{{ route('intern.dashboard') }}" class="btn btn-sm ndc-btn-outline">
                    {{ __('Back to dashboard') }}
                </a>
            </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ndc-card p-4 h-100">
                <h5 class="text-white mb-3">{{ __('Personal details') }}</h5>
                <p class="small mb-1 text-secondary">{{ __('Name:') }} <span class="fw-semibold text-white">{{ $user->name }}</span></p>
                <p class="small mb-1 text-secondary">{{ __('Email:') }} <span class="fw-semibold text-white">{{ $user->email }}</span></p>
                <p class="small mb-1 text-secondary">
                    {{ __('Application status:') }}
                    @php $status = $request?->status ?? 'pending'; @endphp
                    <span class="badge
                        @if($status==='accepted') bg-success-subtle text-success
                        @elseif($status==='rejected') bg-danger-subtle text-danger
                        @else bg-warning-subtle text-warning @endif">
                        {{ __('status.' . $status) }}
                    </span>
                </p>
                <p class="small mb-1 text-secondary">
                    {{ __('Internship period:') }}
                    <span class="fw-semibold text-white">
                        @if ($intern && $intern->start_date && $intern->end_date)
                            {{ $intern->start_date->format('d M Y') }} – {{ $intern->end_date->format('d M Y') }}
                        @elseif($request)
                            {{ $request->period_start?->format('d M Y') }} – {{ $request->period_end?->format('d M Y') }}
                        @else
                            —
                        @endif
                    </span>
                </p>
                <p class="small mb-1 text-secondary">
                    {{ __('Duration:') }}
                    <span class="fw-semibold text-white">
                        {{ $totalDays ?? '—' }}
                        @if($totalDays) {{ __('days') }} @endif
                    </span>
                </p>
                <p class="small mb-0 text-secondary">
                    {{ __('CV:') }}
                    @if ($request?->cv_path)
                        <a class="fw-semibold text-info text-decoration-none" target="_blank" href="{{ asset('storage/'.$request->cv_path) }}">{{ __('Download CV') }}</a>
                    @else
                        <span class="fw-semibold text-white">—</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="ndc-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-white mb-0">{{ __('Attendance') }}</h6>
                </div>
                <p class="small text-secondary mb-0">
                    {{ __('Present days:') }}
                    <span class="fw-semibold text-white">{{ $attendanceCount }}</span>
                </p>
                @if($totalDays)
                    @php $rate = round(($attendanceCount / $totalDays) * 100, 1); @endphp
                    <p class="small text-secondary mb-0 mt-2">
                        {{ __('Approx. attendance rate:') }}
                        <span class="fw-semibold text-info">{{ $rate }}%</span>
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-lg-7">
            <div class="ndc-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-white mb-0">{{ __('Group assignment') }}</h6>
                    @if($intern && $intern->group)
                        <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#group-members" aria-expanded="false" aria-controls="group-members">
                            {{ __('View members') }}
                        </button>
                    @endif
                </div>
                @if($intern && $intern->group)
                    <p class="small text-secondary mb-1">
                        {{ __('Group:') }} <span class="fw-semibold text-white">{{ $intern->group->name }}</span>
                    </p>
                    <p class="small text-secondary mb-1">
                        {{ __('Department:') }} <span class="fw-semibold text-white">{{ $intern->department?->name ?? '—' }}</span>
                    </p>
                    <p class="small text-secondary mb-0">
                        {{ __('Capacity note: Group size :count interns.', ['count' => $intern->group->max_interns]) }}
                    </p>
                    @php
                        $groupRoster = $groupMembers->prepend($intern);
                    @endphp
                    <div class="collapse mt-3" id="group-members">
                        <div class="card card-body p-3 bg-white text-dark shadow-sm">
                            <p class="small text-secondary mb-2">{{ __('Group members') }}</p>
                            <ul class="list-unstyled mb-0">
                                @foreach ($groupRoster as $member)
                                    <li class="small mb-1 d-flex justify-content-between">
                                        <span>{{ $member->user->name }}</span>
                                        <span class="text-muted">{{ $member->user->email }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <p class="small text-secondary mb-0">{{ __('You are not assigned to a group yet.') }}</p>
                @endif
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ndc-card p-4 h-100">
                <h6 class="text-white mb-3">{{ __('Notes from NDC PRO') }}</h6>
                @php
                    $adminNote = $intern?->admin_note ?? $request?->admin_notes;
                @endphp
                @if ($adminNote)
                    <p class="small text-secondary mb-0">{{ $adminNote }}</p>
                @else
                    <p class="small text-secondary mb-0">{{ __('No admin note recorded yet.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
