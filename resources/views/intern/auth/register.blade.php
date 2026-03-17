@extends('layouts.app')

@section('title', __('Intern Registration – NDC PRO'))

@section('content')
<div class="home-hero position-relative overflow-hidden">
    <div class="hero-gradient hero-gradient-one"></div>
    <div class="hero-gradient hero-gradient-two"></div>
    <div class="container py-5 py-md-6">
        <div class="row justify-content-center">
            <div class="col-lg-7 fade-up">
                <div class="ndc-card p-4 p-md-5">
                    <h2 class="fw-bold text-white mb-3">
                        {{ __('Intern Registration') }}
                    </h2>
                    <p class="text-secondary mb-4">
                        {{ __('Fill in your information and upload your CV (PDF). Your application will appear as') }}
                        <span class="text-warning">{{ __('Pending approval') }}</span> {{ __('until reviewed by NDC PRO admins.') }}
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger small">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('intern.register.submit') }}" enctype="multipart/form-data" class="row g-3">
                        @csrf

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Password') }}</label>
                            <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('School / University') }}</label>
                            <input type="text" name="school" value="{{ old('school') }}" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Field of study') }}</label>
                            <input type="text" name="field_of_study" value="{{ old('field_of_study') }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Filière') }}</label>
                            <input type="text" name="filiere" value="{{ old('filiere') }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Internship start date') }}</label>
                            <input type="date" name="period_start" value="{{ old('period_start') }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Internship end date') }}</label>
                            <input type="date" name="period_end" value="{{ old('period_end') }}" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Upload CV (PDF)') }}</label>
                            <input type="file" name="cv" accept="application/pdf" class="form-control form-control-sm" required>
                            <small class="text-secondary">{{ __('Max size 2MB. PDF only.') }}</small>
                        </div>

                        <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('intern.login') }}" class="small text-secondary text-decoration-none">
                                {{ __('Already registered?') }} <span class="text-info">{{ __('Login') }}</span>
                            </a>
                            <button type="submit" class="btn ndc-btn ndc-btn-primary">
                                {{ __('Submit application') }}
                            </button>
                        </div>
                    </form>
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
        background: radial-gradient(circle at 40% 20%, rgba(66, 94, 220, 0.9), rgba(148, 163, 184, 0.35));
    }
    .hero-gradient-two {
        bottom: -120px;
        left: -40px;
        width: 520px;
        height: 520px;
        background: radial-gradient(circle at 75% 25%, rgba(55, 129, 234, 0.95), rgba(148, 163, 184, 0.3));
        animation-delay: 4s;
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
