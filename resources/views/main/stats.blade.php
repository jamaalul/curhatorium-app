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
    <div class="top-box" style="position: relative;">
      <canvas id="myChart"></canvas>
      <a href="/info/mood-tracker" class="info-button-stats" title="Info Mood Tracker" style="position: absolute; top: 12px; right: 12px; z-index: 10; color: #70c0bb !important; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#70c0bb" class="bi bi-info-circle" viewBox="0 0 16 16" style="color: #70c0bb;">
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
          <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
        </svg>
      </a>
    </div>

    <div class="bottom-boxes">
      <div class="box mental">
        <div>Rata-rata Mood</div>
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

  <script src="/js/modules/dashboard-chart.js"></script>
</body>