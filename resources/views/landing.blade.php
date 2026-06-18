<x-layout title="Your Safest Place | Curhatorium" bodyClass="w-full">
    <x-slot:head>
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </x-slot:head>

    <nav class="top-0 left-0 z-50 fixed flex items-center gap-6 bg-white px-4 w-full h-16">
        <div class="flex justify-center items-center gap-2 mr-auto py-3 h-full" onclick="window.location.href = '/'">
            <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" class="h-full">
            <h1 class="text-[#222222] text-2xl">Curhatorium</h1>
        </div>

        <div class="hidden md:flex items-center gap-4">
            <a href="#about" class="hover:text-[#48A6A6] transition-colors duration-200 nav-link">Tentang</a>
            <a href="#features" class="hover:text-[#48A6A6] transition-colors duration-200 nav-link">Fitur</a>
            <a href="#testimonials" class="hover:text-[#48A6A6] transition-colors duration-200 nav-link">Cerita</a>
            <a href="#pricing" class="hover:text-[#48A6A6] transition-colors duration-200 nav-link">Harga</a>
        </div>

        <!-- Mobile login button -->
        <a href="/login"
            class="bg-[#48A6A6] hover:bg-[#48A6A6] px-4 py-2 rounded-md text-white transition-colors duration-200">Masuk</a>
    </nav>

    <!-- Hero Section -->
    <section class="bg-white px-4 pt-16 pb-4 w-full h-fit md:h-screen" id="hero">
        {{-- <div class="flex flex-col items-center bg-cover shadow-md p-4 rounded-xl w-full h-full overflow-hidden"
            style="background: #F1EDE7 radial-gradient(rgba(0,0,0,0.2) 1px, transparent 1px); background-size: 13px 13px; box-shadow: inset 0 2px 20px rgba(93,71,38,0.1);">
            --}}
            <div class="flex flex-col items-center bg-cover shadow-md p-4 rounded-xl w-full h-full overflow-hidden"
                style="background-image: url(images/background.webp)">
                <div class="flex flex-col items-center gap-4 p-6 md:p-12 text-center">
                    <h1 class="font-medium text-[#ffcc00] text-6xl md:text-9xl tracking-tight"
                        style="text-shadow: 0px 1px 2px rgba(0,0,0,0.1);">
                        Curhatorium</h1>
                    <p class="text-gray-600 text-base md:text-lg text-ellipsis">
                        Curhatorium adalah ruang aman untuk berbagi cerita, mendapatkan dukungan, dan
                        menemukan ketenangan tanpa rasa takut dihakimi. Mulai perjalananmu menuju kesehatan mental yang
                        lebih baik bersama komunitas yang peduli dan anonim.</p>
                </div>
                <div class="flex md:flex-row flex-col justify-center items-center gap-4 w-full md:w-auto">
                    <button
                        class="hover:bg-[#48A6A6] shadow-md px-4 py-2 border border-[#48A6A6] rounded-md w-full md:w-auto text-[#48A6A6] hover:text-white transition-colors duration-200"
                        onclick="window.location.href = '#about'">Kenali Lebih Lanjut</button>
                    <button
                        class="bg-[#48A6A6] hover:bg-[#357979] shadow-md px-4 py-2 rounded-md w-full md:w-auto text-white transition-colors duration-200"
                        onclick="window.location.href = '/register'">Mulai Perjalananmu</button>
                </div>
                <img src="{{ asset('images/platform.webp') }}" alt="curhatorium platform"
                    class="hidden md:block shadow-lg mt-7 rounded-md w-full max-w-full md:max-w-[70vw] object-cover">
            </div>
    </section>

    <section class="flex flex-col justify-center items-center gap-8 bg-white px-4 py-6 w-full h-fit">
        <p class="text-stone-600 md:text-lg text-center">Berkolaborasi dengan</p>
        <div class="flex flex-wrap justify-center items-center gap-6 md:gap-10 w-full">
            <img src="images/hc.webp" alt="Help Center Unair" class="h-12 md:h-16">
            <img src="images/kpmw.webp" alt="KPMW Unair" class="h-10 md:h-16 filter">
            <img src="images/upp.webp" alt="Unit Pelayanan Psikologi Unair" class="h-10 md:h-16">
            <img src="images/sdg.webp" alt="SDG Center Unair" class="h-10 md:h-16">
            <img src="images/ahpc.webp" alt="Airlangga Health Promotion Center" class="h-10 md:h-16">
            <img src="images/lpm.webp" alt="Lembaga Pengabdian Masyarakat" class="h-10 md:h-16">
            <img src="images/hji.webp" alt="Halo Jiwa Indonesia" class="h-10 md:h-16">
            <img src="images/dikti.webp" alt="Dikti" class="h-10 md:h-16">
            <img src="images/belmawa.webp" alt="Belmawa" class="h-10 md:h-16">
            <img src="images/venture.webp" alt="Venture Studio" class="h-10 md:h-16">
        </div>
    </section>

    <section class="bg-white pt-0 md:pt-24 w-full h-fit" id="statistics">
        <div class="flex flex-col items-center p-4 rounded-xl w-full h-full overflow-hidden">
            <h1 class="mb-4 font-bold text-[#222222] text-2xl md:text-3xl text-center">Curhatorium dalam Angka</h1>
            <p class="mb-6 text-gray-600 text-base md:text-lg text-center">Ratusan pengguna telah merasakan manfaat
                Curhatorium, tempat aman untuk berbagi, mendapatkan dukungan, dan tetap anonim kapan saja.</p>
            <div class="w-full">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 mt-7 border border-[#323232] rounded-lg overflow-hidden">
                    <div class="flex flex-col items-center bg-[#222222] p-8 border border-[#323232]">
                        <p class="font-bold text-white text-2xl">200+</p>
                        <p class="text-[#9ACBD0] text-lg text-center">Pengguna Terbantu</p>
                    </div>
                    <div class="flex flex-col items-center bg-[#222222] p-8 border border-[#323232]">
                        <p class="font-bold text-white text-2xl">24/7</p>
                        <p class="text-[#9ACBD0] text-lg text-center">Tersedia</p>
                    </div>
                    <div class="flex flex-col items-center bg-[#222222] p-8 border border-[#323232]">
                        <p class="font-bold text-white text-2xl">97%</p>
                        <p class="text-[#9ACBD0] text-lg text-center">Pengguna Puas</p>
                    </div>
                    <div class="flex flex-col items-center bg-[#222222] p-8 border border-[#323232]">
                        <p class="font-bold text-white text-2xl">100%</p>
                        <p class="text-[#9ACBD0] text-lg text-center">Anonim</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About Us Section -->
    <section id="about" class="bg-white px-4 py-12 md:py-24 w-full overflow-hidden">
        <div class="flex md:flex-row flex-col items-center gap-8 md:gap-12 mx-auto max-w-7xl">
            <div class="w-full md:w-1/2">
                <div class="bg-[#ffcd2d] shadow-lg p-8 rounded-lg">
                    <h3 class="mb-4 font-bold text-[#222222] text-3xl">Tentang Curhatorium</h3>
                    <p class="text-stone-800">"Curhatorium hadir sebagai ruang aman untuk generasi muda di seluruh
                        Indonesia dalam merawat kesehatan mentalnya. Kami berkomitmen menyediakan layanan yang
                        terjangkau, ramah, dan efektif didukung oleh teknologi inovatif serta pendekatan yang manusiawi.
                        Di Curhatorium, setiap perjalanan emosional layak dihargai, didengar, dan didampingi."</p>
                </div>
            </div>
            <div class="w-full md:w-1/2">
                <div class="space-y-6">
                    <div>
                        <h4 class="font-bold text-[#48A6A6] text-2xl">Visi</h4>
                        <p class="text-gray-600">Menjadi ekosistem digital kesehatan mental berbasis komunitas dan
                            gamifikasi terdepan yang inklusif, solutif, dan berkelanjutan untuk Indonesia.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#48A6A6] text-2xl">Misi</h4>
                        <ul class="space-y-2 text-gray-600 list-disc list-inside">
                            <li>Menyediakan ruang aman dan anonim bagi generasi muda untuk berbagi cerita, pengalaman,
                                dan dukungan psikologis berbasis peer-support.</li>
                            <li>Meningkatkan kesadaran dan literasi kesehatan mental melalui edukasi interaktif, fitur
                                self-assesment, dan motivasi harian.</li>
                            <li>Mengembangkan layanan konsultasi terjangkau dengan dukungan mitra profesional, mahasiswa
                                psikologi, dan komunitas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Photo Grid Section -->
    <section class="relative w-full">
        <div class="grid grid-cols-2 md:grid-cols-4">
            <div class="bg-gray-300 bg-cover bg-center aspect-[4/3]" style="background-image: url('images/gal1.jpg');">
            </div>
            <div class="bg-gray-300 bg-cover bg-center aspect-[4/3]" style="background-image: url('images/gal2.jpg');">
            </div>
            <div class="bg-gray-300 bg-cover bg-center aspect-[4/3]" style="background-image: url('images/gal3.jpg');">
            </div>
            <div class="bg-gray-300 bg-cover bg-center aspect-[4/3]" style="background-image: url('images/gal4.jpg');">
            </div>
            <div class="bg-gray-300 bg-cover bg-center aspect-[4/3]" style="background-image: url('images/gal5.jpg');">
            </div>
            <div class="bg-gray-300 bg-cover aspect-[4/3]" style="background-image: url('images/gal6.jpg');"></div>
            <div class="bg-gray-300 bg-cover bg-center aspect-[4/3]" style="background-image: url('images/gal7.jpg');">
            </div>
            <div class="bg-gray-300 bg-cover bg-center aspect-[4/3]" style="background-image: url('images/gal8.jpg');">
            </div>
        </div>
        <div class="right-0 bottom-0 left-0 absolute flex w-full h-1/2"
            style="background: linear-gradient(to top, #222222 10%, transparent 100%);"></div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-stone-200 px-4 py-12 md:py-24 w-full">
        <div class="mx-auto max-w-7xl">
            <div class="mb-12 text-center">
                <h2 class="font-bold text-[#222222] text-3xl md:text-4xl">Semua yang Anda Butuhkan</h2>
                <p class="mt-2 text-stone-600 text-lg">Dukungan kesejahteraan mental yang komprehensif, dirancang
                    khusus untuk kamu.</p>
            </div>
            <div class="gap-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                <div class="flex flex-col items-center gap-2 bg-white shadow-md p-6 rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="#48a6a6" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <h3 class="mb-2 font-bold text-[#222222] text-xl">Support Group Discussion</h3>
                    <p class="text-stone-600">Bergabung dalam grup diskusi anonim yang dipandu profesional. Berbagi
                        pengalaman dan dukungan dengan sesama.</p>
                </div>
                <div class="flex flex-col items-center gap-2 bg-white shadow-md p-6 rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="#48a6a6" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    <h3 class="mb-2 font-bold text-[#222222] text-xl">Konsultasi Profesional</h3>
                    <p class="text-stone-600">Sesi konsultas dengan peer-supporter atau psikolog profesional melalui
                        chat atau video call yang aman dan rahasia.</p>
                </div>
                <div class="flex flex-col items-center gap-2 bg-white shadow-md p-6 rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-12 text-[#48a6a6]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                    <h3 class="mb-2 font-bold text-[#222222] text-xl">Ment-AI</h3>
                    <p class="text-stone-600">Curhat dengan AI 24/7 yang memahami perasaan Anda. Dapatkan dukungan
                        emosional dan saran praktis kapan saja.</p>
                </div>
                <div class="flex flex-col items-center gap-2 bg-white shadow-md p-6 rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-12 text-[#48a6a6]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                    </svg>
                    <h3 class="mb-2 font-bold text-[#222222] text-xl">Mood and Productivity Tracker</h3>
                    <p class="text-stone-600">Pantau mood, energi, dan produktivitas harian Anda. Dapatkan analisis dan
                        feedback untuk kemajuan yang terukur.</p>
                </div>
                <div class="flex flex-col items-center gap-2 bg-white shadow-md p-6 rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="#48a6a6" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    <h3 class="mb-2 font-bold text-[#222222] text-xl">Skala Kesejahteraan Mental</h3>
                    <p class="text-stone-600">Tes kesejahteraan mental standar untuk mengenali kondisi dan kebutuhan
                        Anda.</p>
                </div>
                <div class="flex flex-col items-center gap-2 bg-white shadow-md p-6 rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="#48a6a6" class="size-12">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 8.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v8.25A2.25 2.25 0 0 0 6 16.5h2.25m8.25-8.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-7.5A2.25 2.25 0 0 1 8.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 0 0-2.25 2.25v6" />
                    </svg>
                    <h3 class="mb-2 font-bold text-[#222222] text-xl">Deep Cards</h3>
                    <p class="text-stone-600">Kartu panduan untuk memulai percakapan dan refleksi diri secara mendalam.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="relative bg-[#222222] px-4 py-12 md:py-24 w-full">
        <div class="mx-auto max-w-7xl">
            <div class="mb-12 text-center">
                <h2 class="font-bold text-stone-200 text-3xl md:text-4xl">Cerita Nyata, <span
                        class="text-[#48A6A6]">Dampak Nyata</span></h2>
                <p class="mt-2 text-stone-400 text-lg">Lihat bagaimana Curhatorium telah mengubah hidup orang seperti
                    Anda.</p>
            </div>
            <div class="gap-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                @php
                    $testimonials = [
                        [
                            'text' =>
                                'Tampilan platform Curhatorium sangat bersih dan mudah digunakan. Warnanya menenangkan, cocok untuk saat sedang merasa tidak baik.',
                            'author' => 'Rohim',
                            'role' => 'HoD Campaign & Branding Digimarly',
                        ],
                        [
                            'text' =>
                                'Saat stres karena masalah keluarga, Curhatorium menjadi tempat pelarian yang nyaman. Respon komunitas yang hangat membuatku merasa lebih tenang.',
                            'author' => 'Mutmainnah F.',
                            'role' => 'Mahasiswa Psikologi Universitas Airlangga',
                        ],
                        [
                            'text' =>
                                'Awalnya hanya iseng, tapi Curhatorium menjadi titik balik bagiku. Berbagi cerita dengan orang lain membuatku sadar bahwa aku tidak sendirian.',
                            'author' => 'Basmah',
                            'role' => 'Mahasiswa Gizi Universitas Negeri Surabaya',
                        ],
                        [
                            'text' =>
                                'Saya sulit bercerita, tapi di Curhatorium saya menemukan ruang yang membantu saya terbuka perlahan tanpa tuntutan untuk selalu "baik-baik saja".',
                            'author' => 'Ali Ridho',
                            'role' => 'Mahasiswa Teknik Universitas Negeri Surabaya',
                        ],
                        [
                            'text' =>
                                'Aku skeptis dengan layanan kesehatan mental online, tapi Curhatorium menunjukkan pendekatan yang aman, sistematis, dan nyaman untuk kami.',
                            'author' => 'Abdul Aziz',
                            'role' => 'Mahasiswa Teknik Universitas Negeri Surabaya',
                        ],
                        [
                            'text' =>
                                'Sebagai seorang introvert, Curhatorium memberiku ruang untuk bercerita dan merasa dipahami. Aku merasa didengar dan termotivasi untuk menjaga kesehatan mentalku.',
                            'author' => 'Almira',
                            'role' => 'Mahasiswa Universitas Airlangga',
                        ],
                        [
                            'text' =>
                                'Komunitas ini sangat nyaman dan suportif. Saya bisa berbagi cerita tanpa dihakimi dan merasa lebih diterima. Cocok untuk yang butuh ruang aman.',
                            'author' => 'Usaratus Sakinah',
                            'role' => 'Mahasiswa Universitas Airlangga',
                        ],
                        [
                            'text' =>
                                'Curhatorium adalah tempat yang nyaman untuk berbagi cerita tanpa takut dihakimi. Saya bisa mendapatkan saran dan sudut pandang baru dari orang lain.',
                            'author' => 'Adam',
                            'role' => 'Mahasiswa Universitas Airlangga',
                        ],
                    ];
                @endphp
                @foreach ($testimonials as $testimonial)
                    <div class="flex flex-col bg-stone-200 shadow-lg p-6 rounded-lg">
                        <p class="mb-auto text-gray-700 italic">"{{ $testimonial['text'] }}"</p>
                        <div class="text-right">
                            <p class="font-bold text-[#222222]">{{ $testimonial['author'] }}</p>
                            <p class="text-gray-500 text-sm">{{ $testimonial['role'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="right-0 bottom-0 left-0 absolute flex w-full h-1/2"
            style="background: linear-gradient(to top, #222222 10%, transparent 100%);"></div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="bg-stone-200 px-4 py-12 md:py-24 w-full">
        <div class="mx-auto max-w-7xl">
            <div class="mb-12 text-center">
                <h2 class="font-bold text-[#222222] text-3xl md:text-4xl">Paket & Harga</h2>
                <p class="mt-2 text-stone-600 text-lg">Pilih paket membership sesuai kebutuhanmu.</p>
            </div>
            <div class="gap-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    @php
                        // Add "Terpopuler" badge to a specific plan if needed
                        $isTerpopuler = strtolower($plan->name) === 'serenity';
                        $badge = $isTerpopuler ? 'Terpopuler' : null;
                    @endphp
                    <div
                        class="bg-white p-6 rounded-lg shadow-md ease-in-out flex flex-col{{ $isTerpopuler ? ' border-2 border-[#48A6A6]' : '' }}">
                        @if ($badge)
                            <div class="self-center bg-[#48A6A6] mb-4 px-3 py-1 rounded-full font-bold text-white text-sm">
                                {{ $badge }}
                            </div>
                        @endif
                        <h3 class="mb-4 font-bold text-[#222222] text-2xl text-center">{{ $plan->name }}</h3>
                        <p class="mb-6 font-bold text-[#48A6A6] text-4xl text-center">
                            {{ $plan->price_idr > 0 ? $plan->getPriceInIDR() : 'Gratis' }}
                        </p>
                        <ul class="flex-grow space-y-3 mb-6 text-gray-600 text-left">
                            <li class="flex justify-between items-center gap-2 pb-2 border-gray-100 border-b">
                                <span><span class="mr-2 text-[#ffcd2d]">✦</span>MHC-SF</span>
                                <span class="font-medium">&#8734;</span>
                            </li>
                            <li class="flex justify-between items-center gap-2 pb-2 border-gray-100 border-b">
                                <span><span class="mr-2 text-[#ffcd2d]">✦</span>Mood Tracker</span>
                                <span class="font-medium">&#8734;</span>
                            </li>
                            <li class="flex justify-between items-center gap-2 pb-2 border-gray-100 border-b">
                                <span><span class="mr-2 text-[#ffcd2d]">✦</span>Daily Missions</span>
                                <span class="font-medium">&#8734;</span>
                            </li>
                            <li class="flex justify-between items-center gap-2 pb-2 border-gray-100 border-b">
                                <span><span class="mr-2 text-[#ffcd2d]">✦</span>Deep Cards</span>
                                <span class="font-medium">&#8734;</span>
                            </li>
                            @foreach ($plan->planBenefits as $benefit)
                                @if ($benefit->amount >= 1 && $benefit->benefit != 'ai_window_token')
                                    <li class="flex justify-between items-center gap-2 pb-2 border-gray-100 border-b">
                                        <span><span class="mr-2 text-[#ffcd2d]">✦</span>{{ $benefit->benefit }}</span>
                                        <span class="font-medium text-right">{{ $benefit->amount }}</span>
                                    </li>
                                @endif
                            @endforeach
                            <li class="flex justify-between items-center gap-2 pt-1">
                                <span><span class="mr-2 text-[#ffcd2d]">✦</span>Ment-AI</span>
                                <span class="font-medium text-sm text-right">
                                    @if ($plan->name == 'Free')
                                        Base limit
                                    @elseif ($plan->name == 'Calm')
                                        Extended limit
                                    @else
                                        Longer limit
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-cover bg-center px-2 md:px-4 py-12 md:py-20 w-full overflow-hidden"
        style="background-image: url('{{ asset('images/background.jpg') }}');">
        <div class="bg-white/80 shadow-xl backdrop-blur-sm mx-auto p-4 md:p-10 rounded-lg max-w-4xl text-center">
            <h2 class="mb-4 font-bold text-[#222222] text-2xl md:text-4xl">Siap Memulai Perjalanan Kesehatan Mentalmu?
            </h2>
            <p class="mb-8 text-gray-700 text-base md:text-lg">Bergabunglah dengan komunitas kami dan temukan ruang
                aman untuk bertumbuh.</p>
            <a href="/register"
                class="block bg-[#48A6A6] hover:bg-[#357979] mx-auto mb-4 md:mb-0 px-4 md:px-8 py-2 md:py-3 rounded-md w-fit text-white text-base md:text-lg transition-colors duration-200">Gabung
                Sekarang</a>
            <div class="flex md:flex-row flex-col justify-center items-center gap-4 md:gap-8 mt-8 text-gray-600">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 md:size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Gratis selamanya
                </span>
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 md:size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    100% Anonim
                </span>
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 md:size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Tersedia 24/7
                </span>
            </div>
        </div>
    </section>
    @include('components.footer')

    <!-- Fixed Tutorial Button -->
    <button id="tutorial-btn" style="position: fixed; bottom: 1rem; right: 1rem; z-index: 50; background-color: #48A6A6; color: white;
            padding: 0.75rem; border-radius: 50%; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
            0 4px 6px -2px rgba(0, 0, 0, 0.05); border: none; cursor: pointer; transition: all 0.3s ease;
            display: flex; align-items: center; gap: 0.5rem; overflow: hidden; width: 3rem; height: 3rem;"
        onmouseover="this.style.backgroundColor = '#357979'; this.style.width = 'auto'; this.style.borderRadius = '2rem'; this.style.padding = '0.75rem 1rem'; this.querySelector('span').style.opacity = '1';"
        onmouseout="this.style.backgroundColor = '#48A6A6'; this.style.width = '3rem'; this.style.borderRadius = '50%'; this.style.padding = '0.75rem'; this.querySelector('span').style.opacity = '0';">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            style="width: 1.5rem; height: 1.5rem; flex-shrink: 0;">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
        </svg>
        <span style="opacity: 0; transition: opacity 0.3s ease; white-space: nowrap; color: white;">Tutorial</span>
    </button>

    <!-- Tutorial Modal -->
    <div id="tutorial-modal" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5);
            display: none; align-items: center; justify-content: center; z-index: 60;">
        <div style="background-color: white; border-radius: 1rem; width: 90%; max-width: 900px; 
                    padding: 1.5rem; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <button id="close-modal" style="position: absolute; top: 1rem; right: 1rem; color: #6b7280; border: none;
                    background: none; cursor: pointer; transition: color 0.2s;"
                onmouseover="this.style.color = '#374151'" onmouseout="this.style.color = '#6b7280'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" style="width: 1.75rem; height: 1.75rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Responsive video container -->
            <div
                style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 0.5rem; margin-top: 2rem; border: 2px solid #e5e7eb;">
                <iframe id="tutorial-video" src="" frameborder="0" allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                        border: none; border-radius: 0.5rem;">
                </iframe>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            const btn = document.getElementById('tutorial-btn');
            const modal = document.getElementById('tutorial-modal');
            const close = document.getElementById('close-modal');
            const video = document.getElementById('tutorial-video');

            btn.addEventListener('click', () => {
                modal.style.display = 'flex';
                video.src = "https://www.youtube.com/embed/C2pjXtknQSI?si=icjaTmb1-iZbrjSl";
            });

            close.addEventListener('click', () => {
                modal.style.display = 'none';
                video.src = ""; // stop video when closing
            });

            // Optional: close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    video.src = "";
                }
            });
        </script>
    </x-slot:scripts>
</x-layout>