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
        <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('admin.attendance.index') }}">
                <div class="ndc-card p-3 h-100">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary">{{ __('Manage attendance') }}</span>
                        <i class="bi bi-clipboard-check text-info"></i>
                    </div>
                    <div class="h4 text-white mb-0">{{ $attendanceRate }}%</div>
                    <div class="small text-secondary">{{ __('Attendance rate') }}</div>
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
                        ['label' => __('Onboarded'), 'value' => $activeInterns, 'description' => __('recruitment_funnel.onboarded_desc')],
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

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="ndc-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="small text-secondary text-uppercase mb-1">{{ __('Internship status by filière') }}</p>
                        <h6 class="mb-0 text-white">{{ __('Internship distribution') }}</h6>
                    </div>
                    <span class="small text-secondary">{{ __('Accepted vs Rejected vs Pending') }}</span>
                </div>
                <div class="chart-wrapper-sm">
                    <canvas id="filiereChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2" id="attendance-trend-curve">
        <div class="col-12">
            <div class="ndc-card p-4 simple-curve">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="mb-0 text-dark">{{ __('Attendance trend') }}</h5>
                        <p class="small text-secondary mb-0">{{ __('Weekly attendance rates') }}</p>
                    </div>
                    <span class="badge bg-light text-dark shadow-sm">{{ __('Weekly') }}</span>
                </div>
                @php
                    $count = max($attendanceTrend->count(), 1);
                    $curvePoints = $attendanceTrend->map(function ($point, $index) use ($count) {
                        $x = $count === 1 ? 50 : ($index / ($count - 1)) * 100;
                        $rate = min(max($point['rate'], 0), 100);
                        $y = 35 - ($rate / 100) * 30;
                        return ['x' => $x, 'y' => $y, 'label' => $point['label'], 'rate' => $rate];
                    });
                    $polyPoints = $curvePoints->map(fn($p) => "{$p['x']},{$p['y']}")->implode(' ');
                    $areaPoints = "0,40 {$polyPoints} 100,40";
                @endphp
                <div class="curve-chart">
                    <svg viewBox="0 0 100 40" preserveAspectRatio="none">
                        <polygon points="{{ $areaPoints }}" fill="rgba(96,165,250,0.15)"/>
                        <polyline points="{{ $polyPoints }}" stroke="#3b82f6" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        @foreach ($curvePoints as $point)
                            <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="1.3" fill="#f9fbff" stroke="#3b82f6" stroke-width="0.9"/>
                        @endforeach
                    </svg>
                </div>
                <div class="d-flex justify-content-between text-secondary mt-3 px-1">
                    @foreach ($curvePoints as $item)
                        <span class="small">{{ $item['label'] }}: {{ $item['rate'] }}%</span>
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
        background: #f9fbff;
        border: 1px solid #edf2f7;
        border-radius: 1.2rem;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        color: #0f172a;
    }
    .curve-chart {
        padding: 0.5rem 0.75rem 0.25rem;
        border-radius: 0.9rem;
    }
    .curve-chart svg {
        width: 100%;
        height: 170px;
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
    .chart-wrapper-sm {
        height: clamp(180px, 32vw, 260px);
    }
    .chart-wrapper-sm canvas {
        width: 100% !important;
        height: 100% !important;
    }
    @media (max-width: 991px) {
        .kpi-feed .funnel-stage {
            min-width: calc(33% - 0.5rem);
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filiereCtx = document.getElementById('filiereChart');
    if (filiereCtx) {
        const labels = @json($filiereStats->pluck('filiere'));
        const acceptedData = @json($filiereStats->pluck('accepted'));
        const rejectedData = @json($filiereStats->pluck('rejected'));
        const pendingData = @json($filiereStats->pluck('pending'));

        new Chart(filiereCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: @json(__('Accepted')), data: acceptedData, backgroundColor: 'rgba(34,197,94,0.8)' },
                    { label: @json(__('Rejected')), data: rejectedData, backgroundColor: 'rgba(239,68,68,0.8)' },
                    { label: @json(__('Pending')), data: pendingData, backgroundColor: 'rgba(234,179,8,0.9)' },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: false, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { backgroundColor: '#0f172a' },
                },
            },
        });
    }

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
