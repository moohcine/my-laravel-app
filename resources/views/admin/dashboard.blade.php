@extends('layouts.app')

@section('title', __('Admin Dashboard â€“ NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-white mb-1">{{ __('NDC PRO Dashboard') }}</h2>
                <p class="text-secondary mb-0 small">{{ __('Overview of interns, applications, departments and attendance.') }}</p>
            </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">
            {{ session('status') }}
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('admin.interns.index') }}">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary">{{ __('Total interns') }}</span>
                        <i class="bi bi-people text-info"></i>
                    </div>
                    <div class="h4 text-white mb-0">{{ $totalInterns }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('admin.requests.index', ['status' => 'accepted']) }}">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary">{{ __('Accepted') }}</span>
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="h4 text-success mb-0">{{ $acceptedInterns }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('admin.requests.index', ['status' => 'pending']) }}">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary">{{ __('Pending requests') }}</span>
                        <i class="bi bi-hourglass-split text-warning"></i>
                    </div>
                    <div class="h4 text-warning mb-0">{{ $pendingRequests }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('admin.requests.index', ['status' => 'rejected']) }}">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary">{{ __('Rejected') }}</span>
                        <i class="bi bi-x-circle text-danger"></i>
                    </div>
                    <div class="h4 text-danger mb-0">{{ $rejectedInterns }}</div>
                </div>
            </a>
        </div>
    </div>


    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('admin.timetables.index') }}">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary">{{ __('Timetables') }}</span>
                        <i class="bi bi-calendar3 text-info"></i>
                    </div>
                    <div class="h4 text-white mb-0">{{ $timetableSlots }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('admin.interns.history') }}">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary">{{ __('History') }}</span>
                        <i class="bi bi-archive text-secondary"></i>
                    </div>
                    <div class="h4 text-white mb-0">{{ $formerInterns }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('admin.tasks.index') }}">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary">{{ __('Tasks – NDC PRO') }}</span>
                        <i class="bi bi-card-checklist text-info"></i>
                    </div>
                    <div class="h4 text-white mb-0">{{ __('View tasks') }}</div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="ndc-card p-4 kpi-feed">
                @php
                    $funnelStages = collect([
                        ['label' => __('Requests'), 'value' => $totalRequests, 'description' => __('recruitment_funnel.requests_desc')],
                        ['label' => __('Accepted'), 'value' => $acceptedInterns, 'description' => __('recruitment_funnel.accepted_desc')],
                        ['label' => __('Onboarded'), 'value' => $totalInterns, 'description' => __('recruitment_funnel.onboarded_desc')],
                    ]);
                    $maxFunnel = max($funnelStages->max(fn($stage) => $stage['value']) ?? 1, 1);
                    $pointDivider = max($attendanceTrend->count() - 1, 1);
                    $sparkPoints = $attendanceTrend->map(function ($point, $index) use ($pointDivider) {
                        $x = $pointDivider === 0 ? 0 : ($index / $pointDivider) * 100;
                        $y = 60 - min(max($point['rate'], 0), 100) * 0.5;
                        return "{$x},{$y}";
                    })->implode(' ');
                @endphp
                <div class="d-flex flex-column flex-lg-row gap-4">
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1 text-white">{{ __('Recruitment funnel') }}</h5>
                                <p class="small text-secondary mb-0">{{ __('recruitment_funnel.tagline') }}</p>
                            </div>
                            <span class="badge rounded-pill border border-secondary text-secondary">{{ __('NDC PRO') }}</span>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($funnelStages as $stage)
                                @php
                                    $percent = $maxFunnel > 0 ? min(100, round(($stage['value'] / $maxFunnel) * 100)) : 0;
                                @endphp
                                <div class="funnel-stage flex-fill">
                                    <div class="small text-secondary mb-1">{{ $stage['label'] }}</div>
                                    <div class="display-5 fw-bold mb-1 text-white">{{ $stage['value'] }}</div>
                                    <div class="small text-secondary">{{ $stage['description'] }}</div>
                                    <div class="progress funnel-progress mt-3">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <p class="small text-secondary mb-1">{{ __('Weekly attendance average') }}</p>
                                        <div class="h3 mb-0 text-info">{{ number_format($weeklyAttendanceAverage, 1) }}%</div>
                                    </div>
                                    <span class="small text-secondary">{{ __('Average over last 7 days') }}</span>
                                </div>
                                <div class="sparkline-chart mb-3 clickable-sparkline" role="button" tabindex="0">
                                    <svg viewBox="0 0 100 60" preserveAspectRatio="none">
                                        <polyline points="{{ $sparkPoints }}" stroke="rgba(59,130,246,0.9)" stroke-width="3" fill="none" stroke-linecap="round"/>
                                        <polyline points="{{ $sparkPoints }}" stroke="rgba(37,99,235,0.25)" stroke-width="9" fill="none" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="small text-secondary">{{ __('Completion rate') }}</div>
                                        <div class="h5 text-success mb-0">{{ $completionRate }}%</div>
                                    </div>
                                    <div class="small text-secondary">{{ __('Former interns vs active') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ndc-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-white">{{ __('Interns per department') }}</h5>
                    <a href="{{ route('admin.interns.index') }}" class="small text-info text-decoration-none">
                        {{ __('View interns') }} <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
                <div class="departments-grid">
                    @forelse ($internsPerDept as $dept)
                        <div class="department-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="small text-secondary text-uppercase">{{ __('Department') }}</div>
                                    <div class="h6 mb-0 text-white">{{ $dept->name }}</div>
                                </div>
                                <div class="badge bg-secondary bg-opacity-15 text-white border border-secondary border-opacity-25 px-3 py-1">
                                    {{ $dept->interns_count }} {{ __('Interns') }}
                                </div>
                            </div>
                            <div class="small text-secondary">
                                {{ __('Active interns currently assigned.') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center small text-secondary py-4">
                            {{ __('No departments defined yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ndc-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-white">{{ __('Intern attendance rate') }}</h5>
                    <a href="{{ route('admin.attendance.index') }}" class="small text-info text-decoration-none">
                        {{ __('Manage attendance') }} <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="min-height: 120px;">
                    <div class="text-center">
                        <div class="display-5 fw-bold text-info mb-1">{{ $attendanceRate }}%</div>
                        <div class="small text-secondary">{{ __('Average presence based on recorded days.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="ndc-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1 text-white">{{ __('Department statistics') }}</h5>
                        <p class="small text-secondary mb-0">{{ __('Visualize intern distribution by department and status.') }}</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-7">
                        <canvas id="deptChart" height="140"></canvas>
                    </div>
                    <div class="col-lg-5">
                        <div class="ndc-card p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-secondary">{{ __('Accepted') }}</span>
                                <span class="fw-semibold text-success">{{ $acceptedInterns }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="small text-secondary">{{ __('Pending') }}</span>
                                <span class="fw-semibold text-warning">{{ $pendingRequests }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="small text-secondary">{{ __('Rejected') }}</span>
                                <span class="fw-semibold text-danger">{{ $rejectedInterns }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2" id="attendance-trend-curve">
        <div class="col-12">
            <div class="ndc-card p-4 simple-curve">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h5 class="mb-0 text-white">{{ __('Attendance trend') }}</h5>
                        <p class="small text-secondary mb-0">{{ __('Weekly attendance rates') }}</p>
                    </div>
                    <span class="badge bg-light text-dark">{{ __('Weekly') }}</span>
                </div>
                @php
                    $count = max($attendanceTrend->count(), 1);
                    $points = $attendanceTrend->map(function ($point, $index) use ($count) {
                        $x = $count === 1 ? 50 : ($index / ($count - 1)) * 100;
                        $rate = min(max($point['rate'], 0), 100);
                        $y = 35 - ($rate / 100) * 30;
                        return "{$x},{$y}";
                    })->implode(' ');
                @endphp
                <div class="curve-chart">
                    <svg viewBox="0 0 100 40" preserveAspectRatio="none">
                        <polyline points="{{ $points }}" stroke="rgba(59,130,246,0.9)" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <polyline points="{{ $points }}" stroke="rgba(59,130,246,0.2)" stroke-width="8" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="d-flex justify-content-between small text-secondary mt-3">
                    @foreach ($attendanceTrend as $item)
                        <span>{{ $item['label'] }}: {{ $item['rate'] }}%</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .simple-curve {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 1rem;
    }
    .curve-chart {
        padding: 0.75rem;
        border-radius: 0.7rem;
    }
    .curve-chart svg {
        width: 100%;
        height: 140px;
    }
    .clickable-sparkline {
        cursor: pointer;
        outline: none;
    }
    .clickable-sparkline:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
    }
    .kpi-feed {
        border-radius: 1.25rem;
    }
    .kpi-feed .funnel-stage {
        padding: 1rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        min-width: 210px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .kpi-feed .funnel-stage .funnel-progress {
        height: 6px;
        background: rgba(148, 163, 184, 0.18);
        border-radius: 999px;
        margin-top: 0.5rem;
    }
    .kpi-feed .sparkline-chart {
        min-height: 80px;
        border-radius: 0.8rem;
        background: rgba(15, 23, 42, 0.4);
        padding: 0.6rem;
    }
    .kpi-feed .sparkline-chart svg {
        width: 100%;
        height: 80px;
    }
    @media (max-width: 991px) {
        .kpi-feed .funnel-stage {
            min-width: calc(33% - 0.5rem);
        }
    }
    .departments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
    .department-card {
        padding: 1.1rem 1.25rem;
        border-radius: 1.15rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
        min-height: 130px;
    }
    .department-card h6 {
        font-size: 1rem;
        letter-spacing: 0.01em;
    }
    .department-card .badge {
        font-size: 0.75rem;
    }
    @media (max-width: 767px) {
        .departments-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('deptChart');
    if (!ctx) return;

    const labels = @json($internsPerDept->pluck('name'));
    const data = @json($internsPerDept->pluck('interns_count'));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: @json(__('Interns')),
                data,
                backgroundColor: 'rgba(37, 99, 235, 0.6)',
                borderColor: 'rgba(37, 99, 235, 0.9)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#0f172a' }
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    const sparkline = document.querySelector('.clickable-sparkline');
    const targetCurve = document.getElementById('attendance-trend-curve');
    const scrollToCurve = () => {
        if (!targetCurve) return;
        targetCurve.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    if (sparkline) {
        sparkline.addEventListener('click', scrollToCurve);
        sparkline.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                scrollToCurve();
            }
        });
    }
});
</script>
@endpush
