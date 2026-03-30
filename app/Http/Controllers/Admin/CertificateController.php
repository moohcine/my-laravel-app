<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Intern;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function __construct(protected CertificateService $certificateService)
    {
    }

    public function draft(Request $request, Intern $intern)
    {
        $draft = [
            'issue_date' => $intern->end_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'hours_completed' => $intern->duration_days ?? 0,
            'projects' => __('Participation active aux projets du groupe :group.', ['group' => $intern->group->name ?? __('NDC PRO')]),
            'soft_skills' => __('Travail en Ã©quipe, pragmatisme technique, communication.'),
            'notes' => __('certificate.default_note'),
            'message' => __('certificate.default_message'),
        ];

        $request->session()->flash('certificate_draft', $draft);

        return back()->with('status', __('Draft certificate values prepared. Edit as needed and save.'));
    }

    public function store(Request $request, Intern $intern)
    {
        $validated = $request->validate([
            'issue_date' => ['required', 'date'],
            'hours_completed' => ['required', 'integer', 'min:1'],
            'projects' => ['required', 'string'],
            'soft_skills' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['message'] = $validated['message'] ?: __('certificate.default_message');
        $validated['notes'] = $validated['notes'] ?: __('certificate.default_note');
        // Always stamp certificates with the requested signature label.
        $validated['signed_by'] = 'ADMIN NDC PRO';
        $validated['intern_id'] = $intern->id;

        Certificate::updateOrCreate(
            ['intern_id' => $intern->id],
            $validated
        );

        $this->certificateService->generateForIntern($intern->fresh(), true);

        $request->session()->forget('certificate_draft');

        return back()->with('status', __('Certificate updated for :intern', ['intern' => $intern->user->name]));
    }

    public function download(Intern $intern)
    {
        $certificate = $intern->certificate ?? $this->certificateService->generateForIntern($intern, true);

        if (! $certificate || ! $certificate->pdf_path) {
            return back()->with('status', __('Certificate PDF not ready yet.'));
        }

        if (! Storage::disk('public')->exists($certificate->pdf_path)) {
            $certificate = $this->certificateService->generateForIntern($intern, true);
        }

        return Storage::disk('public')->download(
            $certificate->pdf_path,
            'certificate-' . str_replace(' ', '-', strtolower($intern->user->name)) . '.pdf'
        );
    }
}


