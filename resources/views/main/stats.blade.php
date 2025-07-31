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
        ℹ️
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

  <!-- Chart data for JavaScript -->
  <script>
    window.chartData = {
      labels: @json($labels->toArray()),
      moodData: @json($moodData->toArray()),
      productivityData: @json($productivityData->toArray())
    };
  </script>

  <!-- Load Chart.js with multiple fallbacks -->
  <script>
    function loadChartJS() {
      return new Promise((resolve, reject) => {
        // Try multiple CDNs
        const cdnUrls = [
          'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js',
          'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js',
          'https://unpkg.com/chart.js@4.4.0/dist/chart.umd.js'
        ];
        
        let currentIndex = 0;
        
        function tryLoadCDN() {
          if (currentIndex >= cdnUrls.length) {
            reject(new Error('All CDNs failed'));
            return;
          }
          
          const script = document.createElement('script');
          script.src = cdnUrls[currentIndex];
          script.onload = () => {
            console.log('Chart.js loaded from:', cdnUrls[currentIndex]);
            resolve();
          };
          script.onerror = () => {
            console.warn('Failed to load from:', cdnUrls[currentIndex]);
            currentIndex++;
            tryLoadCDN();
          };
          document.head.appendChild(script);
        }
        
        tryLoadCDN();
      });
    }
    
    function initializeChart() {
      const ctx = document.getElementById('myChart');
      if (!ctx) {
        console.error('Canvas element not found');
        return;
      }
      
      if (!window.chartData) {
        console.error('Chart data not available');
        return;
      }
      
      const { labels, moodData, productivityData } = window.chartData;
      
      // Validate data
      if (!Array.isArray(labels) || !Array.isArray(moodData) || !Array.isArray(productivityData)) {
        console.error('Invalid chart data format:', { labels, moodData, productivityData });
        return;
      }
      
      // Debug: Log the actual data
      console.log('Chart data received:', {
        labels: labels,
        moodData: moodData,
        productivityData: productivityData,
        labelsLength: labels.length,
        moodDataLength: moodData.length,
        productivityDataLength: productivityData.length
      });
      
      try {
        ctx.height = 250;
        
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [
              {
                label: 'Mood',
                data: moodData,
                borderWidth: 2,
                tension: 0.3,
                borderColor: '#48A6A6',
                backgroundColor: 'rgba(72, 166, 166, 0.1)',
                pointBackgroundColor: '#48A6A6',
                pointRadius: 4,
                fill: true,
              },
              {
                label: 'Productivity',
                data: productivityData,
                borderWidth: 2,
                tension: 0.3,
                borderColor: '#FFCD2D',
                backgroundColor: 'rgba(255, 205, 45, 0.1)',
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
        
        console.log('Chart initialized successfully');
      } catch (error) {
        console.error('Error initializing chart:', error);
      }
    }
    
    // Initialize when everything is ready
    document.addEventListener('DOMContentLoaded', function() {
      loadChartJS()
        .then(() => {
          // Give a small delay to ensure Chart.js is fully initialized
          setTimeout(initializeChart, 50);
        })
        .catch(error => {
          console.error('Failed to load Chart.js:', error);
        });
    });
  </script>
</body>