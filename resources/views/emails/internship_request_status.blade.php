<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Internship application status') }}</title>
</head>
<body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background-color:#0f172a; color:#e5e7eb; padding:24px;">
<div style="max-width:600px;margin:0 auto;background-color:#020617;border-radius:16px;padding:24px;border:1px solid #1f2937;">
    <h2 style="margin-top:0;color:#e5e7eb;">{{ __('NDC PRO – Internship application') }}</h2>
    <p style="color:#9ca3af;">{{ __('Hello :name,', ['name' => $request->user->name]) }}</p>
    <p style="color:#9ca3af;">
        {{ __('The status of your internship application has been updated to:') }}
    </p>
    <p style="font-size:18px;font-weight:600;">
        @if($request->status === 'accepted')
            <span style="color:#4ade80;">{{ __('status.accepted') }}</span>
        @elseif($request->status === 'rejected')
            <span style="color:#f97373;">{{ __('status.rejected') }}</span>
        @else
            <span style="color:#facc15;">{{ __('status.pending') }}</span>
        @endif
    </p>
    @if ($request->admin_notes)
    <p style="color:#9ca3af;">{{ __('Admin notes:') }}</p>
        <blockquote style="border-left:3px solid #1d4ed8;margin:0;padding-left:12px;color:#e5e7eb;">
            {{ $request->admin_notes }}
        </blockquote>
    @endif
    <p style="color:#9ca3af;margin-top:24px;">
        {{ __('You can log in to your intern dashboard to see more details.') }}
    </p>
    <p style="color:#6b7280;font-size:12px;margin-top:24px;">
        {{ __('This is an automated message from the NDC PRO internship management system.') }}
    </p>
</div>
</body>
</html>
