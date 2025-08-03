<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatat Mood & Produktivitas - Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tracker/index.css') }}">
    <style>
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>Pencatat Mood & Produktivitas</h1>
                <p>
                    Catat suasana hati, aktivitas, dan tingkat energi harianmu untuk memahami pola kesehatan mental dan meningkatkan kesejahteraanmu.
                </p>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-indicator">
            <div class="progress-step active" id="step1"></div>
            <div class="progress-step" id="step2"></div>
            <div class="progress-step" id="step3"></div>
            <div class="progress-step" id="step4"></div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="form-card">
                <form id="moodTrackingForm" method="POST" action="{{ route('tracker.entry') }}">
                    @csrf
                    <!-- 1. Mood Scale -->
                    <div class="form-section">
                        <h2 class="section-title">Bagaimana perasaanmu hari ini?</h2>
                        <p class="section-description">
                            Pilih emoji yang paling menggambarkan suasana hatimu hari ini. Ini membantu kami memahami keadaan emosimu.
                        </p>
                        <div class="mood-scale">
                            @php
                                $moods = [
                                    1 => ['label' => 'Sangat sedih'],
                                    2 => ['label' => 'Sedih'],
                                    3 => ['label' => 'Murung'],
                                    4 => ['label' => 'Biasa'],
                                    5 => ['label' => 'Netral'],
                                    6 => ['label' => 'Positif'],
                                    7 => ['label' => 'Senang'],
                                    8 => ['label' => 'Sangat senang'],
                                    9 => ['label' => 'Bahagia'],
                                    10 => ['label' => 'Gembira'],
                                ];
                            @endphp
                            @for ($i = 1; $i <= 10; $i++)
                                <label class="mood-option" data-mood="{{ $i }}">
                                    <input type="radio" name="mood" value="{{ $i }}">
                                    <div class="mood-emoji">
                                        <img 
                                            src="{{ asset('assets/emoji/' . $i . '.png') }}" 
                                            alt="{{ $moods[$i]['label'] }}" 
                                            style="width: 2.5rem; height: 2.5rem; object-fit: contain; display: block; margin: 0 auto;"
                                            class="mood-emoji-img"
                                        >
                                    </div>
                                    <div class="mood-label">{{ $moods[$i]['label'] }}</div>
                                    <div class="mood-number">{{ $i }}</div>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- 2. Activity Selection -->
                    <div class="form-section">
                        <h2 class="section-title">Aktivitas apa yang paling memengaruhi mood-mu hari ini?</h2>
                        <p class="section-description">
                            Pilih aktivitas utama atau pengalaman yang paling memengaruhi suasana hatimu hari ini. Ini membantu mengidentifikasi pola dalam kehidupan sehari-hari.
                        </p>
                        <div class="activity-grid">
                            @php
                                $activities = [
                                    'work' => ['icon' => '💼', 'name' => 'Pekerjaan & Karir', 'desc' => 'Rapat, presentasi, bisnis'],
                                    'exercise' => ['icon' => '🏃‍♂️', 'name' => 'Aktivitas Fisik', 'desc' => 'Jalan kaki, senam, gym, yoga'],
                                    'social' => ['icon' => '💬', 'name' => 'Sosialisasi', 'desc' => 'Berkumpul, hangout'],
                                    'hobbies' => ['icon' => '🎨', 'name' => 'Kreativitas & Hobi', 'desc' => 'Menggambar, melukis, mendesain'],
                                    'rest' => ['icon' => '🎧', 'name' => 'Hiburan & Santai', 'desc' => 'Game, film, scrolling'],
                                    'entertainment' => ['icon' => '🛁', 'name' => 'Perawatan Diri', 'desc' => 'Skincare, potong rambut, mandi'],
                                    'nature' => ['icon' => '🌳', 'name' => 'Aktivitas Luar Ruangan', 'desc' => 'Jalan pagi, berjemur, piknik'],
                                    'food' => ['icon' => '🏠', 'name' => 'Rumah Tangga', 'desc' => 'Memasak, menyapu, mencuci baju'],
                                    'health' => ['icon' => '🧘', 'name' => 'Kesehatan Mental', 'desc' => 'Meditasi, menulis, terapi'],
                                    'study' => ['icon' => '📖', 'name' => 'Belajar & Produktivitas', 'desc' => 'Membaca, belajar, mengerjakan tugas'],
                                    'spiritual' => ['icon' => '🙏', 'name' => 'Spiritual', 'desc' => 'Berdoa, kajian, membaca kitab suci'],
                                    'romance' => ['icon' => '💖', 'name' => 'Hubungan Romantis', 'desc' => 'Kencan, quality time, merayakan momen'],
                                    'finance' => ['icon' => '📊', 'name' => 'Finansial & Mandiri', 'desc' => 'Mencatat keuangan, investasi, membayar tagihan'],
                                    'other' => ['icon' => '🧩', 'name' => 'Lainnya', 'desc' => 'Sesuatu yang lain'],
                                ];
                            @endphp
                            @foreach ($activities as $key => $activity)
                                <label class="activity-option" data-activity="{{ $key }}">
                                    <input type="radio" name="activity" value="{{ $key }}">
                                    <div class="activity-icon">{{ $activity['icon'] }}</div>
                                    <div class="activity-info">
                                        <div class="activity-name">{{ $activity['name'] }}</div>
                                        <div class="activity-description">{{ $activity['desc'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Activity Explanation -->
                    <div class="form-section">
                        <h2 class="section-title">Ceritakan lebih lanjut tentang aktivitas ini</h2>
                        <p class="section-description">
                            Jelaskan secara spesifik apa yang terjadi selama aktivitas ini yang memengaruhi suasana hatimu. Ceritakan sedetail mungkin sesuai keinginanmu.
                        </p>
                        <div class="form-group">
                            <label for="activityExplanation" class="form-label">Detail Aktivitas</label>
                            <textarea 
                                id="activityExplanation" 
                                name="activityExplanation" 
                                class="form-textarea"
                                placeholder="Ceritakan apa yang terjadi selama aktivitas ini yang memengaruhi mood-mu. Contoh: 'Latihan di gym berjalan lancar, merasa berenergi dan puas setelah menyelesaikan rutinitas' atau 'Rapat kerja yang sulit dengan banyak masukan, merasa stres dan kewalahan setelahnya'..."
                                maxlength="500"
                            ></textarea>
                            <div class="char-counter">
                                <span id="charCount">0</span>/500 karakter
                            </div>
                        </div>
                    </div>

                    <!-- 4. Energy Sliders -->
                    <div class="form-section">
                        <h2 class="section-title">Bagaimana aktivitas ini memengaruhi energimu?</h2>
                        <p class="section-description">
                            Nilai tingkat energi dan produktivitasmu selama dan setelah aktivitas ini. Ini membantu kami memahami bagaimana aktivitas memengaruhi kesejahteraanmu.
                        </p>
                        <div class="slider-container">
                            <div class="slider-group">
                                <div class="slider-label">
                                    <div class="slider-title">
                                        ⚡ Tingkat Energi
                                        <span>(Seberapa berenergi kamu?)</span>
                                    </div>
                                    <div class="slider-value" id="energyValue">5</div>
                                </div>
                                <div class="slider-wrapper">
                                    <input type="range" id="energySlider" class="slider" min="1" max="10" value="5" name="energy">
                                    <div class="slider-track" id="energyTrack"></div>
                                </div>
                                <div class="slider-labels">
                                    <span>Sangat Rendah</span>
                                    <span>Rendah</span>
                                    <span>Sedang</span>
                                    <span>Tinggi</span>
                                    <span>Sangat Tinggi</span>
                                </div>
                            </div>

                            <div class="slider-group">
                                <div class="slider-label">
                                    <div class="slider-title">
                                        🎯 Tingkat Produktivitas
                                        <span>(Seberapa produktif kamu?)</span>
                                    </div>
                                    <div class="slider-value" id="productivityValue">5</div>
                                </div>
                                <div class="slider-wrapper">
                                    <input type="range" id="productivitySlider" class="slider" min="1" max="10" value="5" name="productivity">
                                    <div class="slider-track" id="productivityTrack"></div>
                                </div>
                                <div class="slider-labels">
                                    <span>Sangat Rendah</span>
                                    <span>Rendah</span>
                                    <span>Sedang</span>
                                    <span>Tinggi</span>
                                    <span>Sangat Tinggi</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="submit-section">
                        <button type="submit" class="submit-btn" id="submitBtn">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        // State Formulir
        let formData = {
            mood: null,
            activity: null,
            explanation: '',
            energy: 5,
            productivity: 5,
            timestamp: null
        };

        // Elemen DOM
        const moodOptions = document.querySelectorAll('.mood-option');
        const activityOptions = document.querySelectorAll('.activity-option');
        const explanationTextarea = document.getElementById('activityExplanation');
        const charCount = document.getElementById('charCount');
        const energySlider = document.getElementById('energySlider');
        const productivitySlider = document.getElementById('productivitySlider');
        const energyValue = document.getElementById('energyValue');
        const productivityValue = document.getElementById('productivityValue');
        const energyTrack = document.getElementById('energyTrack');
        const productivityTrack = document.getElementById('productivityTrack');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('moodTrackingForm');

        // Langkah Progres
        const progressSteps = {
            step1: document.getElementById('step1'),
            step2: document.getElementById('step2'),
            step3: document.getElementById('step3'),
            step4: document.getElementById('step4')
        };

        // Pilihan Mood
        moodOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Hapus seleksi sebelumnya
                moodOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Tambahkan seleksi pada yang diklik
                this.classList.add('selected');
                
                // Pilih radio button
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
                
                // Update data form
                formData.mood = parseInt(this.dataset.mood);
                
                // Update progres
                updateProgress();
            });
        });

        // Pilihan Aktivitas
        activityOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Hapus seleksi sebelumnya
                activityOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Tambahkan seleksi pada yang diklik
                this.classList.add('selected');
                
                // Pilih radio button
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
                
                // Update data form
                formData.activity = this.dataset.activity;
                
                // Update progres
                updateProgress();
            });
        });

        // Textarea Penjelasan
        explanationTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Update data form
            formData.explanation = this.value;
            
            // Update progres
            updateProgress();
        });

        // Slider Energi
        energySlider.addEventListener('input', function() {
            const value = this.value;
            energyValue.textContent = value;
            energyTrack.style.width = ((value - 1) / 9) * 100 + '%';
            
            // Update data form
            formData.energy = parseInt(value);
            
            // Update progres
            updateProgress();
        });

        // Slider Produktivitas
        productivitySlider.addEventListener('input', function() {
            const value = this.value;
            productivityValue.textContent = value;
            productivityTrack.style.width = ((value - 1) / 9) * 100 + '%';
            
            // Update data form
            formData.productivity = parseInt(value);
            
            // Update progres
            updateProgress();
        });

        // Update indikator progres
        function updateProgress() {
            // Reset semua langkah
            Object.values(progressSteps).forEach(step => {
                step.classList.remove('active', 'completed');
            });

            // Langkah 1: Mood dipilih
            if (formData.mood !== null) {
                progressSteps.step1.classList.add('completed');
                progressSteps.step2.classList.add('active');
            } else {
                progressSteps.step1.classList.add('active');
                return;
            }

            // Langkah 2: Aktivitas dipilih
            if (formData.activity !== null) {
                progressSteps.step2.classList.add('completed');
                progressSteps.step3.classList.add('active');
            } else {
                return;
            }

            // Langkah 3: Penjelasan diisi (opsional tapi dianjurkan)
            if (formData.explanation.trim().length > 0) {
                progressSteps.step3.classList.add('completed');
                progressSteps.step4.classList.add('active');
            } else {
                return;
            }

            // Langkah 4: Slider (selalu ada nilai default)
            progressSteps.step4.classList.add('completed');

            // Aktifkan tombol submit jika syarat minimal terpenuhi
            updateSubmitButton();
        }

        // Update status tombol submit
        function updateSubmitButton() {
            const isValid = formData.mood !== null && formData.activity !== null;
            submitBtn.disabled = !isValid;
        }

        // Pengiriman form ditangani oleh Laravel
        // Form akan dikirim ke server dan diarahkan kembali dengan pesan sukses/error

        // Form submission is handled by Laravel redirect
    </script>
    <script src="/js/modules/tracker-form.js"></script>
</body>
</html>
