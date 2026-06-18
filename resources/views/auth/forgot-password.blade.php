<x-layout title="Curhatorium | Lupa Password">
    <x-slot:head>
        <link rel="stylesheet" href="{{ asset('css/forgot-password.css') }}">
    </x-slot:head>

    <div class="icon">
      <!-- Replace with your icon image if needed -->
      <img src="/assets/logo.svg" alt="Hand Icon">
    </div>
  <div class="container">
    <div class="text">
      Lupa password? Jangan khawatir. Cukup beri tahu kami alamat emailmu dan kami akan mengirimkan tautan untuk mengatur ulang password, sehingga Anda dapat memilih kata sandi baru.
    </div>
    <form>
      <input type="email" placeholder="Email" required>
      <button type="submit">Ajukan Perubahan Password</button>
    </form>
  </div>
</x-layout>