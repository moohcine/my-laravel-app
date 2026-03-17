@extends('layouts.app')

@section('title', __('Intern history – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-secondary small mb-1">{{ __('Archive') }}</p>
            <h3 class="fw-bold text-white mb-0">{{ __('Former interns') }}</h3>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm ndc-btn-outline">
            {{ __('Back to dashboard') }}
        </a>
    </div>

    <div class="ndc-card p-4 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Cohort start date') }}</label>
                <input type="date" name="cohort_start" value="{{ $cohortStart }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Cohort end date') }}</label>
                <input type="date" name="cohort_end" value="{{ $cohortEnd }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-4 d-flex align-items-center gap-2">
                <button type="submit" class="btn ndc-btn ndc-btn-primary btn-sm w-100">
                    {{ __('Apply filters') }}
                </button>
                @php
                    $exportParams = array_filter([
                        'cohort_start' => $cohortStart,
                        'cohort_end' => $cohortEnd,
                    ]);
                @endphp
                <a href="{{ route('admin.interns.history.export') }}{{ $exportParams ? ('?' . http_build_query($exportParams)) : '' }}" class="btn btn-sm btn-outline-info w-100">
                    {{ __('Export archive') }}
                </a>
            </div>
        </form>
    </div>

    <div class="ndc-card p-0">
        <div class="table-responsive">
            <table class="table table-sm table-dark-modern align-middle mb-0">
                <thead class="table-light">
                <tr class="small text-secondary">
                    <th>{{ __('Intern') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Department') }}</th>
                    <th>{{ __('Group') }}</th>
                    <th>{{ __('Start') }}</th>
                    <th>{{ __('End') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Training notes') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($formerInterns as $intern)
                    <tr>
                        <td>{{ $intern->user->name }}</td>
                        <td class="small text-secondary">{{ $intern->user->email }}</td>
                        <td class="small text-secondary">{{ $intern->department?->name ?? '—' }}</td>
                        <td class="small text-secondary">{{ $intern->group?->name ?? '—' }}</td>
                        <td class="small text-secondary">{{ $intern->start_date?->format('d M Y') ?? '—' }}</td>
                        <td class="small text-secondary">{{ $intern->end_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            @if(!$intern->active)
                                <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-50 small">{{ __('Inactive') }}</span>
                            @else
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 small">{{ __('Active') }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#note-{{ $intern->id }}" aria-expanded="false">
                                {{ __('View note') }}
                            </button>
                            <div class="collapse mt-2 small text-secondary" id="note-{{ $intern->id }}">
                                {{ $intern->admin_note ?? __('No note yet.') }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center small text-secondary py-3">
                            {{ __('No former interns recorded yet.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $formerInterns->links() }}
    </div>
</div>
@endsection
