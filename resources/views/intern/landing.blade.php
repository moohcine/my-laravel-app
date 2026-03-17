@extends('layouts.app')

@section('title', __('Intern Portal – NDC PRO'))

@section('content')
<div class="home-hero position-relative overflow-hidden">
    <div class="hero-gradient hero-gradient-one"></div>
    <div class="hero-gradient hero-gradient-two"></div>
    <div class="container py-5 py-md-6">
        <div class="row justify-content-center" style="min-height: 80vh;">
            <div class="col-md-7 col-lg-6 fade-up">
                <div class="ndc-card p-4 p-md-5 text-center">
                    <h2 class="fw-bold text-white mb-3">
                        {{ __('Intern Portal') }}
                    </h2>
                    <p class="text-secondary mb-4">
                        {{ __('Register your internship application or login to follow your approval status, group assignment and timetable.') }}
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('intern.register') }}" class="btn ndc-btn ndc-btn-primary">
                            <i class="bi bi-file-earmark-plus me-2"></i> {{ __('Register') }}
                        </a>
                        <a href="{{ route('intern.login') }}" class="btn ndc-btn ndc-btn-outline">
                            <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('Login') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        background: radial-gradient(circle at 40% 20%, rgba(91, 157, 233, 0.9), rgba(148, 163, 184, 0.35));
    }
    .hero-gradient-two {
        bottom: -120px;
        left: -40px;
        width: 520px;
        height: 520px;
        background: radial-gradient(circle at 75% 25%, rgba(57, 155, 225, 0.95), rgba(148, 163, 184, 0.3));
    }
    @keyframes pulseMove {
        0%, 100% {
            transform: translate3d(0, 0, 0) scale(1);
        }
        50% {
            transform: translate3d(10px, 20px, 0) scale(1.05);
        }
    }
</style>
@endpush
