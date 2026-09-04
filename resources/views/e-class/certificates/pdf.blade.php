<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $certificate->certificate_number }}</title>
    <link rel="stylesheet" href="{{ public_path('css/certificate-pdf.css') }}">
</head>
<body>
    <main class="certificate">
        <p class="brand">CURHATORIUM E-CLASS</p>
        <h1>Sertifikat Penyelesaian</h1>
        <p class="muted">Dengan bangga diberikan kepada</p>
        <h2>{{ $certificate->user->name }}</h2>
        <p class="muted">atas penyelesaian module</p>
        <h3>{{ $certificate->module->title }}</h3>
        <div class="identity">
            <p>{{ $certificate->certificate_number }}</p>
            <p>Diterbitkan {{ $certificate->issued_at->translatedFormat('d F Y') }}</p>
        </div>
        <p class="signature">Curhatorium</p>
    </main>
</body>
</html>
