@extends('layouts.app')

@section('title', __('Internship Requests – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Internship Requests') }}</h3>
        <a href="{{ route('admin.dashboard') }}" class="small text-secondary text-decoration-none">
            <i class="bi bi-arrow-left"></i> {{ __('Back to dashboard') }}
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">
            {{ session('status') }}
        </div>
    @endif

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-3">
            <label class="form-label small text-secondary">{{ __('Status') }}</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">{{ __('All') }}</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>{{ __('Accepted') }}</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-secondary">{{ __('Search (name or email)') }}</label>
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="{{ __('Search intern...') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm ndc-btn ndc-btn-outline w-100">
                <i class="bi bi-funnel me-1"></i> {{ __('Filter') }}
            </button>
        </div>
    </form>

    <div class="ndc-card p-0">
        <div class="table-responsive">
            <table class="table table-sm table-dark table-dark-modern align-middle mb-0">
                <thead>
                <tr class="small text-secondary">
                    <th>{{ __('Intern') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('School') }}</th>
                    <th>{{ __('Group') }}</th>
                    <th>{{ __('Intern status') }}</th>
                    <th>{{ __('Period') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($requests as $req)
                    @php $intern = $req->intern; @endphp
                    <tr>
                        <td>{{ $req->user->name }}</td>
                        <td>{{ $req->user->email }}</td>
                        <td class="small text-secondary">{{ $req->school }}</td>
                        <td class="small text-secondary">
                            {{ $intern?->group?->name ?? '—' }}
                        </td>
                        <td>
                            @if($intern)
                                <span class="badge bg-opacity-25 border small {{ $intern->active ? 'bg-success text-success border-success' : 'bg-secondary text-secondary border-secondary' }}">
                                    {{ $intern->active ? __('Active') : __('Inactive') }}
                                </span>
                            @else
                                <span class="badge bg-opacity-25 border small bg-warning text-warning border-warning">{{ __('Pending onboarding') }}</span>
                            @endif
                        </td>
                        <td class="small text-secondary">
                            {{ $req->period_start?->format('d M Y') }} – {{ $req->period_end?->format('d M Y') }}
                        </td>
                        <td>
                            <span class="badge bg-opacity-25 border small
                                @if($req->status === 'pending') bg-warning text-warning border-warning
                                @elseif($req->status === 'accepted') bg-success text-success border-success
                                @else bg-danger text-danger border-danger @endif">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.requests.show', $req) }}" class="btn btn-sm btn-outline-info">
                                {{ __('Details') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center small text-secondary py-3">
                            {{ __('No internship requests yet.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $requests->links() }}
    </div>
</div>
@endsection
