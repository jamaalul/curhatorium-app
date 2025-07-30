<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curhatorium | Signup</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
    <div class="container">
    <div class="illustration">
      <img src="/assets/logo.svg" alt="logo" />
    </div>
    <div class="signup-box">
      <h1>Signup</h1>
      <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="text" placeholder="Username" required name="username" autofocus/>
        @error('username')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <input type="text" placeholder="Email" required name="email" autofocus/>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <input type="password" placeholder="Password" required name="password"/>
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required/>

        <label>
            <input type="checkbox" name="terms" required>
            Setuju dengan <a href="/terms-and-conditions" target="_blank">Syarat dan Ketentuan</a>
        </label>
        @error('terms') <div>{{ $message }}</div> @enderror
        <div class="links">
          <a href="/login">Sudah Punya Akun</a>
        </div>
        <button type="submit">Sign Up</button>
      </form>
    </div>
  </div>
</body>
</html>