<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curhatorium | Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
  <div class="container">
    <div class="illustration">
      <img src="/assets/logo.svg" alt="logo" />
    </div>
    <div class="login-box">
      <h1>Login</h1>
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="text" placeholder="Username/Email" required name="login" autofocus/>
        @error('login')
            <div>{{ $message }}</div>
        @enderror
        <input type="password" placeholder="Password" required name="password"/>
        @error('password')
            <div>{{ $message }}</div>
        @enderror
                <label>
            <input type="checkbox" name="remember">
            Ingat Saya
        </label>
        <div class="links">
          <a href="{{ route('register') }}">Signup</a>
          <a href="{{ route('password.request') }}">Lupa Password</a>
        </div>
        <button type="submit">Log In</button>
      </form>
    </div>
  </div>
</body>
</html>