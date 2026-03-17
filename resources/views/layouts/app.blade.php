<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', __('NDC PRO Internship System'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --ndc-bg: #f6f6f7;
            --ndc-surface: #ffffff;
            --ndc-border: #e5e7eb;
            --ndc-text: #1f2933;
            --ndc-muted: #6c727f;
            --ndc-accent: #3b82f6;
            --ndc-accent-2: #3b82f6;
        }
        body {
        background: #f6f6f7;
            color: var(--ndc-text);
            min-height: 100vh;
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0.01em;
        }
        .ndc-card {
            background: var(--ndc-surface);
            border-radius: 1.1rem;
            border: 1px solid var(--ndc-border);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }
        .ndc-card:hover {
            transform: translateY(-2px);
            border-color: rgba(0, 0, 0, 0.06);
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.08);
        }
        .ndc-btn {
            border-radius: 999px;
            padding: 0.78rem 1.55rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            transition: all 0.18s ease;
        }
        .ndc-btn-primary {
            background: var(--ndc-accent);
            border: none;
            color: #ffffff;
            box-shadow: none;
        }
        .ndc-btn-primary:hover {
            transform: translateY(-1px) scale(1.005);
            box-shadow: 0 10px 18px rgba(59, 130, 246, 0.18);
        }
        .ndc-btn-outline {
            border: 1px solid var(--ndc-border);
            color: var(--ndc-text);
            background: var(--ndc-surface);
        }
        .ndc-btn-outline:hover {
            background: #f2f3f5;
            border-color: rgba(31,41,51,0.15);
            transform: translateY(-1px);
        }
        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.8s ease forwards;
        }
        .fade-up-delayed {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.9s ease 0.12s forwards;
        }
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Light tables, even when paired with table-dark class */
        .table-dark-modern {
            --bs-table-bg: var(--ndc-surface);
            --bs-table-striped-bg: #f6f7fb;
            --bs-table-striped-color: var(--ndc-text);
            --bs-table-color: var(--ndc-text);
            --bs-table-hover-bg: #eef2ff;
        }
        /* Keep text-white readable on light cards */
        .text-white { color: var(--ndc-text) !important; }
        .text-secondary { color: var(--ndc-muted) !important; }
        /* Nav pills */
        .nav-pill {
            color: #334155;
            padding: 8px 12px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.18s ease;
        }
        .nav-pill:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .nav-pill.active {
            background: linear-gradient(135deg,var(--ndc-accent),var(--ndc-accent-2));
            color: #fff;
        box-shadow: 0 10px 28px rgba(37,99,235,0.18);
    }
    .app-header {
        background: rgba(255, 255, 255, 0.78);
        backdrop-filter: blur(14px);
        border-color: rgba(226, 232, 240, 0.8);
    }
    </style>

    @stack('styles')
</head>
<body>
<div class="d-flex flex-column min-vh-100">
    <header class="border-bottom app-header" style="box-shadow:0 6px 24px rgba(15,23,42,0.06);">
        <div class="container py-3 d-flex align-items-center justify-content-between">
            @php
                $locales = config('app.locales', ['en' => 'English']);
                $currentLocale = app()->getLocale();
            @endphp
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-primary">{{ __('NDC PRO') }}</span>
                <span class="text-secondary small">{{ __('Internship Management') }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(!request()->routeIs('home'))
                    <a href="{{ route('home') }}" class="btn btn-sm ndc-btn-outline">{{ __('Home') }}</a>
                @endif
                <div class="dropdown">
                    <button class="btn btn-sm ndc-btn-outline d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-translate"></i>
                        <span class="text-uppercase">{{ $currentLocale }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach($locales as $locale => $label)
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center{{ $currentLocale === $locale ? ' active' : '' }}" href="{{ route('locale.switch', $locale) }}">
                                    <span>{{ $label }}</span>
                                    @if($currentLocale === $locale)
                                        <i class="bi bi-check2"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @auth
                    <form method="POST" action="{{ auth()->user()->role === 'admin' ? route('admin.logout') : route('intern.logout') }}">
                        @csrf
                        <button class="btn btn-sm ndc-btn-outline">{{ __('Logout') }}</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-fill">
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
