<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Intern;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function draft(Request $request, Intern $intern)
    {
        $draft = [
            'issue_date' => $intern->end_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'hours_completed' => $intern->duration_days ?? 0,
            'projects' => __('Participation active aux projets du groupe :group.', ['group' => $intern->group->name ?? __('NDC PRO')]),
            'soft_skills' => __('Travail en équipe, pragmatisme technique, communication.'),
            'notes' => __('Mentor: ' . ($request->user()->name ?? __('NDC PRO Admin'))),
            'message' => __('Je tiens à exprimer ma profonde gratitude et mes sincères remerciements à toutes les personnes qui ont contribué, de près ou de loin, à la réalisation de ce travail. Leur soutien constant, leurs efforts considérables et leur engagement ont joué un rôle essentiel dans l’aboutissement de ce projet. Leur esprit de collaboration, leur professionnalisme et leur dévouement ont été une véritable source de motivation tout au long de ce parcours. Je tiens à valoriser le temps et l’énergie qu’ils ont investis, et j’espère pouvoir continuer à partager avec eux de futures réussites. Je leur souhaite beaucoup de succès et d’épanouissement dans leurs projets à venir.'),
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

        $validated['signed_by'] = $request->user()->name ?? 'NDC PRO';
        $validated['intern_id'] = $intern->id;

        Certificate::updateOrCreate(
            ['intern_id' => $intern->id],
            $validated
        );

        $request->session()->forget('certificate_draft');

        return back()->with('status', __('Certificate updated for :intern', ['intern' => $intern->user->name]));
    }
}
