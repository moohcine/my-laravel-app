<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(protected CertificateService $certificateService)
    {
    }

    public function show(Request $request)
    {
        $intern = $request->user()->intern;

        if ($intern && ! $intern->certificate && $intern->end_date && ! $intern->end_date->isFuture()) {
            $this->certificateService->generateForIntern($intern);
            $intern->refresh();
        }

        if (! $intern || ! $intern->certificate) {
            return redirect()->route('intern.dashboard')->with('status', __('Votre certificat sera disponible une fois la période terminée.'));
        }

        return view('intern.certificate.show', [
            'intern' => $intern,
            'certificate' => $intern->certificate,
        ]);
    }

    public function download(Request $request)
    {
        $intern = $request->user()->intern;

        if (! $intern || ! $intern->certificate || ! $intern->certificate->pdf_path) {
            return redirect()->route('intern.dashboard')->with('status', __('Certificate not available yet.'));
        }

        $path = $intern->certificate->pdf_path;
        if (! Storage::disk('public')->exists($path)) {
            $this->certificateService->generateForIntern($intern, true);
        }

        return Storage::disk('public')->download(
            $intern->certificate->pdf_path,
            'certificate-' . str_replace(' ', '-', strtolower($intern->user->name)) . '.pdf'
        );
    }
}
