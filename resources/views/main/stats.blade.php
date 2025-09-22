@php
  $stats = $statsData['chartData'] ?? [];
  $labels = collect($stats)->pluck('day');
  $moodData = collect($stats)->pluck('value');
  $productivityData = collect($stats)->pluck('productivity');
  $averageMood = $statsData['averageMood'] ?? '0.00';
  $averageProd = $statsData['averageProd'] ?? '0.00';
@endphp

<section class="bg-stone-200 h-fit p-4 w-full">
  <div class="w-full">
    <div class="relative bg-white rounded-lg shadow-md p-2 mb-4 w-auto">
      <canvas id="myChart"></canvas>
      <a href="/info/mood-tracker" class="absolute top-3 right-3 z-10 text-teal-500 hover:text-teal-700" title="Info Mood Tracker">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#70c0bb" class="bi bi-info-circle" viewBox="0 0 16 16">
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
          <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
        </svg>
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center">
        <div class="text-gray-700 font-semibold mb-2">Rata-rata Mood</div>
        <div class="score mood text-3xl font-bold text-teal-500 mb-2">{{ $averageMood }}</div>
        <div class="status text-sm text-gray-500">
          @php
            $moodValue = floatval($averageMood);
            if ($moodValue >= 8) echo 'Sangat Baik';
            elseif ($moodValue >= 6) echo 'Baik';
            elseif ($moodValue >= 4) echo 'Cukup';
            else echo 'Perlu Perhatian';
          @endphp
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center">
        <div class="text-stone-700 font-semibold mb-2">Rata-rata Produktivitas</div>
        <div class="score prod text-3xl font-bold text-yellow-400 mb-2">{{ $averageProd }}</div>
        <div class="status text-sm text-stone-500">
          @php
            $prodValue = floatval($averageProd);
            if ($prodValue >= 8) echo 'Sangat Baik';
            elseif ($prodValue >= 6) echo 'Baik';
            elseif ($prodValue >= 4) echo 'Cukup';
            else echo 'Perlu Perhatian';
          @endphp
        </div>
      </div>
    </div>
  </div>
  <div class="flex flex-col md:flex-row gap-4 justify-center w-full mt-4">
    <button class="bg-black hover:bg-gray-800 text-white font-semibold py-3 px-6 rounded-lg transition w-full shadow-md flex flex-row items-center justify-center gap-2" onclick="window.location.href = '/tracker/history'">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
      </svg>
      Lihat Laporan Sebelumnya
    </button>
    <button class="bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-3 px-6 rounded-lg transition w-full shadow-md flex flex-row items-center justify-center gap-2" onclick="window.location.href = '/tracker'">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
      </svg>
      Ceritakan Kondisimu Hari Ini
    </button>
  </div>

  <script>
    window.chartData = {
      labels: @json($labels->toArray()),
      moodData: @json($moodData->toArray()),
      productivityData: @json($productivityData->toArray())
    };
  </script>
  <script src="/js/modules/dashboard-chart.js"></script>
</section>
