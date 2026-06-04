<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificatePdfController extends Controller
{
    public function download(Request $request, $id)
    {
        $certificate = Certificate::with([
            'user',
            'competition',
        ])->findOrFail($id);

        // Security check
        if ($certificate->user_id != $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $pdf = Pdf::loadView(
            'certificates.template',
            compact('certificate')
        );

        return $pdf->download(
            'certificate-' .
            $certificate->certificate_number .
            '.pdf'
        );
    }
}