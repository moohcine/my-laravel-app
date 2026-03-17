<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function show(Request $request)
    {
        $intern = $request->user()->intern;

        if (! $intern || ! $intern->certificate) {
            return redirect()->route('intern.dashboard')->with('status', __('Votre certificat sera disponible une fois la période terminée.'));
        }

        return view('intern.certificate.show', [
            'intern' => $intern,
            'certificate' => $intern->certificate,
        ]);
    }
}
