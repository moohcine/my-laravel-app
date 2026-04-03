@extends('layouts.app')

@section('title', __('Intern Dashboard – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1">{{ __('Welcome, :name', ['name' => $user->name]) }}</h2>
            <p class="text-secondary mb-0 small">{{ __('Monitor your internship status, group, timetable and attendance.') }}</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">
            {{ session('status') }}
        </div>
    @endif

    @if (!$isActive)
        <div class="d-flex justify-content-center">
            <div class="ndc-card p-4 text-center" style="max-width: 480px;">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <i class="bi bi-award text-info fs-3 me-2"></i>
                    <h5 class="mb-0 text-white">{{ __('Certificate') }}</h5>
                </div>
                @if($certificate)
                    <p class="text-white mb-1">{{ __('Issued on') }} {{ $certificate->issue_date?->format('d M Y') }}</p>
                    <p class="small text-secondary mb-3">{{ __('Hours completed:') }} {{ $certificate->hours_completed }}</p>
                    <a href="{{ route('intern.certificate.download') }}" class="btn ndc-btn-primary btn-sm">
                        <i class="bi bi-download me-1"></i> {{ __('Download certificate') }}
                    </a>
                @else
                    <p class="small text-secondary mb-0">{{ __('Your certificate will appear after the admin finishes the review.') }}</p>
                @endif
            </div>
        </div>
    @else

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="ndc-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small">{{ __('Application status') }}</span>
                    <i class="bi bi-clipboard-check text-info"></i>
                </div>
                @php
                    $status = $request?->status ?? 'pending';
                @endphp
                <div class="h5 mb-1 text-white text-capitalize">
                    {{ __('status.' . $status) }}
                </div>
                <div class="small">
                    @if ($status === 'pending')
                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50">{{ __('Pending approval') }}</span>
                    @elseif ($status === 'accepted')
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50">{{ __('Accepted') }}</span>
                    @else
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50">{{ __('Rejected') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ndc-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small">{{ __('Internship period') }}</span>
                    <i class="bi bi-calendar3 text-info"></i>
                </div>
                <div class="small text-secondary mb-1">
                    @if ($intern && $intern->start_date && $intern->end_date)
                        {{ $intern->start_date->format('d M Y') }} – {{ $intern->end_date->format('d M Y') }}
                    @elseif($request)
                        {{ $request->period_start?->format('d M Y') }} – {{ $request->period_end?->format('d M Y') }}
                    @else
                        {{ __('Not defined yet.') }}
                    @endif
                </div>
                <div class="small">
                    @if ($totalDays)
                        {{ __('Duration:') }}
                        <span class="fw-semibold text-white">
                            {{ $totalDays }} {{ __('days') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ndc-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small">{{ __('Attendance') }}</span>
                    <i class="bi bi-activity text-info"></i>
                </div>
                <div class="h5 mb-1 text-white">
                    {{ $attendanceCount }} <span class="small text-secondary">{{ __('days present') }}</span>
                </div>
                @if ($totalAttendanceRecords > 0)
                    @php
                        $attendanceRate = round(($attendanceCount / $totalAttendanceRecords) * 100, 1);
                    @endphp
                    <div class="small text-secondary">
                        {{ __('Attendance performance:') }}
                        <span class="fw-semibold text-info">{{ $attendanceRate }}%</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="ndc-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-white">{{ __('Group information') }}</h5>
                    <i class="bi bi-people text-info"></i>
                </div>
                @if ($intern && $intern->group)
                    <p class="mb-1 small text-secondary">
                        {{ __('Group:') }} <span class="fw-semibold text-white">{{ $intern->group->name }}</span>
                    </p>
                    <p class="mb-1 small text-secondary">
                        {{ __('Department:') }}
                        <span class="fw-semibold text-white">
                            {{ $intern->department ?? '—' }}
                        </span>
                    </p>
                    <div class="mt-3">
                        <p class="small text-secondary mb-2">{{ __('Group members') }}</p>
                        @forelse ($groupMembers as $member)
                            <div class="d-flex justify-content-between small text-white mb-1">
                                <span>{{ $member->user->name }}</span>
                                <span class="text-secondary">{{ $member->user->email }}</span>
                            </div>
                        @empty
                            <p class="small text-secondary mb-0">{{ __('No active members in this group') }}</p>
                        @endforelse
                    </div>
                @else
                    <p class="mb-0 small text-secondary">
                        {{ __('You are not yet assigned to a group. Please wait for admin confirmation.') }}
                    </p>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="ndc-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-white">{{ __('Timetable') }}</h5>
                    <i class="bi bi-clock-history text-info"></i>
                </div>
                @php
                    $daysOrder = ['monday','tuesday','wednesday','thursday','friday','saturday'];
                @endphp
                @if ($intern && $intern->group && $timetable->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-dark table-dark-modern align-middle mb-0">
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
                                        <td class="text-capitalize">{{ __('days.' . $day) }}</td>
                                        <td class="small text-secondary">{{ $slot->start_time ?? '—' }}</td>
                                        <td class="small text-secondary">{{ $slot->end_time ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($intern && $intern->group)
                    <p class="small text-secondary mb-0">
                        {{ __('Your group is created but the timetable has not been defined yet.') }}
                    </p>
                @else
                    <p class="small text-secondary mb-0">
                        {{ __('Timetable will appear after you are assigned to a group.') }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-md-6">
            <a href="{{ route('intern.tasks.index') }}" class="text-decoration-none">
                <div class="ndc-card p-3 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="small text-secondary text-uppercase">{{ __('Group tasks') }}</div>
                        <h5 class="text-white fw-semibold mb-2">{{ __('View your tasks') }}</h5>
                        <p class="small text-secondary mb-0">{{ __('See the tasks your group is working on and mark them as completed when done.') }}</p>
                    </div>
                    <div class="mt-3 text-end">
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-50">{{ __('My tasks – NDC PRO') }}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('intern.certificate.show') }}" class="text-decoration-none">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 text-white">{{ __('Certificate') }}</h5>
                        <i class="bi bi-award text-info"></i>
                    </div>
                    @if($intern && $intern->certificate)
                        <p class="text-white mb-1">{{ __('Issued on') }} {{ $intern->certificate->issue_date->format('d M Y') }}</p>
                        <p class="small text-secondary mb-0">{{ __('Hours completed:') }} {{ $intern->certificate->hours_completed }}</p>
                    @else
                        <p class="small text-secondary mb-0">{{ __('Your certificate will appear after the admin finishes the review.') }}</p>
                    @endif
                </div>
            </a>
        </div>
    </div>
@endif
</div>
@endsection
