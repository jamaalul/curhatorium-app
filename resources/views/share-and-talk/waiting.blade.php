<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Menunggu | Curhatorium</title>
  @vite('resources/css/app.css')

</head>
<body class="bg-teal-50 min-h-screen flex items-center justify-center p-4">
  <main class="text-center">
    <div class="relative inline-flex items-center justify-center" aria-live="polite">
      <!-- ripple (fast) -->
      <div class="absolute w-36 h-36 rounded-full bg-[#48A6A6]/40 blur-xl z-10 animate-ripple"></div>

      <!-- ripple (slower, delayed) -->
      <div
        class="absolute w-36 h-36 rounded-full bg-[#48A6A6]/30 blur-2xl z-10 animate-ripple"
        style="animation-duration:2.2s; animation-delay:0.5s;"
      ></div>

      <!-- soft breathing halo behind everything -->
      <div class="absolute w-40 h-40 rounded-full bg-[#48A6A6]/20 z-0 animate-ping"></div>

      <!-- base circle (top) -->
      <div class="relative z-20 flex items-center justify-center w-36 h-36 rounded-full bg-[#48A6A6] text-white font-semibold text-lg shadow-2xl ring-4 ring-teal-300/20">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-20">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
      </div>
    </div>

    <h1 class="mt-8 text-2xl font-bold text-gray-700">Menunggu respon...</h1>
    <p class="mt-2 text-gray-500 max-w-lg mx-auto">Harap tunggu sebentar. Ranger kamu lagi dihubungi.</p>
  </main>
</body>
</html>
