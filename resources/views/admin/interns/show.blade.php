@extends('layouts.app')

@section('title', __('Intern profile – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-secondary small mb-1">{{ __('Intern profile') }}</p>
            <h3 class="fw-bold text-white mb-0">{{ $intern->user->name }}</h3>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.interns.index') }}" class="btn btn-sm ndc-btn-outline">
                <i class="bi bi-arrow-left"></i> {{ __('Back') }}
            </a>
            @if($intern->active)
                <a href="{{ route('admin.interns.edit', $intern) }}" class="btn btn-sm ndc-btn-primary">
                    <i class="bi bi-pencil-square me-1"></i> {{ __('Edit') }}
                </a>
            @endif
            <form action="{{ route('admin.interns.destroy', $intern) }}" method="POST" onsubmit="return confirm('{{ __('Delete this intern?') }}');">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i> {{ __('Delete') }}
            </button>
        </form>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">
            {{ session('status') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ndc-card p-4 h-100">
                <h5 class="text-white mb-3">{{ __('Profile') }}</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <p class="small mb-1 text-secondary">{{ __('Name:') }}
                            <span class="fw-semibold text-white">{{ $intern->user->name }}</span>
                        </p>
                        <p class="small mb-1 text-secondary">{{ __('Email:') }}
                            <span class="fw-semibold text-white">{{ $intern->user->email }}</span>
                        </p>
                        <p class="small mb-1 text-secondary">{{ __('Phone:') }}
                            <span class="fw-semibold text-white">{{ $intern->request?->phone ?? '—' }}</span>
                        </p>
                        <p class="small mb-1 text-secondary">{{ __('School / University:') }}
                            <span class="fw-semibold text-white">{{ $intern->request?->school ?? '—' }}</span>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <p class="small mb-1 text-secondary">{{ __('Field of study:') }}
                            <span class="fw-semibold text-white">{{ $intern->request?->field_of_study ?? '—' }}</span>
                        </p>
                        <p class="small mb-1 text-secondary">{{ __('Filière:') }}
                            <span class="fw-semibold text-white">{{ $intern->request?->filiere ?? '—' }}</span>
                        </p>
                        <p class="small mb-1 text-secondary">{{ __('Department:') }}
                            <span class="fw-semibold text-white">{{ $intern->department?->name ?? '—' }}</span>
                        </p>
                        <p class="small mb-1 text-secondary">{{ __('Group:') }}
                            <span class="fw-semibold text-white">{{ $intern->group?->name ?? '—' }}</span>
                        </p>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <p class="small mb-1 text-secondary">{{ __('Internship period:') }}
                            <span class="fw-semibold text-white">
                                @if ($intern->start_date && $intern->end_date)
                                    {{ $intern->start_date->format('d M Y') }} – {{ $intern->end_date->format('d M Y') }}
                                @else
                                    —
                                @endif
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="small mb-1 text-secondary">{{ __('Duration:') }}
                            <span class="fw-semibold text-white">
                                {{ $intern->duration_days ? $intern->duration_days.' '.__('days') : '—' }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="small mb-1 text-secondary">{{ __('CV:') }}
                            @if($intern->request?->cv_path)
                                <a class="fw-semibold text-info text-decoration-none" target="_blank" href="{{ asset('storage/'.$intern->request->cv_path) }}">
                                    {{ __('Download CV') }}
                                </a>
                            @else
                                <span class="fw-semibold text-white">—</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="small mb-0 text-secondary">{{ __('Status:') }}
                            @if($intern->active)
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 small">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-50 small">{{ __('Inactive') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ndc-card p-4 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-white mb-0">{{ __('Attendance') }}</h6>
                    <a href="{{ route('admin.attendance.index') }}" class="small text-info text-decoration-none">
                        {{ __('Manage attendance') }}
                    </a>
                </div>
                <p class="small text-secondary mb-0">
                    {{ __('Present days:') }}
                    <span class="fw-semibold text-white">
                        {{ $intern->attendances->where('status', 'present')->count() }}
                    </span>
                </p>
            </div>
            <div class="ndc-card p-4 mt-3" id="certificate-builder">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
                    <div>
                        <h6 class="text-white mb-0">{{ __('Certificate builder') }}</h6>
                        <p class="small mb-0 text-secondary">{{ __('Generate a certificate draft populated with the intern’s data.') }}</p>
                    </div>
                    <form action="{{ route('admin.interns.certificate.draft', $intern) }}" method="POST">
                        @csrf
                        <button class="text-secondary btn btn-outline-primary btn-sm" type="submit">
                            {{ __('Generate draft') }}
                        </button>
                    </form>
                </div>
                @if(session('certificate_draft'))
                    <div class="alert alert-info small py-2">
                        {{ __('Draft loaded – update as needed then save.') }}
                    </div>
                @endif
                <div class="mb-2">
                    @if($intern->certificate)
                        <span class="badge bg-success bg-opacity-25 text-success border border-success small">{{ __('Issued') }}</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary small">{{ __('Draft') }}</span>
                    @endif
                </div>
                <form action="{{ route('admin.interns.certificate.store', $intern) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Issue date') }}</label>
                        <input type="date" name="issue_date" class="form-control form-control-sm"
                               value="{{ old('issue_date', session('certificate_draft.issue_date', optional($intern->certificate)->issue_date?->format('Y-m-d'))) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">{{ __('Hours completed') }}</label>
                        <input type="number" min="1" name="hours_completed" class="form-control form-control-sm"
                               value="{{ old('hours_completed', session('certificate_draft.hours_completed', $intern->certificate?->hours_completed)) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">{{ __('Projects / contributions') }}</label>
                        <textarea name="projects" class="form-control form-control-sm" rows="2" required>{{ old('projects', session('certificate_draft.projects', $intern->certificate?->projects)) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">{{ __('Soft skills highlighted') }}</label>
                        <input type="text" name="soft_skills" class="form-control form-control-sm"
                               value="{{ old('soft_skills', session('certificate_draft.soft_skills', $intern->certificate?->soft_skills)) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">{{ __('Mentor notes') }}</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2">{{ old('notes', session('certificate_draft.notes', $intern->certificate?->notes)) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">{{ __('Certificate message') }}</label>
                        <textarea name="message" class="form-control form-control-sm" rows="3">{{ old('message', session('certificate_draft.message', $intern->certificate?->message)) }}</textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn ndc-btn ndc-btn-primary btn-sm" type="submit">
                            {{ __('Save certificate') }}
                        </button>
                    </div>
                </form>
                @if($intern->certificate)
                    <div class="mt-3 border-top pt-3">
                        <p class="small text-secondary mb-1">{{ __('Signed by') }}:
                            <span class="fw-semibold text-white">{{ $intern->certificate->signed_by }}</span>
                        </p>
                        <p class="small text-secondary mb-0">{{ __('Issued on') }} {{ $intern->certificate->issue_date->format('d M Y') }}</p>
                        @if($intern->certificate->message)
                            <p class="small text-secondary mt-2 mb-0">
                                {{ __('Message:') }}
                                <span class="text-white">{{ $intern->certificate->message }}</span>
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
            <div class="ndc-card p-4 mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-white mb-0">{{ __('Admin note') }}</h6>
                    <a href="{{ route('admin.interns.edit', $intern) }}" class="small text-info text-decoration-none">
                        {{ __('Edit') }}
                    </a>
                </div>
                <p class="small text-secondary mb-0">
                    {{ $intern->admin_note ?? __('No note yet.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-7">
            <div class="ndc-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-white mb-0">{{ __('Attendance history') }}</h6>
                    <a href="{{ route('admin.attendance.index', ['group_id' => $intern->group_id]) }}" class="small text-info text-decoration-none">
                        {{ __('Mark / edit attendance') }}
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-dark-modern align-middle mb-0">
                        <thead>
                            <tr class="small text-secondary">
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($intern->attendances->take(30) as $att)
                            <tr>
                                <td class="small">{{ $att->date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $att->status === 'present' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ __('status.' . $att->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center small text-secondary py-3">{{ __('No attendance recorded yet.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ndc-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-white mb-0">{{ __('Group timetable') }}</h6>
                    <a href="{{ route('admin.timetables.index') }}" class="small text-info text-decoration-none">
                        {{ __('Manage timetable') }}
                    </a>
                </div>
                @php
                    $timetable = $intern->group?->timetables?->groupBy('day_of_week') ?? collect();
                    $daysOrder = ['monday','tuesday','wednesday','thursday','friday','saturday'];
                @endphp
                @if ($timetable->isNotEmpty())
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
                            @foreach ($daysOrder as $day)
                                @foreach ($timetable->get($day, collect()) as $slot)
                                    <tr>
                                        <td class="text-capitalize small">{{ __('days.' . $day) }}</td>
                                        <td class="small text-secondary">{{ $slot->start_time ?? '—' }}</td>
                                        <td class="small text-secondary">{{ $slot->end_time ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="small text-secondary mb-0">{{ __('No timetable defined for this group yet.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
