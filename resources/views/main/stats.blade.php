@php
  // Use real data from database
  $stats = $statsData['chartData'] ?? [];
  $labels = collect($stats)->pluck('day');
  $moodData = collect($stats)->pluck('value');
  $productivityData = collect($stats)->pluck('productivity');

  // Use calculated averages from database
  $averageMood = $statsData['averageMood'] ?? '0.00';
  $averageProd = $statsData['averageProd'] ?? '0.00';
@endphp

<link rel="stylesheet" href="{{ asset('css/main/stats.css') }}">

<body>
  <div class="container">
    <div class="top-box">
      <canvas id="myChart"></canvas>
    </div>

    <div class="bottom-boxes">
      <div class="box mental">
        <div>Rata-rata Kesehatan Mental</div>
        <div class="score mood">{{ $averageMood }}</div>
        <div class="status">
          @php
            $moodValue = floatval($averageMood);
            if ($moodValue >= 8) echo 'Sangat Baik';
            elseif ($moodValue >= 6) echo 'Baik';
            elseif ($moodValue >= 4) echo 'Cukup';
            else echo 'Perlu Perhatian';
          @endphp
        </div>
      </div>

      <div class="box productivity">
        <div>Rata-rata Produktivitas</div>
        <div class="score prod">{{ $averageProd }}</div>
        <div class="status">
          @php
            $prodValue = floatval($averageProd);
            if ($prodValue >= 8) echo 'Sangat Baik';
            elseif ($prodValue >= 6) echo 'Baik';
            elseif ($prodValue >= 4) echo 'Cukup';
            else echo 'Perlu Perhatian';
          @endphp
        </div>
      </div>

      <div class="button-box">
        <div class="button-yellow" onclick="window.location.href = '/tracker'">
            Ceritakan Kondisimu Hari Ini →
        </div>
        <div class="button-black" onclick="window.location.href = '/tracker/history'">
            Lihat Laporan Sebelumnya →
        </div>
      </div>

      {{-- <div class="button-yellow">
        <div>Ceritakan Kondisimu Hari Ini →</div>
      </div> --}}

      {{-- <div class="button-black">
        <div>Lihat Laporan Sebelumnya →</div>
      </div> --}}
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>

    document.addEventListener('DOMContentLoaded', function () {
      const ctx = document.getElementById('myChart');
      ctx.height = 250;

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: {!! $labels->toJson() !!},
          datasets: [
            {
              label: 'Mood',
              data: {!! $moodData->toJson() !!},
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
              data: {!! $productivityData->toJson() !!},
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