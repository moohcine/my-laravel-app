@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', __('Certificate – NDC PRO'))

@section('content')
<div class="certificate-page">
    <div class="certificate-header text-center">
        <h2 class="fw-bold text-white mb-2">{{ __('Certificate of Completion') }}</h2>
        <p class="text-secondary opacity-75">
            {{ __('Issued by NDC Pro Maroc to the intern completing the program at NDC PRO.') }}
        </p>
    </div>
    <div class="ndc-card certificate-card certificate-print p-5">
        <p class="text-uppercase text-muted small mb-1">{{ __('Intern') }}</p>
        <h3 class="text-white fw-semibold">{{ $intern->user->name }}</h3>
        <div class="d-flex flex-column flex-md-row gap-3 mb-3 text-secondary small">
            <span>
                <i class="bi bi-envelope-fill me-1"></i>
                <a href="mailto:{{ $intern->user->email }}" class="text-secondary text-decoration-none">{{ $intern->user->email }}</a>
            </span>
            <span>
                <i class="bi bi-telephone-fill me-1"></i>
                {{ $intern->request?->phone ?? __('N/A') }}
            </span>
            <span>
                <i class="bi bi-mortarboard-fill me-1"></i>
                {{ $intern->request?->school ?? __('N/A') }}
            </span>
        </div>
        <p class="text-secondary">
            {{ __('Internship period') }}:
            {{ $intern->start_date?->format('d M Y') ?? __('N/A') }} –
            {{ $intern->end_date?->format('d M Y') ?? __('N/A') }}
        </p>
        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <p class="text-muted small mb-1">{{ __('Hours completed') }}</p>
                <p class="text-white fs-4 fw-semibold">{{ $certificate->hours_completed }}</p>
            </div>
            <div class="col-md-4">
                <p class="text-muted small mb-1">{{ __('Projects') }}</p>
                <p class="text-white">{{ Str::limit($certificate->projects, 60) }}</p>
            </div>
            <div class="col-md-4">
                <p class="text-muted small mb-1">{{ __('Soft skills') }}</p>
                <p class="text-white">{{ $certificate->soft_skills ?? __('N/A') }}</p>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-muted small">{{ __('Mentor notes') }}</p>
            <p class="text-white">{{ $certificate->notes ?? __('No additional notes.') }}</p>
        </div>
        @if($certificate->message)
            <div class="mt-3">
                <p class="text-muted small">{{ __('Message') }}</p>
                <p class="text-white">{{ $certificate->message }}</p>
            </div>
        @endif
        <div class="pt-4 mt-4 border-top border-secondary d-flex flex-column flex-md-row justify-content-between">
            <div>
                <p class="text-muted small mb-1">{{ __('Signed by') }}</p>
                <p class="text-white fs-5">{{ $certificate->signed_by }}</p>
            </div>
            <div class="text-md-end">
                <p class="text-muted small mb-1">{{ __('Issued on') }}</p>
                <p class="text-white fs-5">{{ $certificate->issue_date->format('d M Y') }}</p>
            </div>
        </div>
    </div>
</div>
<div class="certificate-actions text-center mt-4 no-print">
    <a href="{{ route('intern.dashboard') }}" class="btn ndc-btn ndc-btn-outline me-2">
        {{ __('Back to dashboard') }}
    </a>
    @if($certificate->pdf_path)
        <a href="{{ route('intern.certificate.download') }}" class="btn ndc-btn ndc-btn-outline me-2">
            {{ __('Download PDF') }}
        </a>
    @endif
    <button onclick="window.print()" class="btn ndc-btn ndc-btn-primary">
        {{ __('Print certificate') }}
    </button>
</div>
</div>
@endsection

@push('styles')
<style>
    .certificate-page {
        min-height: 100vh;
        padding: 4rem 1rem;
        background: linear-gradient(135deg, #d7ddea, #dce0e8 45%, #e6dede 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .certificate-header {
        max-width: 640px;
        width: 100%;
        margin-bottom: 2rem;
    }
    .certificate-card {
        width: min(900px, 100%);
        background: radial-gradient(circle at top left, rgba(255,255,255,0.15), rgba(255,255,255,0));
        border: 1px solid rgba(81, 78, 78, 0.2);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.7);
        border-radius: 2rem;
        position: relative;
        overflow: hidden;
    }
    .certificate-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        pointer-events: none;
        background: linear-gradient(120deg, rgba(255,255,255,0.08), transparent 45%);
    }
    .certificate-card > * {
        position: relative;
        z-index: 2;
    }
    .certificate-card small {
        letter-spacing: 0.1em;
    }
    .certificate-card .row > div {
        border-right: 1px solid rgba(255,255,255,0.08);
    }
    .certificate-card .row > div:last-child {
        border-right: none;
    }
    .certificate-actions .btn {
        min-width: 180px;
    }
    @media (max-width: 768px) {
        .certificate-card .row > div {
            border-right: none;
        }
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .certificate-print,
        .certificate-print * {
            visibility: visible;
        }
        .certificate-print {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            box-shadow: none !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush
