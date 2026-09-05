<?php

namespace App\Http\Controllers;

use App\Mail\CertificateCopyMail;
use App\Models\CbtModule;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $certificates = $request->user()
            ->certificates()
            ->with('module')
            ->latest('issued_at')
            ->get();

        return view('e-class.certificates.index', compact('certificates'));
    }

    public function claim(
        Request $request,
        CbtModule $module,
        CertificateService $certificates,
    ): RedirectResponse {
        $certificate = $certificates->claim($request->user(), $module);

        return redirect()
            ->route('e-class.certificates.show', $certificate)
            ->with('success', 'Sertifikat berhasil diterbitkan.');
    }

    public function show(Request $request, Certificate $certificate): View
    {
        $this->authorizeOwner($request, $certificate);
        $certificate->load(['user', 'module']);

        return view('e-class.certificates.show', compact('certificate'));
    }

    public function download(
        Request $request,
        Certificate $certificate,
        CertificateService $certificates,
    ): StreamedResponse {
        $this->authorizeOwner($request, $certificate);
        $certificate = $certificates->ensurePdf($certificate);

        return Storage::disk('private')->download(
            (string) $certificate->pdf_path,
            'sertifikat-'.$certificate->certificate_number.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function email(
        Request $request,
        Certificate $certificate,
        CertificateService $certificates,
    ): RedirectResponse {
        $this->authorizeOwner($request, $certificate);
        $certificate = $certificates->ensurePdf($certificate);

        Mail::to($request->user()->email)->queue(new CertificateCopyMail($certificate));

        return back()->with('success', 'Salinan sertifikat dikirim ke email akun Anda.');
    }

    private function authorizeOwner(Request $request, Certificate $certificate): void
    {
        abort_unless($certificate->isOwnedBy($request->user()), 403);
    }
}
