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
      <a href="/info/mood-tracker" class="info-button-stats" title="Info Mood Tracker">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="pointer-events: none;">
          <circle cx="12" cy="12" r="10" stroke="#70c0bb" stroke-width="1.5"></circle>
          <path d="M12 17V11" stroke="#70c0bb" stroke-width="1.5" stroke-linecap="round"></path>
          <circle cx="11" cy="9" r="1" fill="#70c0bb"></circle>
        </svg>
      </a>
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
    </div>
  </div>

  <!-- Chart.js with fallback CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Fallback if primary CDN fails
    if (typeof Chart === 'undefined') {
      document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js"><\/script>');
    }
  </script>

  <script>
    // Wait for both DOM and Chart.js to be ready
    function initializeChart() {
      if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        return;
      }

      const ctx = document.getElementById('myChart');
      if (!ctx) {
        console.error('Canvas element not found');
        return;
      }

      // Safely get data with fallbacks
      const chartData = {
        labels: @json($labels->toArray()),
        moodData: @json($moodData->toArray()),
        productivityData: @json($productivityData->toArray())
      };

      // Validate data
      if (!Array.isArray(chartData.labels) || !Array.isArray(chartData.moodData) || !Array.isArray(chartData.productivityData)) {
        console.error('Invalid chart data format');
        return;
      }

      try {
        ctx.height = 250;

        new Chart(ctx, {
          type: 'line',
          data: {
            labels: chartData.labels,
            datasets: [
              {
                label: 'Mood',
                data: chartData.moodData,
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
                data: chartData.productivityData,
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
            responsive: true,
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
      } catch (error) {
        console.error('Error initializing chart:', error);
      }
    }

    // Initialize chart when everything is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() {
        // Give Chart.js a moment to load if it's still loading
        setTimeout(initializeChart, 100);
      });
    } else {
      // DOM is already ready, but wait a bit for Chart.js
      setTimeout(initializeChart, 100);
    }
  </script>
</body>