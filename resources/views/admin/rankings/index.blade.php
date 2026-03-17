@extends('layouts.app')

@section('title', __('Intern rankings – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-white mb-0">{{ __('Intern rankings') }}</h3>
        <a href="{{ route('admin.dashboard') }}" class="small text-secondary text-decoration-none">
            <i class="bi bi-arrow-left"></i> {{ __('Back to dashboard') }}
        </a>
    </div>

    <div class="row g-3">
        @forelse ($rankings as $ranking)
            @php
                $pos = $ranking->rank_position;
                $medal = match(true) {
                    $pos === 1 => '🥇',
                    $pos === 2 => '🥈',
                    $pos === 3 => '🥉',
                    default => '🏅'
                };
                $accent = match(true) {
                    $pos === 1 => 'linear-gradient(135deg,#fbbf24,#f59e0b)',
                    $pos === 2 => 'linear-gradient(135deg,#cbd5e1,#94a3b8)',
                    $pos === 3 => 'linear-gradient(135deg,#f97316,#fb923c)',
                    default => 'linear-gradient(135deg,#2563eb,#22c55e)',
                };
                $attendance = $ranking->attendance_score;
                $activity = $ranking->activity_score;
                $total = max(1, $ranking->total_score);
                $attPercent = min(100, round(($attendance / $total) * 100));
                $actPercent = min(100, round(($activity / $total) * 100));
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="ndc-card p-4 h-100" style="border-top:4px solid transparent; border-image: {{ $accent }} 1;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="small text-secondary">{{ __('Rank #:rank', ['rank' => $pos]) }}</div>
                            <h5 class="mb-0 text-white d-flex align-items-center gap-2">
                                <span>{{ $ranking->intern->user->name }}</span>
                                <span class="fs-6">{{ $medal }}</span>
                            </h5>
                            <div class="small text-secondary">{{ $ranking->intern->user->email }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-secondary">{{ __('Total') }}</div>
                            <div class="h4 mb-0 text-white">{{ $ranking->total_score }}</div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small text-secondary">
                            <span>{{ __('Attendance') }}</span>
                            <span>{{ $attendance }}</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between small text-secondary">
                            <span>{{ __('Activity') }}</span>
                            <span>{{ $activity }}</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $actPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
            <div class="ndc-card p-4 text-center small text-secondary">
                {{ __('No rankings yet.') }}
            </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
