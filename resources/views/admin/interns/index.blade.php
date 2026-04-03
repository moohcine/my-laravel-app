@extends('layouts.app')

@section('title', __('Interns – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="small text-secondary text-uppercase mb-1">{{ __('NDC PRO cohort') }}</p>
            <h3 class="fw-bold mb-0">{{ __('Interns') }}</h3>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> {{ __('Back to dashboard') }}
        </a>
    </div>

    <div class="ndc-card p-4 mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label class="form-label small text-secondary">{{ __('Search') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0 rounded-end" placeholder="{{ __('Search name or department') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('Sort by') }}</label>
                <div class="d-flex gap-2">
                    <select name="sort" class="form-select">
                        <option value="created_at" @selected($sort==='created_at')>{{ __('Date added') }}</option>
                        <option value="name" @selected($sort==='name')>{{ __('Name') }}</option>
                        <option value="start_date" @selected($sort==='start_date')>{{ __('Start date') }}</option>
                        <option value="end_date" @selected($sort==='end_date')>{{ __('End date') }}</option>
                    </select>
                    <select name="direction" class="form-select">
                        <option value="desc" @selected($direction==='desc')>{{ __('Descending') }}</option>
                        <option value="asc" @selected($direction==='asc')>{{ __('Ascending') }}</option>
                    </select>
                </div>
            </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn ndc-btn ndc-btn-outline w-100 small text-uppercase">{{ __('Apply filters') }}</button>
                </div>
        </form>
    </div>

    <div class="ndc-card p-3 mb-4">
        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0 interns-table">
                <thead>
                <tr class="small text-secondary text-uppercase">
                    <th>{{ __('Intern') }}</th>
                    <th>{{ __('Department') }}</th>
                    <th>{{ __('Group') }}</th>
                    <th>{{ __('Start') }}</th>
                    <th>{{ __('End') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($interns as $intern)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $intern->user->name }}</div>
                            <div class="small text-secondary">{{ $intern->user->email }}</div>
                            <div class="small text-secondary">
                                {{ __('Phone:') }} {{ $intern->request?->phone ?? __('N/A') }}
                            </div>
                            <div class="small text-secondary">
                                {{ __('School:') }} {{ $intern->request?->school ?? __('N/A') }}
                            </div>
                        </td>
                        <td class="small text-secondary">{{ $intern->department ?? '—' }}</td>
                        <td class="small text-secondary">{{ $intern->group?->name ?? '—' }}</td>
                        <td class="small text-secondary">{{ optional($intern->start_date)->format('d M Y') ?? '—' }}</td>
                        <td class="small text-secondary">{{ optional($intern->end_date)->format('d M Y') ?? '—' }}</td>
                        <td class="text-center">
                            @if($intern->active)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-50 small">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-50 small">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1 flex-wrap">
                                <a href="{{ route('admin.interns.show', $intern) }}" class="btn btn-sm btn-outline-info">{{ __('Profile') }}</a>
                                <a href="{{ route('admin.interns.edit', $intern) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                                <form action="{{ route('admin.interns.destroy', $intern) }}" method="POST" onsubmit="return confirm('{{ __('Delete this intern?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center small text-secondary py-3">
                            {{ __('No interns yet.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(!empty($inactiveInterns) && $inactiveInterns->isNotEmpty())
        <div class="ndc-card p-3 mb-4 bg-white text-dark">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <p class="small text-uppercase text-muted mb-1">{{ __('Former interns') }}</p>
                    <h5 class="fw-semibold mb-0">{{ __('Inactive or finished') }}</h5>
                </div>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">{{ __('End date reached') }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr class="small text-secondary text-uppercase">
                            <th>{{ __('Intern') }}</th>
                            <th>{{ __('Department') }}</th>
                            <th>{{ __('Group') }}</th>
                            <th>{{ __('End') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inactiveInterns as $inactive)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $inactive->user->name }}</div>
                                    <div class="small text-muted">{{ $inactive->user->email }}</div>
                                </td>
                                <td class="small text-muted">{{ $inactive->department ?? '—' }}</td>
                                <td class="small text-muted">{{ $inactive->group?->name ?? '—' }}</td>
                                <td class="small text-muted">{{ optional($inactive->end_date)->format('d M Y') ?? '—' }}</td>
                                <td class="text-end">
                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                    <a href="{{ route('admin.interns.show', $inactive) }}" class="btn btn-sm btn-outline-info">{{ __('Profile') }}</a>
                                    <form action="{{ route('admin.interns.destroy', $inactive) }}" method="POST" onsubmit="return confirm('{{ __('Delete this intern?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-3">
        {{ $interns->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .interns-table tbody tr {
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        transition: background 0.2s ease;
    }
    .interns-table tbody tr:hover {
        background: rgba(59, 130, 246, 0.06);
    }
    .interns-table thead th {
        letter-spacing: 0.08em;
        font-size: 0.72rem;
    }
    .interns-table td, .interns-table th {
        padding: 1rem 0.75rem;
    }
    .interns-table .badge {
        min-width: 95px;
    }
</style>
@endpush
