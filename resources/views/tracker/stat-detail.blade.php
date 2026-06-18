@extends('layouts.app')

@section('title', 'Detail Catatan Mood - Curhatorium')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tracker/result.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
@endsection

@section('content')
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>Detail Catatan Mood</h1>
                <p>Berikut detail tracking mental kamu pada tanggal ini.</p>
                <div class="date-display">{{ \Carbon\Carbon::parse($stat->created_at)->translatedFormat('l, j F Y') }}</div>
            </div>
        </div>

        <!-- Entry Card -->
        <div class="entry-card">
            <div class="entry-header">
                <div class="mood-display">
                    @php
                        $moods = [
                            1 => ['emoji' => '😢', 'label' => 'Sangat sedih'],
                            2 => ['emoji' => '😞', 'label' => 'Sedih'],
                            3 => ['emoji' => '😔', 'label' => 'Murung'],
                            4 => ['emoji' => '😐', 'label' => 'Biasa'],
                            5 => ['emoji' => '🙂', 'label' => 'Netral'],
                            6 => ['emoji' => '😊', 'label' => 'Positif'],
                            7 => ['emoji' => '😄', 'label' => 'Senang'],
                            8 => ['emoji' => '😁', 'label' => 'Sangat senang'],
                            9 => ['emoji' => '🤩', 'label' => 'Bahagia'],
                            10 => ['emoji' => '🥳', 'label' => 'Gembira'],
                        ];
                        $mood = $moods[$stat->mood] ?? ['emoji' => '', 'label' => ''];
                    @endphp
                    {{ $mood['emoji'] }}
                </div>
                <div class="mood-score">{{ $stat->mood }}/10</div>
                <div class="mood-label">{{ $mood['label'] }}</div>
            </div>

            <div class="entry-details">
                <div class="detail-section">
                    <div class="detail-title">
                        🎯 Aktivitas Utama
                    </div>
                    <div class="activity-display">
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
                            $activity = $activities[$stat->activity] ?? ['icon' => '', 'name' => $stat->activity];
                        @endphp
                        <div class="activity-icon">
                            {{ $activity['icon'] }}
                        </div>
                        <div class="activity-name">
                            {{ $activity['name'] }}
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-title">
                        ⚡ Energi & Produktivitas
                    </div>
                    <div class="energy-metrics">
                        <div class="metric-item">
                            <div class="metric-icon">⚡</div>
                            <div class="metric-value">{{ $stat->energy }}</div>
                            <div class="metric-label">Energi</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-icon">🎯</div>
                            <div class="metric-value">{{ $stat->productivity }}</div>
                            <div class="metric-label">Produktivitas</div>
                        </div>
                    </div>
                </div>

                @if($stat->explanation)
                <div class="explanation-section">
                    <div class="detail-title">
                        💭 Pikiranmu
                    </div>
                    <div class="explanation-text">
                        "{{ $stat->explanation }}"
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- AI Feedback Section -->
        <div class="ai-analysis">
            <div class="ai-header">
                <div class="ai-icon">🤖</div>
                <div>
                    <h2 class="ai-title">Umpan Balik & Insight AI</h2>
                    <p class="ai-subtitle">Umpan balik personal dari Ment-AI</p>
                </div>
            </div>
            <div class="ai-content">
                @if($stat->feedback)
                    <div class="ai-section-content prose prose-lg" style="max-width: none;">
                        {!! Illuminate\Support\Str::markdown($stat->feedback) !!}
                    </div>
                @else
                    <div class="ai-section-content text-tertiary font-italic">Belum ada umpan balik AI untuk entri ini.</div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('tracker.history') }}" class="action-btn btn-secondary">
                Kembali ke Riwayat
            </a>
            <a href="{{ route('dashboard') }}" class="action-btn btn-primary">
                Dashboard
            </a>
        </div>
    </div>
@endsection 