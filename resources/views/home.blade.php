@extends('layouts.app')

@section('title', __('NDC PRO â€“ Internship Management'))

@section('content')
<div class="home-hero position-relative overflow-hidden">
    <div class="hero-gradient hero-gradient-one"></div>
    <div class="hero-gradient hero-gradient-two"></div>
    <div class="container py-5 py-md-6">
        <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="text-center text-lg-start fade-up">
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success mb-3 px-3 py-2">
                        <i class="bi bi-grid-1x2-fill me-1"></i> {{ __('NDC PRO Â· Internship Management') }}
                    </span>
                    <h1 class="display-5 fw-bold text-white mb-3">
                        {{ __('Streamline your') }} <span class="text-info">{{ __('internship lifecycle') }}</span>
                        {{ __('inside NDC PRO.') }}
                    </h1>
                    <p class="lead text-secondary mb-4">
                        {{ __('Centralize internship requests, groups, schedules, and attendance in one modern dashboard. Designed for fast, secure workflows between admins and interns.') }}
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('admin.login') }}" class="btn ndc-btn ndc-btn-primary">
                            <i class="bi bi-shield-lock-fill me-2"></i> {{ __('ADMIN') }}
                        </a>
                        <a href="{{ route('intern.landing') }}" class="btn ndc-btn ndc-btn-outline">
                            <i class="bi bi-person-badge-fill me-2"></i> {{ __('INTERN') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 fade-up-delayed">
                <div class="ndc-card p-4 p-md-5">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5 class="mb-0">{{ __('At a glance') }}</h5>
                    </div>
                    <p class="text-secondary mb-4">
                        {{ __('Track total interns, pending requests, and attendance rate in real time.') }}
                    </p>
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="small text-secondary">{{ __('Interns') }}</div>
                            <div class="h4 fw-bold text-white">{{ $totalInterns }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-secondary">{{ __('Pending') }}</div>
                            <div class="h4 fw-bold text-warning">{{ $pendingRequests }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-secondary">{{ __('Attendance') }}</div>
                            <div class="h4 fw-bold text-info">{{ $attendanceRate }}%</div>
                        </div>
                    </div>
                    <div class="mt-4 small text-secondary">
                        {{ __('Working days for') }} <span class="text-info">{{ __('NDC PRO') }}</span> {{ __('department:') }}
                    </div>
                    <div class="mt-2 d-flex flex-wrap gap-2">
                        @foreach ($weekDays as $day)
                            @php
                                $isActive = $activeDays->contains($day);
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 small {{ $isActive ? 'border border-info text-info' : 'border border-secondary text-secondary' }}">
                                {{ __('days.' . $day) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="https://wa.me/212641010222" class="whatsapp-float">
        <i class="bi bi-whatsapp"></i>
    </a>
</div>
@endsection

@push('styles')
<style>
    .home-hero {
        min-height: 100vh;
        background: #d7e2e6;
        color: #0f172a;
        position: relative;
        z-index: 1;
    }
    .home-hero .container {
        position: relative;
        z-index: 2;
    }
    .hero-gradient {
        position: absolute;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        filter: blur(2px);
        opacity: 0.55;
        z-index: 0;
        animation: pulseMove 2s ease-in-out infinite;
    }
    .hero-gradient-one {
        top: -80px;
        right: -60px;
        background: radial-gradient(circle at 40% 20%, rgba(72, 138, 237, 0.9), rgba(148, 163, 184, 0.35));
    }
    .hero-gradient-two {
        bottom: -120px;
        left: -40px;
        width: 520px;
        height: 520px;
        background: radial-gradient(circle at 75% 25%, rgba(70, 120, 235, 0.95), rgba(148, 163, 184, 0.3));
    }
    @keyframes pulseMove {
        0%, 100% {
            transform: translate3d(0, 0, 0) scale(1);
        }
        50% {
            transform: translate3d(10px, 20px, 0) scale(1.05);
        }
    }
    .whatsapp-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #25d366;
        color: #fff;
        box-shadow: 0 12px 25px rgba(37,211,102,0.4);
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        cursor: pointer;
    }
</style>
@endpush
