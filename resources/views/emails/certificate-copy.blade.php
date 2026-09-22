<x-mail::message>
# Sertifikat E-Class Anda

Halo {{ $certificate->user->name }},

Selamat telah menyelesaikan module **{{ $certificate->module->title }}**. Salinan sertifikat bernomor **{{ $certificate->certificate_number }}** terlampir pada email ini.

<x-mail::button :url="route('e-class.certificates.show', $certificate)">
Lihat Sertifikat
</x-mail::button>

Salam,<br>
Curhatorium
</x-mail::message>
