@extends('layouts.app')

@section('title', __('Groups – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Groups') }}</h3>
        <a href="{{ route('admin.groups.create') }}" class="btn btn-sm ndc-btn ndc-btn-primary">
            <i class="bi bi-plus-lg me-1"></i> {{ __('New group') }}
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">
            {{ session('status') }}
        </div>
    @endif

    <div class="ndc-card p-0">
        <div class="table-responsive">
            <table class="table table-sm table-dark table-dark-modern align-middle mb-0">
                <thead>
                <tr class="small text-secondary">
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Department') }}</th>
                    <th>{{ __('Max interns') }}</th>
                    <th>{{ __('Capacity') }}</th>
                    <th>{{ __('Days') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td class="small text-secondary">{{ $group->department?->name ?? '—' }}</td>
                        <td class="small text-secondary">{{ $group->max_interns }}</td>
                        <td class="small text-secondary">
                            @php
                                $count = $group->active_interns_count ?? 0;
                                $capacity = max($group->max_interns, 1);
                                $percent = min(100, (int) floor(($count / $capacity) * 100));
                            @endphp
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $count }}/{{ $group->max_interns }}</span>
                                @if($count >= $group->max_interns)
                                    <span class="badge rounded-pill border border-danger text-danger bg-opacity-10 small">{{ __('group.full_label') }}</span>
                                @endif
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px;">
                                <div class="progress-bar {{ $percent >= 90 ? 'bg-danger' : 'bg-info' }}" role="progressbar"
                                     style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </td>
                        <td class="small text-secondary">
                            @if($group->days_of_week)
                                {{ implode(', ', $group->days_of_week) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-sm btn-outline-info">{{ __('View') }}</a>
                        </td>
                    </tr>
                @empty
                        <tr>
                            <td colspan="5" class="text-center small text-secondary py-3">
                                {{ __('No groups yet.') }}
                            </td>
                        </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $groups->links() }}
    </div>
</div>
@endsection
