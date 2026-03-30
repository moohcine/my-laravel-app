<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; background: #f7f7fb; color: #0f172a; margin: 0; padding: 40px; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; }
        .title { text-transform: uppercase; letter-spacing: 2px; color: #475569; font-size: 12px; margin-bottom: 6px; }
        h1 { margin: 0 0 8px; font-size: 26px; color: #0f172a; }
        .meta { margin: 6px 0; color: #475569; }
        .section { margin-top: 18px; }
        .section h3 { margin: 0 0 6px; font-size: 14px; color: #111827; }
        .section p { margin: 0; line-height: 1.5; color: #334155; }
        .footer { margin-top: 28px; display: flex; justify-content: space-between; font-size: 12px; color: #475569; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; background: #e0f2fe; color: #0369a1; font-weight: 600; font-size: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="title">{{ __('Certificate of Completion') }}</div>
        <h1>{{ $companyName ?? config('app.name', 'Company') }}</h1>
        <div class="meta">{{ __('This certificate is proudly presented to') }}</div>
        <div style="font-size:20px; font-weight:700; color:#0f172a;">{{ $intern->user->name }}</div>
        <div class="meta">
            {{ __('Filière') }}: {{ $intern->group?->filiere ?? $intern->request?->filiere ?? __('N/A') }} |
            {{ __('Internship duration') }}:
            {{ optional($intern->start_date)->format('d M Y') }} – {{ optional($intern->end_date)->format('d M Y') }}
        </div>

        <div class="section">
            <h3>{{ __('Company') }}</h3>
            <p>{{ $companyName ?? config('app.name', 'Company') }}</p>
        </div>

        <div class="section">
            <h3>{{ __('Projects / contributions') }}</h3>
            <p>{{ $certificate->projects }}</p>
        </div>

        @if($certificate->soft_skills)
            <div class="section">
                <h3>{{ __('Soft skills') }}</h3>
                <p>{{ $certificate->soft_skills }}</p>
            </div>
        @endif

        @if($certificate->notes)
            <div class="section">
                <h3>{{ __('Mentor notes') }}</h3>
                <p>{{ $certificate->notes }}</p>
            </div>
        @endif

        @if($certificate->message)
            <div class="section">
                <h3>{{ __('Message') }}</h3>
                <p>{{ $certificate->message }}</p>
            </div>
        @endif

        <div class="footer">
            <div>
                {{ __('Signed by') }}: <strong>{{ $certificate->signed_by }}</strong><br>
                {{ __('Issued on') }}: {{ optional($certificate->issue_date)->format('d M Y') }}
            </div>
            <div class="badge">{{ __('Hours completed') }}: {{ $certificate->hours_completed }}</div>
        </div>
    </div>
</body>
</html>
