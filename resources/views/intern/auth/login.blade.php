@extends('layouts.app')

@section('title', __('Intern Login – NDC PRO'))

@section('content')
<div class="home-hero position-relative overflow-hidden">
    <div class="hero-gradient hero-gradient-one"></div>
    <div class="hero-gradient hero-gradient-two"></div>
    <div class="container py-5 py-md-6">
        <div class="row align-items-center justify-content-center" style="min-height: 80vh;">
            <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0">
                <span class="badge rounded-pill bg-success bg-opacity-10 text-success mb-3 px-3 py-2">
                    <i class="bi bi-people-fill me-1"></i> {{ __('Stagiaire Hub') }}
                </span>
                <h1 class="display-5 fw-bold text-white mb-3">
                    {{ __('Suivez votre') }} <span class="text-info">{{ __('avenir chez NDC PRO') }}</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    {{ __('Consultez vos demandes, votre période de stage et vos présences quotidiennes.') }}
                </p>
            </div>
            <div class="col-md-8 col-lg-5 fade-up">
                <div class="ndc-card p-4 p-md-5">
                    <h2 class="fw-bold text-white mb-3">
                        {{ __('Intern Login') }}
                    </h2>
                    <p class="text-secondary mb-4">
                        {{ __('Connectez-vous pour voir le statut de votre candidature.') }}
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success small">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger small">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('intern.login.submit') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Password') }}</label>
                            <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('intern.register') }}" class="small text-secondary text-decoration-none">
                                {{ __('New here?') }} <span class="text-info">{{ __('Register') }}</span>
                            </a>
                            <button type="submit" class="btn ndc-btn ndc-btn-primary">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </form>
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
        animation: pulseMove 22s ease-in-out infinite;
    }
    .hero-gradient-one {
        top: -80px;
        right: -60px;
        background: radial-gradient(circle at 40% 20%, rgba(255,255,255,0.9), rgba(148, 163, 184, 0.35));
    }
    .hero-gradient-two {
        bottom: -120px;
        left: -40px;
        width: 520px;
        height: 520px;
        background: radial-gradient(circle at 75% 25%, rgba(255,255,255,0.95), rgba(148, 163, 184, 0.3));
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
