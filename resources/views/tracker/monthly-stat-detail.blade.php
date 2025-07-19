<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Bulanan - Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tracker/result.css') }}">
    <style>
        .monthly-summary {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .summary-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .summary-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #222;
        }
        
        .summary-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .summary-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .metric-card {
            text-align: center;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #48A6A6;
            margin-bottom: 4px;
        }
        
        .metric-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .daily-entries {
            margin-top: 32px;
        }
        
        .entries-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: #222;
        }
        
        .entry-item {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .entry-item:hover {
            transform: translateY(-2px);
        }
        
        .entry-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .entry-date {
            font-weight: 500;
            color: #222;
        }
        
        .entry-mood {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .entry-activity {
            color: #666;
            font-size: 0.9rem;
        }
        
        .empty-entries {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .empty-entries-icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }
        
        .monthly-chart {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .chart-header {
            margin-bottom: 20px;
        }
        
        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 4px;
        }
        
        .chart-subtitle {
            color: #666;
            font-size: 0.9rem;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>Ringkasan Bulanan</h1>
                <p>Berikut ringkasan tracking mental kamu untuk bulan ini.</p>
                <div class="date-display">
                    {{ \Carbon\Carbon::parse($monthlyStat->month)->translatedFormat('F Y') }}
                </div>
            </div>
        </div>

        <!-- Monthly Summary Card -->
        <div class="monthly-summary">
            <div class="summary-header">
                <div>
                    <div class="summary-title">Ringkasan Bulanan</div>
                    <div class="summary-date">
                        {{ \Carbon\Carbon::parse($monthlyStat->month)->translatedFormat('F Y') }}
                    </div>
                </div>
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
                        $avgMood = round($monthlyStat->avg_mood);
                        $mood = $moods[$avgMood] ?? ['emoji' => '😐', 'label' => 'Netral'];
                    @endphp
                    {{ $mood['emoji'] }}
                </div>
            </div>

            <div class="summary-metrics">
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($monthlyStat->avg_mood, 1) }}</div>
                    <div class="metric-label">Rata-rata Mood</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($monthlyStat->avg_productivity, 1) }}</div>
                    <div class="metric-label">Rata-rata Produktivitas</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $monthlyStat->total_entries }}</div>
                    <div class="metric-label">Total Entri</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($monthlyStat->best_mood, 1) }}</div>
                    <div class="metric-label">Mood Terbaik</div>
                </div>
            </div>
        </div>

        <!-- Monthly Chart Section -->
        <div class="monthly-chart">
            <div class="chart-header">
                <h2 class="chart-title">Grafik Bulanan</h2>
                <p class="chart-subtitle">Perkembangan mood dan produktivitas selama bulan ini</p>
            </div>
            <div class="chart-container">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- AI Feedback Section -->
        @if($monthlyStat->feedback)
        <div class="ai-analysis">
            <div class="ai-header">
                <div class="ai-icon">🤖</div>
                <div>
                    <h2 class="ai-title">Umpan Balik & Insight AI Bulanan</h2>
                    <p class="ai-subtitle">Umpan balik personal dari Ment-AI untuk bulan ini</p>
                </div>
            </div>
            <div class="ai-content">
                <div class="ai-section-content">
                    @php
                        // Konversi Markdown sederhana ke HTML aman (tanpa tag HTML)
                        $text = $monthlyStat->feedback ?? '';
                        // Escape all HTML
                        $text = e($text);

                        // Bold: **text** or __text__
                        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
                        $text = preg_replace('/\_\_(.*?)\_\_/s', '<strong>$1</strong>', $text);

                        // Italic: *text* or _text_
                        $text = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $text);
                        $text = preg_replace('/\_(.*?)\_/s', '<em>$1</em>', $text);

                        // Inline code: `code`
                        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

                        // Headings: #, ##, ###
                        $text = preg_replace('/^### (.*)$/m', '<b>$1</b>', $text);
                        $text = preg_replace('/^## (.*)$/m', '<b>$1</b>', $text);
                        $text = preg_replace('/^# (.*)$/m', '<b>$1</b>', $text);

                        // Unordered lists: - item or * item
                        $text = preg_replace('/^(\s*)[-\*] (.*)$/m', '$1• $2', $text);

                        // Numbered lists: 1. item
                        $text = preg_replace('/^(\s*)\d+\.\s(.*)$/m', '$1$2', $text);

                        // Convert newlines to <br>
                        $text = nl2br($text);
                    @endphp
                    {!! $text !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Daily Entries Section -->
        <div class="daily-entries">
            <div class="entries-title">Entri Harian Bulan Ini</div>
            
            @if($stats->count() > 0)
                @foreach($stats as $stat)
                    <div class="entry-item" onclick="window.location.href = '{{ route('tracker.stat.detail', $stat->id) }}'">
                        <div class="entry-header">
                            <div class="entry-date">
                                {{ \Carbon\Carbon::parse($stat->created_at)->translatedFormat('l, j F Y') }}
                            </div>
                            <div class="entry-mood">
                                @php
                                    $dayMood = $moods[$stat->mood] ?? ['emoji' => '😐', 'label' => 'Netral'];
                                @endphp
                                <span class="mood-emoji">{{ $dayMood['emoji'] }}</span>
                                <span class="mood-score">{{ $stat->mood }}/10</span>
                            </div>
                        </div>
                        <div class="entry-activity">
                            @php
                                $activities = [
                                    'work' => '💼 Pekerjaan & Karir',
                                    'exercise' => '🏃‍♂️ Aktivitas Fisik',
                                    'social' => '💬 Sosialisasi',
                                    'hobbies' => '🎨 Kreativitas & Hobi',
                                    'rest' => '🎧 Hiburan & Santai',
                                    'entertainment' => '🛁 Perawatan Diri',
                                    'nature' => '🌳 Aktivitas Luar Ruangan',
                                    'food' => '🏠 Rumah Tangga',
                                    'health' => '🧘 Kesehatan Mental',
                                    'study' => '📖 Belajar & Produktivitas',
                                    'spiritual' => '🙏 Spiritual',
                                    'romance' => '💖 Hubungan Romantis',
                                    'finance' => '📊 Finansial & Mandiri',
                                    'other' => '🧩 Lainnya',
                                ];
                                $activityName = $activities[$stat->activity] ?? $stat->activity;
                            @endphp
                            {{ $activityName }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-entries">
                    <div class="empty-entries-icon">📝</div>
                    <h3>Tidak Ada Entri</h3>
                    <p>Belum ada catatan harian untuk bulan ini.</p>
                </div>
            @endif
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Prepare chart data
        @php
            $chartLabels = [];
            $moodData = [];
            $productivityData = [];
            
            foreach($stats as $stat) {
                $chartLabels[] = \Carbon\Carbon::parse($stat->created_at)->format('j M');
                $moodData[] = $stat->mood;
                $productivityData[] = $stat->productivity;
            }
        @endphp

        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('monthlyChart');
            ctx.height = 250;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Mood',
                            data: {!! json_encode($moodData) !!},
                            borderWidth: 2,
                            tension: 0.3,
                            borderColor: '#48A6A6',
                            backgroundColor: 'rgba(54,162,235,0.08)',
                            pointBackgroundColor: '#48A6A6',
                            pointRadius: 4,
                            fill: true,
                        },
                        {
                            label: 'Productivity',
                            data: {!! json_encode($productivityData) !!},
                            borderWidth: 2,
                            tension: 0.3,
                            borderColor: '#FFCD2D',
                            backgroundColor: 'rgba(253,215,91,0.08)',
                            pointBackgroundColor: '#FFCD2D',
                            pointRadius: 4,
                            fill: true,
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#222',
                                font: {
                                    size: 14,
                                    family: "'Inter', 'Arial', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#fff',
                            titleColor: '#222',
                            bodyColor: '#222',
                            borderColor: '#e9e7e4',
                            borderWidth: 1,
                            padding: 12,
                            caretSize: 6,
                        }
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 10,
                            bottom: 10
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#888',
                                font: {
                                    size: 13,
                                    family: "'Inter', 'Arial', sans-serif"
                                }
                            }
                        },
                        y: {
                            min: 0,
                            max: 10,
                            grid: {
                                color: '#e9e7e4'
                            },
                            ticks: {
                                color: '#888',
                                font: {
                                    size: 13,
                                    family: "'Inter', 'Arial', sans-serif"
                                },
                                stepSize: 2
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html> 