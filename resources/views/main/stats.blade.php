<link rel="stylesheet" href="{{ asset('css/main/stats.css') }}">

<body>
  <div class="container">
    <div class="top-box">
      <canvas id="myChart"></canvas>
    </div>

    <div class="bottom-boxes">
      <div class="box mental">
        <div>Rata-rata Kesehatan Mental</div>
        <div class="score mood">86.34</div>
        <div class="status">Baik</div>
      </div>

      <div class="box productivity">
        <div>Rata-rata Produktivitas</div>
        <div class="score prod">87.68</div>
        <div class="status">Baik</div>
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
    // Chart data (should be defined in PHP for consistency)
    @php
      $stats = [
        ['day' => 'Mon', 'value' => 7.5, 'productivity' => 8.1],
        ['day' => 'Tue', 'value' => 8.2, 'productivity' => 8.5],
        ['day' => 'Wed', 'value' => 6.9, 'productivity' => 7.2],
        ['day' => 'Thu', 'value' => 7.8, 'productivity' => 8.0],
        ['day' => 'Fri', 'value' => 8.0, 'productivity' => 9.0],
        ['day' => 'Sat', 'value' => 9.1, 'productivity' => 9.3],
        ['day' => 'Sun', 'value' => 8.7, 'productivity' => 8.9],
      ];

      $labels = collect($stats)->pluck('day');
      $moodData = collect($stats)->pluck('value');
      $productivityData = collect($stats)->pluck('productivity');

      // Correct average mood calculation
      $averageMood = number_format($moodData->avg(), 2);
      $averageProd = number_format($productivityData->avg(), 2);
    @endphp
    document.querySelector('.mood').innerHTML = "{{ $averageMood }}";
    document.querySelector('.prod').innerHTML = "{{ $averageProd }}";

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