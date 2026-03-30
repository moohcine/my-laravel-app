<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Intern;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Generate or refresh a certificate PDF for the given intern.
     */
    public function generateForIntern(Intern $intern, bool $force = false): ?Certificate
    {
        if (! $force && (! $intern->end_date || $intern->end_date->isFuture())) {
            return null;
        }

        $intern->loadMissing(['user', 'group', 'request']);

        $certificate = Certificate::updateOrCreate(
            ['intern_id' => $intern->id],
            [
                'issue_date'      => $intern->end_date ?? now(),
                'hours_completed' => $intern->duration_days ?? 0,
                'projects'        => __('Internship completed with the :group group.', [
                    'group' => $intern->group?->name ?? __('company'),
                ]),
                'soft_skills'     => __('Collaboration, communication, technical delivery'),
                // Explicitly brand certificates with admin signature instead of app name default.
                'signed_by'       => 'ADMIN NDC PRO',
                'notes'           => $intern->admin_note ?? __('certificate.default_note'),
                'message'         => $intern->request?->admin_notes ?? __('certificate.default_message'),
            ]
        );

        $pdf = Pdf::loadView('pdf.certificate', [
            'intern'       => $intern,
            'certificate'  => $certificate,
            'companyName'  => config('app.name', 'NDC PRO'),
        ]);

        $path = "certificates/{$intern->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $certificate->update(['pdf_path' => $path]);

        return $certificate->fresh();
    }
}
